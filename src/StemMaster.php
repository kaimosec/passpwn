<?php


abstract class StemMaster
{
    const TYPE_BASIC    = 0b001;
    const TYPE_EXTRA    = 0b010;
    const TYPE_DEEP     = 0b100;
    const TYPE_ALL      = 0b111;

    /**
     * @var $probability
     * Chance for that type of password to be used. For example, 19% of password stems
     * are a person's first name so $probability would be 0.19.
     * This is used to order the password list by probability of a correct guess.
     * If you're not sure, the average probability is 0.0118 (1.18%) or refer to
     * calc.ods
     */
    private $probability;

    private $input;

    /**
     * @var array|null $combineWith
     * class_name => string
     *
     * Where %1 is this stem and %2 is the other
     */
    private $combineWith;

    private $description;

    public function __construct(
        $probability,
        $input = null,
        $description = null
    )
    {
        $this->probability  = $probability;
        if(!empty($input)) {
            $this->input = $this->processInputs($input);
        } else {
            $this->input = null;
        }
        $this->description = $description;
    }

    public function getProbability()
    {
        return $this->probability;
    }

    public function setInput($input)
    {
        $this->input = $this->processInputs($input);
    }

    public function getDescription()
    {
        return $this->description;
    }

    protected function processInputs($input)
    {
        //Lowercase
        $input = strtolower($input);

        //Remove spaces after delimiters if present
        $input = preg_replace("/(?<=,) /", '', $input);

        return explode(',', $input);
    }

    public function getCombinations()
    {
        return $this->combineWith;
    }

    public function getCombination($className)
    {
        if(!in_array($className, $this->combineWith)) {
            return null;
        } else {

        }
    }

    public function getExtraSuffixes()
    {
        return array();
    }
    public function getExtraPrefixes()
    {
        return array();
    }

    /**
     * @return array
     */
    protected function getInput()
    {
        return $this->input;
    }

    protected abstract function getStemStrings();

    /**
     * @return array
     */
    public function getStems()
    {
        $stems = [];

        $stemStrings = $this->getStemStrings();

        if(!empty($stemStrings)) {
            $prob = $this->probability / count($stemStrings);
            foreach ($stemStrings as $string) {
                $stems[$string] = $prob;
            }
        }

        return $stems;
    }
}