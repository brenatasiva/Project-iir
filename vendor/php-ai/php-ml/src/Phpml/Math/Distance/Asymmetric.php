<?php

declare(strict_types=1);

namespace Phpml\Math\Distance;

use Phpml\Exception\InvalidArgumentException;
use Phpml\Math\Distance;

class Asymmetric implements Distance
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

        $result = 0.0;
        $numerator = 0.0;
        $wkq = 0.0;
        for ($x = 0; $x < count($a); $x++) {
            $numerator += min($b[$x], $a[$x]);
            $wkq += $b[$x];
        }
        if ($wkq == 0.0) {
            $result = 0.0;
        } else {
            $result = $numerator / $wkq;
        }

        return $result;
    }
}
