<?php


class Combination
{
    private $combination;

    public function __construct($combination)
    {
        $this->combination  = $combination;
    }

    public function extractFromAssociative($asscArray)
    {
        $i = 0;
        $array = array();

        foreach($asscArray as $key=>$val) {
            $array[$key] = $val[$this->combination[$i]];

            $i++;
        }

        return $array;
    }

    public function extract($array)
    {
        $newArray = array();
        for($i = 0;$i <= count($this->combination);$i++) {
            $newArray[$i] = $array[$i][$this->combination[$i]];
        }

        return $newArray;
    }
}