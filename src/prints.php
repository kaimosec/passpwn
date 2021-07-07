<?php
function print_help() {
    println("usage: php passgen.php [options]");

    println();
    println("----- Basic Options:");
    println("--input            Get input from terminal instead of INI, and write to file");
    println("-v                 verbose; -vv or -vvv for increased verbosity");
    println("-o <file>          Output list filename. - for STDOUT (default: STDOUT)");
    println("--overwrite-file   If output file already exists, overwrite it");
    println("-q                 Quiet mode");
    println();

    println("----- Filtering Options:");
    println("-n <num>           Limit output to the best NUM passwords");
    println("-l <num>           Limit password list to the best NUM passwords");
    println("  -n Uses more RAM and but less CPU and is quicker. -l is the opposite.");
    println("  Recommended that you use -n if you have the spare RAM");
    println();
    println("-p <min_prob>      Only output passwords with higher probability than MIN_PROB");
    println("--require-symbols  Only output passwords with at least 1 symbol");
    println("--require-numbers  Only output passwords with at least one number");
    println("--require-uc       Only output passwords with at least on uppercase letter");
    println("--min-length <num> Only output passwords which have at least NUM characters");
    println("-c <characters>    Only output passwords with all of these CHARACTERS");
    println("  Case sensitive");

    println();
    println("----- Advanced Options:");
    println("-i <file>          Specify Generator INI file (default: generator.ini)");
    println("-m <dir>           Specify module directory (default: modules)");
    println("--print-prob       To print probability of success after respective passwords");
    println("--get-count        Only show number of passwords to be generated");
}