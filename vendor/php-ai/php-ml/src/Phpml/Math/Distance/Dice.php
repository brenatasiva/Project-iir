<?php

declare(strict_types=1);

namespace Phpml\Math\Distance;

use Phpml\Exception\InvalidArgumentException;
use Phpml\Math\Distance;

class Dice implements Distance
{
    /**
     * @param array $a
     * @param array $b
     *
     * @return float
     *
     * @throws InvalidArgumentException
     */
    public function distance(array $a, array $b): float
    {
        if (count($a) !== count($b)) {
            throw InvalidArgumentException::arraySizeNotMatch();
        }

        $distance = 0;
        $obs_in_both = count(array_intersect($a, $b));


        return 1 - ((2 * $obs_in_both) / (count($a) + count($b)));

        // ref https://towardsdatascience.com/17-types-of-similarity-and-dissimilarity-measures-used-in-data-science-3eb914d2681
    }
}
