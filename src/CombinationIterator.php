<?php

/**
 * Class CombinationIterator
 * Given a 2-dimensional array, it will return an array of all possible combinations.
 *
 * For example, from the array [3, 2, 1], the following combinations will be returned.
 * [
 *   [0,0,0],
 *   [1,0,0],
 *   [2,0,0],
 *   [0,1,0],
 *   [1,1,0],
 *   [2,1,0]
 * ]
 */
class CombinationIterator
{
    private $combinationArray;
    private $indexes;
    private $combinations;
    private $totalAdds = 0;

    public function __construct($array)
    {
        $this->combinationArray = $this->standardizeArray($array);
        $this->combinations = array();

        for ($i = 0; $i < count($this->combinationArray); $i++) {
            $this->indexes[$i] = 0;
        }
        $this->add();
    }

    public function getEveryPossibility()
    {
        $finished = false;
        while(!$finished) {
            $adds = 0;
            $carry = false;

            for ($mark = 0; $mark < count($this->combinationArray); $mark++) {
                //carry
                if($carry) {
                    if($this->indexes[$mark] < $this->combinationArray[$mark] - 1) {
                        $this->indexes[$mark]++;
                        $carry = false;

                        //Reset all previous indexes to 0
                        for($i = $mark - 1;$i >= 0;$i--) {
                            $this->indexes[$i] = 0;
                        }

                        $this->add();
                        $adds = 0;
                        $mark = -1;
                        continue;
                    }
                }

                for($index = $this->indexes[$mark]+1; $index < $this->combinationArray[$mark]; $index++) {
                    $this->indexes[$mark] = $index;
                    $this->add();
                    $adds++;

                    $carry = true;
                }
            }

            if($adds === 0) {
                break;
            }
        }

        return $this->combinations;
    }

    private function add()
    {
        array_push($this->combinations, $this->indexes);
        //println(print_r($this->indexes, true));

        $this->totalAdds++;
        //println('total adds: ' . $this->totalAdds);
    }

    private function standardizeArray($array)
    {
        $array = array_values($array);

        foreach($array as $key=>$val) {
            if(is_string($val)) {
                $array[$key] = strlen($val);
            } else {
                $array[$key] = count($val);
            }
        }

        return $array;
    }
}