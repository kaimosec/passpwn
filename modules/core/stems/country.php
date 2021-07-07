<?php


class country extends BlankStem
{
    public function __construct($input = null)
    {
        parent::__construct(
            0.009049,
            $input,
            "What countries do they reside in?"
        );
    }
}