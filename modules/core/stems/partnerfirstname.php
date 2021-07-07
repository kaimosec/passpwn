<?php


class partnerfirstname extends StemMaster
{
    public function __construct($input = null)
    {
        parent::__construct(
            0.0118,
            $input,
            "Include previous partners"
        );
    }

    public function getStemStrings()
    {
        $stems = $this->getInput();

        foreach($this->getInput() as $input) {
            array_push($stems, "ilove".$input);
            array_push($stems, "iluv".$input);
            array_push($stems, $input.'andme');
        }

        return $stems;
    }
}