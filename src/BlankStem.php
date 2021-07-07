<?php


class BlankStem extends StemMaster
{
    public function getStemStrings()
    {
        return $this->getInput();
    }
}