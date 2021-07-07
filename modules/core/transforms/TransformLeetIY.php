<?php


class TransformLeetIY extends TransformMaster
{
    public function __construct()
    {

        parent::__construct(0.032258);
    }

    protected function doTransform($stem)
    {
        return str_ireplace('i', 'y', $stem);
    }
}