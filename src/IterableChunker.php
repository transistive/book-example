<?php

namespace Nagels\BookExample;

use Generator;

class IterableChunker
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
}