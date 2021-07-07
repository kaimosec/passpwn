<?php


class TransformUpperCase extends TransformMaster
{
    public function __construct()
    {
        parent::__construct(0.548387);
    }

    protected function doTransform($stem)
    {
        return strtoupper($stem);
    }
}