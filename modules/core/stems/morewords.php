<?php


class morewords extends BlankStem
{
    public function __construct($input = null)
    {
        parent::__construct(
            StatsMinder::STEM_AVG_LOCAL_PROB,
            $input,
            "Add any keywords you think may be relevant"
        );
    }
}