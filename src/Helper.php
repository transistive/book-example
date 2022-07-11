<?php

namespace Nagels\BookExample;

use Generator;
use Laudis\Neo4j\Databags\ResultSummary;
use function round;
use const PHP_EOL;

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

    public static function logCreatedNodes(TablesEnum $table, ResultSummary $summary): void
    {
        echo sprintf(
            'Created %s nodes with label "%s" in %s seconds'.PHP_EOL,
            $summary->getCounters()->nodesCreated(),
            $table->asTag(),
            round($summary->getResultConsumedAfter(), 3)
        );
    }

    public static function logCreatedRelationships(string $type, ResultSummary $summary): void
    {
        echo sprintf(
            'Created %s relationships with type "%s" in %s seconds'.PHP_EOL,
            $summary->getCounters()->relationshipsCreated(),
            $type,
            round($summary->getResultConsumedAfter(), 3)
        );
    }
}