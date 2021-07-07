<?php
/**
 * Class TransformMaster
 * Takes a string and blows it up to many strings transformed from the original string
 */
abstract class TransformMaster
{
    protected $localProbability;

    public function __construct($localProbability = null)
    {
        if($localProbability === null) {
            $localProbability = StatsMinder::TRANSFORM_MIN_LOCAL_PROB;
        }

        $this->localProbability = $localProbability;
    }

    public function getLocalProbability()
    {
        return $this->localProbability;
    }

    /**
     * @param string $stem
     * @param float $prob
     * @return array (new_stem, new_probability)
     */
    public function transformStem($stem, $prob)
    {
        return [$this->doTransform($stem), $prob * $this->localProbability];
    }

    /**
     * @param $stem
     * @return array|string
     */
    protected abstract function doTransform($stem);
}
/*
abstract class TransformMaster
{
    //todo There should be a way to  combine transforms
    public function explodeStems($stems, $types)
    {
        $singleArray = [];
        if(is_array($stems)) {
            foreach($stems as $stem) {
                $singleArray = array_merge($singleArray, $this->explodeStem($stem, $types));
            }
            return $singleArray;
        } else {
            return $this->explodeStem($stems, $types);
        }
    }
    private function explodeStem($stem, $types)
    {
        if(!is_array($stem)) {
            $array = [$stem];
        } else {
            $array = $stem;
        }

        if(($types & StemMaster::TYPE_BASIC) > 0) {
            $explodedStems = $this->explodeStemBasic($stem);
            if(!empty($explodedStems)) {
                $array = array_merge($array, $explodedStems);
            }
        }

        if(($types & StemMaster::TYPE_EXTRA) > 0) {
            $explodedStems = $this->explodeStemExtra($stem);
            if(!empty($explodedStems)) {
                $array = array_merge($array, $explodedStems);
            }
        }

        if(($types & StemMaster::TYPE_DEEP) > 0) {
            $explodedStems = $this->explodeStemDeep($stem);
            if(!empty($explodedStems)) {
                $array = array_merge($array, $explodedStems);
            }
        }

        return $array;
    }
    public abstract function explodeStemBasic($stem);
    public abstract function explodeStemExtra($stem);
    public abstract function explodeStemDeep($stem);

    protected function add(&$array, $newStem, $globalProbability, $localProbability)
    {
        $prob = $globalProbability * $localProbability;

        $array[$newStem] = $prob;
    }
}
*/