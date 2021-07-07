<?php


class TransformLeetL1 extends TransformMaster
{
    public function __construct()
    {

        parent::__construct(StatsMinder::TRANSFORM_MIN_LOCAL_PROB);
    }

    protected function doTransform($stem)
    {
        return str_ireplace('l', '1', $stem);
    }
}