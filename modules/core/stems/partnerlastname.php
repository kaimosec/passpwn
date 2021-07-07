<?php


class partnerlastname extends BlankStem
{
    public function __construct($input = null)
    {
        parent::__construct(
            0.008,//A guess in probability
            $input
        );
    }
}