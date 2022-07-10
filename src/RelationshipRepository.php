<?php

namespace Nagels\BookExample;

use Laudis\Neo4j\Basic\Session;

class RelationshipRepository
{
    public function __construct(private readonly Session $session, private readonly PDORepository $pdo)
    {
    }

    public function connectArticles(): int
    {
        return $this->session->run(<<<'CYPHER'
        MATCH (child:Article), (parent:Article {id: child['parent_id']})
        MERGE (child) - [:HAS_PARENT] -> (parent)
        CYPHER)
            ->getSummary()
            ->getCounters()
            ->relationshipsCreated();
    }

    public function connectComments(): int
    {
        return $this->session->run(<<<'CYPHER'
        MATCH (c:Comment), (a:Article {id: c['article_id']}), (u:User {id: c['user_id']})
        MERGE (c) - [:COMMENTED_ON] -> (a)
        MERGE (u) - [:COMMENTED] -> (c)
        CYPHER)
            ->getSummary()
            ->getCounters()
            ->relationshipsCreated()

            +

        $this->session->run(<<<'CYPHER'
        MATCH (c), (p:Comment {id: c['parent_id']})
        MERGE (c) - [:COMMENTED_ON] -> (p)
        CYPHER)
           ->getSummary()
           ->getCounters()
           ->relationshipsCreated();
    }

    public function connectCategories(): int
    {
        return $this->session->run(<<<'CYPHER'
        MATCH (c:Category), (x {id: c.resource_id})
        WHERE c.label IN labels(x)
        MERGE (c) - [:CATEGORIZES] -> (x)
        CYPHER)
            ->getSummary()
            ->getCounters()
            ->relationshipsCreated();
    }

    public function connectTags(): int
    {
        $relationships = 0;
        foreach (Helper::chunk($this->pdo->yieldTable(TablesEnum::ARTICLE_TAGS)) as $chunk) {
            $relationships += $this->session->run(<<<'CYPHER'
            UNWIND $articleTags as articleTag
            MATCH (t:Tag {id: articleTag['tag_id']}), (a:Article {id: articleTag['article_id']})
            MERGE (t) - [ta:TAGS] -> (a)
            ON CREATE SET ta = articleTag
            CYPHER, ['articleTags' => $chunk])
                ->getSummary()
                ->getCounters()
                ->relationshipsCreated();
        }

        return $relationships;
    }
}