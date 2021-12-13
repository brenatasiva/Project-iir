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

        $result = 0.0;
        $numerator = 0.0;
        $denom_wkq = 0.0;
        $denom_wkj = 0.0;
        for ($x = 0; $x < count($a); $x++) {
            $numerator += $b[$x] * $a[$x];
            $denom_wkq += pow($b[$x], 2);
            $denom_wkj += pow($a[$x], 2);
        }
        if ($denom_wkq == 0.0 && $denom_wkj && $numerator == 0.0) {
            $result = 0.0;
        } else {
            $result = $numerator / (0.5 * $denom_wkq + 0.5 * $denom_wkj);
        }

        return $result;
    }
}
