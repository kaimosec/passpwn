<?php

/**
 * Class StatsMinder
 *
 * Constant stats were worked out by analysing existing password lists.
 */
class StatsMinder
{
    const STEM_PROB = 1;//Probability that a password will contain a stem
    const SUFFIX_PROB = 0.2;//Probability that a password will contain a suffix
    const TRANSFORM_PROB = 0.05;//Probability that the stem is transformed

    const SUFFIX_MIN_LOCAL_PROB = 1.48162964444593E-06;
    const SUFFIX_AVG_LOCAL_PROB = 0.055586543222224;

    const TRANSFORM_MIN_LOCAL_PROB = 0.032257;

    const STEM_MIN_LOCAL_PROB = 0.001508;
    const STEM_AVG_LOCAL_PROB = 0.011830;

    public function __construct()
    {
    }

    public static function getPasswordProbability(
        $stemProbability,
        $suffixLocalProbability = null,
        $transformLocalProbability = null,
        $isStemCombination = false
    )
    {
        $prob = self::STEM_PROB * $stemProbability;

        if($isStemCombination) {
            $prob /= 2;
        }

        if($suffixLocalProbability !== null) {
            $prob *= self::SUFFIX_PROB * $suffixLocalProbability;
        }

        if($transformLocalProbability !== null) {
            $prob *= self::TRANSFORM_PROB * $transformLocalProbability;
        }

        return $prob;
    }
}