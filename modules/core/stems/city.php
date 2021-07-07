<?php


class city extends BlankStem
{
    public function __construct($input = null)
    {
        parent::__construct(
            0.036,
            $input,
            "City/Town/Suburb they reside - or have resided - in"
        );
    }
}