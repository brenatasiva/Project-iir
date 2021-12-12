<?php

declare(strict_types=1);

namespace Phpml\Math\Distance;

use Phpml\Exception\InvalidArgumentException;
use Phpml\Math\Distance;

class Cosine implements Distance
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

        foreach ($a as $i => $val) {
            $distance += ($val * $b[$i]);
        }

        $distance / (count($a) * count($b));

        return ($distance);
        
        // ref: https://stackoverflow.com/questions/16803289/cosine-similarity-php
    }
}
