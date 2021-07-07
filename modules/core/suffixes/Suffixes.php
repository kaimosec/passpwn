<?php


class Suffixes extends SuffixMaster
{
    protected function generateSuffixes()
    {
        //Start with the one-timers
            $this->add('1', 0.481481);
            $this->add('123', 0.103703);
            $this->add('1234', 0.014814);
            $this->add('0', 0.007407);
            $this->add('s');

        //Next: Custom Stuffs

        //Age
        //todo where is $array coming from?
        if(!empty($array['age'])) {
            $this->add($array['age'], 0.096296);
        }

        //DOB YY / YYYY
        if(!empty($array['dobYYYY'])) {
            //todo how to use this?

            $this->add($array['dobYYYY'], 0.014814);
            $this->add(substr($array['dobYYYY'], 0, -2), 0.014814);
        }

        //Next: Iterative Suffixes

        //2-9
        for($i = 2;$i <= 9;$i++) {
            $this->add("$i", 0.020370);
        }

        //Age, 10-19, unt
        for($i = 10;$i <= 19;$i++) {
            $this->add("$i", 0.014814);
        }

        ////Age, 20-29 unt
        for($i = 20;$i <= 29;$i++) {
            $this->add("$i", 0.003703);
        }

        //Special Characters

        //!
        $this->add('!', 0.014814);

        //@#$%^&*()
        $specials = [
            '@',
            '#',
            '$',
            '%',
            '^',
            '&',
            '*',
            '(',
            ')',
            '_',
            '+'
        ];
        $singleSpecialProb = StatsMinder::SUFFIX_MIN_LOCAL_PROB;
        foreach($specials as $special) {
            $this->add($special, $singleSpecialProb);

            //Specials with a number
            $specialNumberProb = pow($singleSpecialProb, 2);
            for($i = 0;$i < 10;$i++) {
                $this->add($special.$i, $specialNumberProb);
                $this->add("$i".$special, $specialNumberProb);
            }
        }

        //Less Common Specials
        $rareSpecials = [
            '[',
            ']',
            '\'',
            '"',
            ';',
            ':',
            ',',
            '.',
            '/',
            '\\',
            '|',
            '~',
            '`'
        ];
        $singleRareSpecialProb = StatsMinder::SUFFIX_MIN_LOCAL_PROB / 2;
        foreach($rareSpecials as $special) {
            $this->add($special, $singleRareSpecialProb);
        }

        //Extra

        //Last 20 years in YYYY
        $maxYearRange = 20;
        $maxYear = ((int)date("Y")) + 1;
        $minYear = $maxYear - $maxYearRange;

        for($i = $maxYear;$i >= $minYear;$i--) {
            $this->add("$i", 0.00074);
        }

        //DOB YYYY (untargeted) and DOB YY (untargeted)
        $minAge = 10;
        $maxAge = 80;

        $now = ((int) date("Y"));
        $start = $now - $minAge;
        $end = $now - $maxAge;
        for($i = $start;$i >= $end;$i--) {
            //YYYY
            $this->add("$i", 0.000212);

            //YY
            $this->add(substr($i, -2), 0.000635);
        }

        //0-9999
        for($i = 0;$i <= 9999;$i++) {
            $num = sprintf("%04d",$i);
            $this->add($num, 0.0000015);
        }

        //More of this stuff @#$%^&*()
        $specials_B = [
            '!!',
            '@@',
            '##',
            '$$',
            '%%',
            '^^',
            '&&',
            '**',
            '((',
            '))',
            '__',
            '++',
            '!@',
            '@#',
            '#$',
            '$%',
            '%^',
            '^&',
            '&*',
            '*(',
            '()',
            ')_',
            '_+',
            '+_',
            '_)',
            ')(',
            '(*',
            '*&',
            '&^',
            '^%',
            '%$',
            '$#',
            '#@',
            '@!',
            '!@#',
            '@#$',
            '#$%',
            '$%^',
            '%^&',
            '^&*',
            '&*(',
            '*()',
            '()_',
            ')_+'
        ];
        foreach($specials_B as $special) {
            $pow = pow($singleSpecialProb, 2);
            $this->add($special, $pow);
        }
    }
}