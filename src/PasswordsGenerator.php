<?php


class PasswordsGenerator
{
    /**
     * Takes a list of instances of StemMaster and extracts the stems as strings into an array.
     * Return syntax:
     * {
     *   'class_name' => [ 'stem1', 'stem2', 'etc' ]
     * }
     *
     * @param StemMaster[] $stemClasses
     *
     * @return array
     */
    public static function extractRawStemStrings(
        $stemClasses
    )
    {
        $insertedStems = [];//To keep track of what has been inserted so far. Only the keys here.
        $stems = array();

        //Pull stems from stem classes
        /** @var StemMaster $curClass */
        foreach($stemClasses as $curClass) {
            $curStems = $curClass->getStems();
            if(!empty($curStems)) {
                foreach($curStems as $curString => $curProb) {
                    if(!in_array($curString, $insertedStems)) {
                        array_push($stems, [$curString, $curProb]);
                        array_push($insertedStems, $curString);
                    }
                }
            }
        }

        return $stems;
    }

    /**
     * Extracts suffix strings from an array of SuffixMasters.
     *
     * Returns in the following format:

     * {
     *   'class_name' => [ 'suffix1', 'suffix2', 'etc' ]
     * }
     *
     * @param SuffixMaster[] $suffixClasses
     * @return array
     */
    public static function extractSuffixStrings(
        $suffixClasses
    )
    {
        $insertedSuffixes = [];//To keep track of what has been inserted so far. Only the keys here.
        $suffixes = array();

        //Load suffixes
        /** @var SuffixMaster $curClass */
        foreach($suffixClasses as $curClass) {
            $curSuffixes = $curClass->getSuffixes();
            if(!empty($curSuffixes)) {
                foreach($curSuffixes as $curString => $curProb) {
                    if(!in_array($curString, $insertedSuffixes, true)) {
                        array_push($suffixes, [$curString, $curProb]);
                        array_push($insertedSuffixes, $curString);
                    } else {
                        echo $curString.' is in';
                    }
                }
            }
        }

        return $suffixes;
    }

    /**
     * @param array $stemStrings
     * @param array $suffixStrings
     * @param TransformMaster[] $transforms
     * @param null|int $topN
     * @param bool $getCount
     * @param null|float $minProb
     * @param bool $requireSymbols
     * @param bool $requireNumbers
     * @param bool $requireUppercase
     * @param null|int $minLength
     * @param array $filterAllCharacters
     * @return PasswordList
     */
    public static function generatePasswords(
        $stemStrings,
        $suffixStrings = array(),
        $transforms = array(),
        $topN = null,
        $getCount = false,//If true, won't generate, only show combinations,
        $minProb = null,
        $requireSymbols = false,
        $requireNumbers = false,
        $requireUppercase = false,
        $minLength = null,
        $filterAllCharacters = []
    )
    {
        $passwordList = new PasswordList([], $topN);
        //Add null to beginnings to for the combinations when the suffix and/or transform is not present
        $suffixStrings = array_merge([null], $suffixStrings);
        $transforms = array_merge([null], $transforms);
        //Get Combinations
        $combinationArray = [
            $suffixStrings,
            $transforms
        ];
        $combinationIterator = new CombinationIterator($combinationArray);
        $combinations = $combinationIterator->getEveryPossibility();
        verbose(
            number_format(count($combinations))
            . " combinations per stem found", 2, Program::$verbose);
        $totalPasswordCount = count($combinations) * count($stemStrings);
        verbose(
            number_format($totalPasswordCount)
            . " passwords will be generated", 1, Program::$verbose
        );

        if($getCount) {
            println(
                "With the information provided, up to ".number_format($totalPasswordCount)
                . " passwords can be generated"
            );
            die();
        }

        //Explode stems
        $i = 0;
        foreach($stemStrings as $curStem) {
            foreach($combinations as $curCombination) {
                $password = self::getPassword(
                    $curStem,
                    $suffixStrings[$curCombination[0]],
                    $transforms[$curCombination[1]]
                );

                //Is password valid
                if($password === false) {
                    continue;
                }

                //Does password meet minimum probability
                if(
                    $minProb !== null
                    && $password[1] < $minProb
                ) {
                    continue;
                }

                //Does password have symbols
                if(
                    $requireSymbols
                    && preg_match(
                        "/[^A-Za-z0-9]+/",
                        $password[0]
                    ) !== 1
                ) {
                    continue;
                }

                //Does password have numbers
                if(
                    $requireNumbers
                    && preg_match(
                        "/[0-9]/",
                        $password[0]
                    ) === 0
                ) {
                    continue;
                }

                //Does password meet minimum length
                if(
                    $minLength !== null
                    && strlen($password[0]) < $minLength
                ) {
                    continue;
                }

                //Does password have uppercase characters
                if(
                    $requireUppercase
                    && preg_match("/[A-Z]/", $password[0]) !== 1
                ) {
                    continue;
                }

                //Does password have all required characters
                if(!empty($filterAllCharacters)) {
                    $dame = false;
                    foreach($filterAllCharacters as $char) {
                        if(strpos($password[0], $char) === false) {
                            $dame = true;
                            break;
                        }
                    }

                    if($dame) {
                        continue;
                    }
                }

                $passwordList->addPassword($password[0], $password[1]);
                $i++;
            }
        }
        verbose('Finished generating '.$i.' passwords', 1, Program::$verbose);

        //Sort passwords
        $passwordList->sortByProbDescending();

        return $passwordList;
    }

    /**
     * @param array $stemString
     * @param null|array $suffixString
     * @param null|TransformMaster $transform
     *
     * @return array|false
     */
    private static function getPassword(
        $stemString,
        $suffixString = null,
        $transform = null
    )
    {
        $prob = StatsMinder::getPasswordProbability(
            $stemString[1],
            (($suffixString) ? $suffixString[1] : null),
            (($transform) ? $transform->getLocalProbability() : null)
        );

        if($transform !== null) {
            $oldPassword = $stemString[0];
            $password = $transform->transformStem($stemString[0], $stemString[1])[0];
            if($oldPassword === $password) {
                //If the password didn't change after transform, then this password
                //By this combination needs to be ignored.
                return false;
            }
        } else {
            $password = $stemString[0];
        }

        if($suffixString !== null) {
            $password .= $suffixString[0];
        }

        Program::$passwordsGenerated++;
        return [$password, $prob];
    }
}