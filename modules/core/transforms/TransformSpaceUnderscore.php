<?php


class TransformSpaceUnderscore extends TransformMaster
{
    protected function doTransform($stem)
    {
        return str_ireplace(' ', '_', $stem);
    }
}