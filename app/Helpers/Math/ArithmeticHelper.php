<?php

namespace App\Helpers\Math;

use InvalidArgumentException;

class ArithmeticHelper
{
    public static function add(...$nums)
    {
        if (sizeof($nums) < 1) {
            throw new InvalidArgumentException('Must have at least 1 argument.');
        }
        $sum = 0;
        foreach ($nums as $num) {
            if (!is_float($num) && !is_int($num)) {
                throw new InvalidArgumentException('Argument can only be numeric.');
            }
            $sum += $num;
        }
        return $sum;
    }

    public static function minus()
    {

    }
}
