<?php

namespace Nagels\BookExample;

use DateTime;
use Generator;
use PDO;
use function array_key_exists;

class PDORepository
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    public function yieldRows(TablesEnum $table): Generator
    {
        $statement = $this->pdo->query(sprintf('SELECT * FROM %s', $table->value));

        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $row['created_at'] = new DateTime($row['created_at']);
            $row['updated_at'] = $row['updated_at'] === null ? null : new DateTime($row['updated_at']);

            yield $row;
        }
    }
}