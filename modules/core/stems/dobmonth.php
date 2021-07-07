<?php


class dobmonth extends BlankStem
{
    public function __construct($input = null)
    {
        parent::__construct(
            0.048,
            $input,
            "e.g. September"
        );
    }
}