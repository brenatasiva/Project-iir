<?php

declare(strict_types=1);

namespace Phpml\Math\Distance;

use Phpml\Exception\InvalidArgumentException;
use Phpml\Math\Distance;

class Jaccard implements Distance
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

        $obs_in_both = count(array_intersect($a, $b));
        $obs_in_either = count(array_unique(array_merge($a, $b)));
        $jaccard_similarity = $obs_in_both / $obs_in_either;
        $jaccard_distance = 1 - $jaccard_similarity;

        return $jaccard_distance;

        // ref: https://www.statology.org/jaccard-similarity/
    }
}
