<?php


class hobby extends StemMaster
{
    public function __construct($input = null)
    {
        parent::__construct(
            0.01659,
            $input,
            "Hobbies, sports etc. Only use the stem (e.g. golf instead of golfing"
        );
    }

    public function getStemStrings()
    {
        $stems = $this->getInput();

        foreach($this->getInput() as $input) {
            array_push($stems, "ilove".$input);
            array_push($stems, "iluv".$input);

            //-ing
            if(substr($input, -3, 3) !== 'ing') {
                if(substr($input, -1, 1) !== 'e') {
                    $ing = $input.'ing';
                } else {
                    $ing = substr($input, 0, -1).'ing';
                }
                array_push($stems, $ing);
                array_push($stems, "ilove".$ing);
                array_push($stems, "iluv".$ing);
            }

            //-er
            if(substr($input, -2, 2) !== 'er') {
                if(substr($input, -1, 1) !== 'e') {
                    array_push($stems, $input . 'er');
                } else {
                    array_push(
                        $stems,
                        $input.'r'
                    );
                }
            }
        }

        return $stems;
    }
}