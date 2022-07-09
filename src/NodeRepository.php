<?php

namespace Nagels\BookExample;

use Laudis\Neo4j\Basic\Session;

class NodeRepository
{
    public function __construct(private readonly Session $session)
    {
    }

    public function storeRowsAsNodes(TablesEnum $table, iterable $rows, int $chunkSize = 200): void
    {
        $tag = $table->asTag();
        foreach (Helper::chunk($rows, $chunkSize) as $chunk) {
            $this->session->run(<<<CYPHER
            UNWIND \$chunk as row
            MERGE (a:$tag {id: row['id']})
            ON CREATE SET a = row;
            CYPHER, ['chunk' => $chunk]);
        }
    }
}