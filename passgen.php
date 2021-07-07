<?php
error_reporting(E_ALL & ~E_NOTICE);

require "src/functions.php";
require "src/prints.php";
require "src/StemMaster.php";
require "src/SuffixMaster.php";
require "src/PasswordsGenerator.php";
require "src/CombinationIterator.php";
require "src/Combination.php";
require "src/TransformMaster.php";
require "src/BlankStem.php";
require "src/StatsMinder.php";
require "src/PasswordList.php";

$program = new Program($argv);

/*
 * todo
 * argument for not using the config file
 */

class Program {
    public static $passwordsGenerated = 0;//Not necessarily passwords used at the end
    public static $verbose = 0;
    private $useConfigFile = true;
    private $iniGeneratorPath = 'generator.ini';
    private $iniGenerator;
    private $modules;
    private $baseModulePath = 'modules';
    private $outputFilename = null;
    private $outputHandle;
    private $loadedSuffixes = array();
    private $loadedTransformers = array();
    private $overwriteFile = false;
    private $minProb = null;//If not null, only output passwords with a higher probability
    private $quiet = false;
    private $printProbabilities = false;
    private $limitLines = false;
    private $getCount = false;//If true, don't generate passwords, only show possible combinations
    private $limitPasswords = null;
    private $requireSymbols = false;
    private $requireNumbers = false;
    //If array not empty, only output passwords with all characters in the array
    private $filterAllCharacters = array();
    private $minLength = null;
    private $requireUc = false;

    public function __construct($argv)
    {
        $startTime=microtime(true);
        //Parse arguments

        /*
         * The following commands will print help:
         * php passgen.php
         * php passgen.php -h
         * php passgen.php --help
         */
        if(
            count($argv) === 2
            && (
                $argv[1] === '-h'
                || $argv[1] === '--help'
            )
        ) {
            print_help();
            exit(0);
        }

        $opt = getopt(
            'vi:m:l:c:o:qp:n:',
            array(
                'require-symbols',
                'require_numbers',
                'min-length:',
                'overwrite-file',
                'require-uc',
                'print-prob',
                'input',
                'get-count'
            )
        );

        foreach($opt as $key=>$val) {
            switch ($key) {
                case 'v':
                    if(!is_array($val)) {
                        self::$verbose = 1;
                    } else {
                        self::$verbose = count($val);
                    }
                    break;
                case 'i':
                    $this->iniGeneratorPath = $val;
                    break;
                case 'm':
                    $this->baseModulePath = rtrim($val, '/');
                    break;
                case 'l':
                    $this->limitPasswords = (int)$val;
                    break;
                case 'c':
                    for($j = 0;$j < strlen($val);$j++) {
                        array_push(
                            $this->filterAllCharacters,
                            substr($val, $j, 1)
                        );
                    }
                    break;
                case 'o':
                    $this->outputFilename = $val;
                    break;
                case 'q':
                    $this->quiet = true;
                    break;
                case 'require-symbols':
                    $this->requireSymbols = true;
                    break;
                case 'require-numbers':
                    $this->requireNumbers = true;
                    break;
                case 'p':
                    $this->minProb = $val;
                    break;
                case 'min-length':
                    $this->minLength = $val;
                    break;
                case 'overwrite-file':
                    $this->overwriteFile = true;
                    break;
                case 'require-uc':
                    $this->requireUc = true;
                    break;
                case 'print-prob':
                    $this->printProbabilities = true;
                    break;
                case 'input':
                    $this->useConfigFile = false;
                    break;
                case 'n':
                    $this->limitLines = (int)$val;
                    break;
                case 'get-count':
                    $this->getCount = true;
                    break;
            }
        }

        if(
            !$this->useConfigFile
            && empty($this->outputFilename)
        ) {
            $this->outputFilename = 'passgen.list';
        }

        //Load output handle
        if(
            $this->outputFilename === '-'
            || $this->outputFilename === null
        ) {
            $this->outputHandle = fopen('php://stdout', 'w');
            if(!$this->outputHandle) {
                err("ERROR: Failed to get handle of STDOUT");
            }
        } else {
            if(is_file($this->outputFilename)) {
                if(!$this->overwriteFile) {
                    //File name already exists, make an alternative name
                    verbose("Output file already exists, finding another filename", 1, self::$verbose);
                    $this->outputFilename = getUnusedFilename($this->outputFilename);
                    warning('Output file already exists. Writing to ' . $this->outputFilename . ' instead');
                } else {
                    verbose("Overwriting output file: ".$this->outputFilename, 2, self::$verbose);
                }
            }

            $this->outputHandle = fopen($this->outputFilename, 'w');
            if(!$this->outputHandle) {
                err("ERROR: Failed to get handle on output file: ".$this->outputFilename);
            }
        }

        //Load modules
        verbose('Loading modules...', 2, self::$verbose);
        $this->loadModules($this->baseModulePath);

        if($this->useConfigFile) {
            //Parse config file and use that to load stems
            verbose('Parsing configuration file: '.$this->iniGeneratorPath, 2, self::$verbose);

            $this->iniGenerator = parse_ini_file($this->iniGeneratorPath, true);

            if($this->iniGenerator === false) {
                err('Failed to parse INI file \"'.$this->iniGeneratorPath.'\"');
            }

            //Load stem classes
            verbose('Loading stem classes..', 3, self::$verbose);
            $stemClasses = $this->loadStemClasses();

        } else {
            if(!$this->quiet) {
                println("Enter as much as you can about the target");
                println("To skip, press enter");
                println("You can enter multiple answers, separated between commas");
            }

            $stemClasses = array();
            foreach($this->modules as $className => $path) {
                array_push($stemClasses, new $className());
            }
            /**
             * @param StemMaster $a
             * @param StemMaster $b
             * @return int
             */
            function cmp($a, $b)
            {
                if($a->getProbability() === $b->getProbability()) {
                    return 0;
                }
                if($a->getProbability() >= $b->getProbability()) {
                    return -1;
                } else {
                    return 1;
                }
            }
            usort($stemClasses, 'cmp');

            $i = 0;
            /** @var StemMaster $curStemClass */
            foreach($stemClasses as $curStemClass) {
                $prompt = get_class($curStemClass);
                if(
                    !empty($curStemClass->getDescription())
                    && !$this->quiet
                ) {
                    $prompt .= ' ('.$curStemClass->getDescription().')';
                }
                $prompt .= ': ';
                $input = readline($prompt);
                if(empty($input)) {
                    unset($stemClasses[$i]);
                } else {
                    $curStemClass->setInput($input);
                }

                $i++;
            }
        }

        if(!$this->quiet) {
            println("Crunching..");
        }

        //Extract stem strings
        verbose('Extracting stem strings..', 3, self::$verbose);
        $stemStrings = PasswordsGenerator::extractRawStemStrings(
            $stemClasses
        );
        verbose(count($stemStrings) . " Plain stems extracted", 2, self::$verbose);

        //Extract Suffix Strings
        verbose('Extracting suffix strings..', 3, self::$verbose);
        $suffixStrings = PasswordsGenerator::extractSuffixStrings(
            $this->loadedSuffixes
        );
        verbose(count($suffixStrings) . " Suffixes extracted", 2, self::$verbose);

        //Generate password list
        $passwordList = PasswordsGenerator::generatePasswords(
            $stemStrings,
            $suffixStrings,
            $this->loadedTransformers,
            $this->limitPasswords,
            $this->getCount,
            $this->minProb,
            $this->requireSymbols,
            $this->requireNumbers,
            $this->requireUc,
            $this->minLength,
            $this->filterAllCharacters
        );

        verbose('Writing passwords..', 2, Program::$verbose);
        $i = 0;
        foreach($passwordList->getPasswords() as $password=>$probability) {
            if(
                $this->limitLines !== false
                && $i >= $this->limitLines
            ) {
                break;
            }
            $line = $password;
            if($this->printProbabilities) {
                $line .= ':'.$probability;
            }
            self::writeOut($this->outputHandle, $line);

            $i++;
        }





        //At end, report memory
        $mbPeak = number_format(round(memory_get_peak_usage() / 1024 / 1024));
        verbose(
            $mbPeak.' MB of memory used at peak',
            3,
            self::$verbose
        );
        //Report time
        $msElapsed = round((microtime(true) - $startTime) * 1000);
        $passwordsPerMs = self::$passwordsGenerated / $msElapsed;
        verbose(
            'Took '.number_format($msElapsed).'ms to generate '
            . number_format(self::$passwordsGenerated) . ' passwords '
            . 'with an efficiency of ' . number_format($passwordsPerMs, 2)
            . 'pw/ms',
            3,
            self::$verbose
        );

        if(!$this->quiet) {
            if(
                $this->outputFilename === '-'
                || empty($this->outputFilename)
            ) {
                $writtenTo = 'STDOUT';
            } else {
                $writtenTo = $this->outputFilename;
            }
            println(number_format(self::$passwordsGenerated).' passwords generated');
            println(number_format($i).' passwords written to '.$writtenTo);
            println("Baibai");
        }
    }

    private static function writeOut($handle, $line)
    {
        fwrite($handle, $line."\n");
    }

    private function loadModules($dir) {
        if(!is_dir($dir)) {
            err("Not a directory: ".$dir);
        }

        verbose('Looking for modules in: '.$dir, 3, self::$verbose);

        $modules = glob($dir.'/*', GLOB_ONLYDIR);
        foreach($modules as $dirname) {
            verbose("Loading module: ".$dirname, 2, self::$verbose);

            //Load stems
            $stemPath = $dirname.'/stems';
            if(!is_dir($stemPath)) {
                verbose("Module has no stems", 3, self::$verbose);
            } else {
                $this->loadStems($stemPath);
            }

            //Load suffixes
            $suffixPath = $dirname.'/suffixes';
            if(!is_dir($suffixPath)) {
                verbose("Module has no suffixes", 3, self::$verbose);
            } else {
                $this->loadSuffixes($suffixPath);
            }

            //Load Transformers
            $transformerPath = $dirname.'/transforms';
            if(!is_dir($transformerPath)) {
                verbose("Module has no Transforms", 3, self::$verbose);
            } else {
                $this->loadTransformers($transformerPath);
            }
        }
    }
    private function loadStems($dir) {
        //Load all .php files
        $stems = glob($dir.'/*.php');
        foreach($stems as $filename) {
            $baseNoExtension = basename($filename, '.php');

            if(array_key_exists($baseNoExtension, $stems)) {
                warning('Duplicate stem found, cannot load: '.$filename);
            } else {
                if(!is_file($filename)) {
                    warning("Stem isn't a file: ".$filename);
                } else {
                    $loadModule = include($filename);
                    if($loadModule === false) {
                        warning("Failed to load stem: ".$filename);
                    } else {
                        $this->modules[$baseNoExtension] = $filename;
                        verbose('Loaded stem: '.$filename, 3, self::$verbose);
                    }
                }
            }
        }
    }

    private function loadSuffixes($dir)
    {
        $files = glob($dir.'/*.php');

        foreach($files as $filename) {
            $baseName = basename($filename, '.php');

            if(is_dir($filename)) {
                warning("All directories within your module's suffixes directories will be ignored. Files only.");
                warning("Ignoring: ".$filename);
            } else {
                //Check if a class of the same name has already been loaded
                if(array_key_exists($baseName, $this->loadedSuffixes)) {
                    warning("Cannot load Suffixes ".$filename.". A suffix class of the same name has already been loaded");
                } else {
                    $include = include($filename);
                    if($include === false) {
                        warning("Failed to load suffixes: ".$filename);
                    } else {
                        $class = new $baseName();
                        array_push($this->loadedSuffixes, $class);
                        verbose("Loaded Suffixes ".$filename, 3, self::$verbose);
                    }
                }
            }
        }
    }

    private function loadTransformers($dir)
    {
        verbose('Loading transformers in '.$dir, 2, self::$verbose);
        $files = glob($dir.'/*.php');

        foreach($files as $file) {
            $basename = basename($file,'.php');

            $include = include($file);
            if($include === false) {
                warning("Failed to load transformer: ".$file);
            } else {
                array_push($this->loadedTransformers, new $basename());
                verbose("Loaded transformer: ".$file, 3, self::$verbose);
            }
        }
    }

    private function loadStemClasses()
    {
        verbose("Loading stems from INI ".$this->iniGeneratorPath, 2, self::$verbose);
        $stemClasses = array();
        foreach($this->modules as $className => $path) {
            foreach($this->iniGenerator as $sectionName => $sectionArray) {
                if (
                    array_key_exists($className, $sectionArray)
                    && !empty($sectionArray[$className])
                ) {
                    $curStemClass = new $className($sectionArray[$className]);
                    array_push($stemClasses, $curStemClass);
                }
            }
        }

        return $stemClasses;
    }
}