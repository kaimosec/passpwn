<?php


class TransformCamelCase extends TransformMaster
{
    public function __construct()
    {
        parent::__construct(0.225806);
    }

    protected function doTransform($stem)
    {
        return ucwords($stem);
    }
}