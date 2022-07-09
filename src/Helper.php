<?php

namespace Nagels\BookExample;

use DateTime;
use Generator;

class Helper
{
    public static function chunk(iterable $it, int $size = 200): Generator
    {
        $chunks = [];
        $counter = 0;

        foreach ($it as $value) {
            if ($counter >= $size) {
                yield $chunks;
                $chunks = [];
            }

            $chunks[] = $value;
            ++$counter;
        }

        if (count($chunks) > 0) {
            yield $chunks;
        }
    }
    public static function map(iterable $it, callable $map): Generator
    {
        foreach ($it as $x) {
            yield $map($x);
        }
    }
}