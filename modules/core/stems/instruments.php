<?php


class instruments extends BlankStem
{
    public function __construct($input = null)
    {
        parent::__construct(
            StatsMinder::STEM_MIN_LOCAL_PROB,
            $input
        );
    }
}