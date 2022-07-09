<?php

namespace Nagels\BookExample;

use Laudis\Neo4j\Basic\Session;

class RelationshipRepository
{
    public function __construct(private readonly Session $session, private readonly PDORepository $pdo)
    {
    }

    public function connectArticles(): void
    {
        $this->session->run(<<<'CYPHER'
        MATCH (child:Article), (parent:Article {id: child['parent_id']})
        MERGE (child) - [:HAS_PARENT] -> (parent)
        CYPHER);
    }

    public function connectComments(): void
    {
        $this->session->run(<<<'CYPHER'
        MATCH (c:Comment), (a:Article {id: c['article_id']}), (u:User {id: c['user_id']})
        MERGE (c) - [:COMMENTED_ON] -> (a)
        MERGE (u) - [:COMMENTED] -> (c)
        WITH c
        MATCH (c), (p:Comment {id: c['parent_id']})
        MERGE (c) - [:COMMENTED_ON] -> (p)
        CYPHER);
    }

    public function connectCategories(): void
    {
        $this->session->run(<<<'CYPHER'
        MATCH (c:Category), (x {id: c.resource_id})
        WHERE c.label IN labels(x)
        MERGE (c) - [:CATEGORIZES] -> (x)
        CYPHER);
    }

    public function connectTags(): void
    {
        foreach (Helper::chunk($this->pdo->yieldTable(TablesEnum::ARTICLE_TAGS)) as $chunk) {
            $this->session->run(<<<'CYPHER'
            UNWIND $articleTags as articleTag
            MATCH (t:Tag {id: articleTag['tag_id']}), (a:Article {id: articleTag['article_id']})
            MERGE (t) - [ta:TAGS] -> (a)
            ON CREATE SET ta = articleTag
            CYPHER, ['articleTags' => $chunk]);
        }
    }
}