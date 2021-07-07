<?php
function println($a = null) {
    echo $a."\n";
}
function err($message, $printHelp = false, $exitCode = 1) {
    println("ERROR: ".$message);
    if($printHelp) {
        print_help();
    }
    exit($exitCode);
}
function warning($message)
{
    println("WARNING: ".$message);
}
function verbose($message, $required, $actual)
{
    if($actual >= $required) {
        println("[VERBOSE ".$required."] " . $message);
    }
}
function getUnusedFilename($basePath)
{
    $basePath = $basePath.'_';
    $i = 1;

    do {
        $path = $basePath.$i;
        $fileExists = file_exists($path);
        $i++;
    } while($fileExists);

    return $path;
}