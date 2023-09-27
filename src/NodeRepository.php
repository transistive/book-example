<?php

namespace Nagels\BookExample;

use Laudis\Neo4j\Basic\Session;
use Laudis\Neo4j\Databags\ResultSummary;
use Laudis\Neo4j\Types\Node;
use const PHP_EOL;

class NodeRepository
{
    public function __construct(private readonly Session $session)
    {
    }

    public function storeRowsAsNodes(TablesEnum $table, iterable $rows): ResultSummary
    {
        $tag = $table->asTag();
        return $this->session->run(<<<CYPHER
        UNWIND \$rows as row
        MERGE (a:$tag {id: row['id']})
        ON CREATE SET a = row;
        CYPHER, ['rows' => $rows])->getSummary();
    }

    public function listAllTags(int $articleId): array
    {
        return $this->session->run(<<<'CYPHER'
        MATCH p = (:Article {id: $articleId}) <- [:HAS_PARENT*0..] - (:Article)
        UNWIND nodes(p) AS article
        WITH DISTINCT article
        MATCH (article) <- [:TAGS] - (tag:Tag)
        WITH DISTINCT tag
        RETURN tag.tag AS tag
        CYPHER, compact('articleId'))
            ->pluck('tag')
            ->toArray();
    }

    public function topCategoryNode(int $articleId): void
    {
        $node =  $this->session->run(<<<'CYPHER'
        MATCH (c:Category) - [:CATEGORIZES] -> (node)
        WITH node, collect(c) AS categoryDegree
        RETURN node
        ORDER BY categoryDegree DESC
        LIMIT 1
        CYPHER, compact('articleId'))
            ->getAsCypherMap(0)
            ->getAsNode('node');

        echo 'LABEL: ' . $node->getLabels()->first() . PHP_EOL;
        echo 'ID: ' . $node->getProperty('id') . PHP_EOL;
    }

    public function doubleCommenters(int $articleId): array
    {
        return $this->session->run(<<<'CYPHER'
        MATCH (b:Article) <- [:COMMENTED_ON*1..] - (:Comment) <- [:Commented] - (u:User),
              (u) - [:COMMENTED] -> (:Comment) - [:COMMENTED_ON*1..] -> (a:Article)
        WHERE a <> b
        RETURN DISTINCT u AS user
        CYPHER)
            ->pluck('user')
            ->toArray();
    }
}