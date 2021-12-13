<?php

declare(strict_types=1);

namespace Phpml\Math\Distance;

use Phpml\Exception\InvalidArgumentException;
use Phpml\Math\Distance;

class Overlap implements Distance
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
        $wkj = 0.0;
        for ($x = 0; $x < count($a); $x++) {
            $numerator += $b[$x] * $a[$x];
            $wkq += pow($b[$x], 2);
            $wkj += pow($a[$x], 2);
        }
        if ($wkq == 0.0 || $wkj == 0.0) {
            $result = 0.0;
        } else {
            $result = $numerator / min($wkq, $wkj);
        }

        return $result;
    }
}
