<?php


class TransformFirst2Uppercase extends TransformMaster
{
    public function __construct()
    {

        parent::__construct(0.032258);
    }

    protected function doTransform($stem)
    {
        $firstN = substr($stem, 0, 2);

        return str_replace(
            $firstN,
            strtoupper($firstN),
            $stem
        );
    }
}