<?php


class christian extends StemMaster
{
    public function __construct($input = null)
    {
        parent::__construct(
            0.000670,
            $input,
            "TRUE/FALSE. Are they Christian?"
        );
    }

    public function getStemStrings()
    {
        $input = $this->getInput();
        if(
            $input[0] === 'true'
            || $input[0] === 'yes'
            || $input[0] === 'y'
        ) {
            $stems = array();

            array_push($stems, "christian");
            array_push($stems, "jesus");
            array_push($stems, 'angel');
            array_push($stems, 'angels');
            array_push($stems, 'gabriel');
            array_push($stems, 'jesussaves');
            array_push($stems, 'lord');

            //Adding my own
            array_push($stems, 'christ');
            array_push($stems, 'god');

            return $stems;
        } else {
            return array();
        }
    }
}