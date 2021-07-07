<?php


class firstname extends BlankStem
{
    public function __construct($input = null)
    {
        parent::__construct(
            0.393,
            $input,
            "Also include middle names"
        );
    }
}