<?php

class PasswordList
{
    /**
     * @var array $passwords As "password" => probability
     * @var int|null $keepTopN If set, will only hold the top $keepTopN passwords in the array
     */
    private $passwords;
    private $keepTopN;

    /**
     * PasswordList constructor.
     * @param array $passwords
     * @param null|int $keepTopN
     */
    public function __construct(
        $passwords = [],
        $keepTopN = null
    )
    {
        $this->passwords = $passwords;
        $this->keepTopN = $keepTopN;
    }

    public function addPassword($passwordString, $probability)
    {
        if($this->shouldAdd($probability)) {
            $this->doAdd($passwordString, $probability);
            return true;
        }
        return false;
    }

    /**
     * @return array
     */
    public function getPasswords()
    {
        return $this->passwords;
    }

    /**
     * @param float $probability
     * @return bool
     */
    private function shouldAdd($probability)
    {
        if(
            $this->keepTopN === null
        ) {
            return true;
        } else {
            if(count($this->passwords) < $this->keepTopN) {
                return true;
            } else {
                reset($this->passwords);
                $lowestProb = current($this->passwords);

                if($probability > $lowestProb) {
                    return true;
                }

                return false;
            }
        }
    }

    private function doAdd($passwordString, $probability)
    {
        if($this->keepTopN === null) {
            $this->passwords[$passwordString] = $probability;
        } else {
            if(count($this->passwords) < $this->keepTopN) {
                $this->passwords[$passwordString] = $probability;

                //If $this->passwords was filled up, sort it first
                if(count($this->passwords) === $this->keepTopN) {
                    $this->sortByProbAscending();
                }
            } else {
                //shouldAdd() has already checked that curpass is higher up

                //Remove lowest probability entry
                reset($this->passwords);
                unset($this->passwords[key($this->passwords)]);

                //Add password in-place
                //$offset = $this->findOffsetForInsertion($probability);
                //$this->addPasswordWithOffset($passwordString, $probability, $offset);
                $this->passwords[$passwordString] = $probability;
                $this->sortByProbAscending();
            }
        }
    }

    private function sortByProbAscending()
    {
        asort($this->passwords);
    }

    public function sortByProbDescending()
    {
        arsort($this->passwords);
    }

    /*todo remove
    private function addPasswordWithOffset($password, $probability, $offset)
    {
        $this->passwords = array_merge(
            array_slice($this->passwords, 0, $offset),
            [$password => $probability],
            array_slice($this->passwords, $offset)
        );
    }

    /**
     * I tried everything to have this iterate as few times as possible, but
     * PHP has its limitations.
     * @param float $newProb
     * @return int
     */
    /*todo remove
    private function findOffsetForInsertion($newProb)
    {
        reset($this->passwords);
        $iSuperior = 0;//Index of the first higher probability

        //Iterate through, if the first index isn't superior
        if($newProb >= current($this->passwords)) {
            while (true) {
                $iSuperior++;
                //Exit if it reaches the end of the array
                if(count($this->passwords) === $iSuperior) {
                    break;
                }
                $curProb = next($this->passwords);

                //Exit if we reached the first higher probability
                if($newProb < $curProb) {
                    break;
                }
            }
        }

        //Now $iSuperior represents the index of the first higher probability
        return $iSuperior;
    }
    */
}