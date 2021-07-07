<?php

/**
 * Class SuffixMaster
 *
 * getxxxxxSuffixes():
 *   Must return an array in the following format:
 *     {
 *        'suffix' => probability
 *     }
 *     Keys of the array must be a string
 *     Value is optional if you're unsure of the probability
 *     Probability (if provided) must be the LOCAL probability for that suffix
 */
abstract class SuffixMaster
{
    private $suffixes = array();

    public function clearSuffixes()
    {
        $this->suffixes = array();
    }
    public function add($key, $prob = null)
    {
        $key = (string) $key;
        if($prob === null) {
            $prob = StatsMinder::SUFFIX_MIN_LOCAL_PROB;
        }

        if(!array_key_exists($key, $this->suffixes)) {
            $this->suffixes[$key] = $prob;
        } else {
            $this->suffixes[$key] += $prob;
        }
    }

    public function __construct() {}

    protected abstract function generateSuffixes();

    public function getSuffixes()
    {
        $this->clearSuffixes();
        $this->generateSuffixes();
        return $this->suffixes;
    }
}