<?php


class TransformFirst3Uppercase extends TransformMaster
{
    public function __construct()
    {

        parent::__construct(0.032258);
    }

    protected function doTransform($stem)
    {
        $firstN = substr($stem, 0, 3);

        return str_replace(
            $firstN,
            strtoupper($firstN),
            $stem
        );
    }
}