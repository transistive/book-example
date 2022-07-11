<?php

namespace Nagels\BookExample;

use Laudis\Neo4j\Basic\Session;
use Laudis\Neo4j\Databags\ResultSummary;

class RelationshipRepository
{
    public function __construct(private readonly Session $session)
    {
    }

    public function connectArticles(): ResultSummary
    {
        return $this->session->run(<<<'CYPHER'
        MATCH (child:Article), (parent:Article {id: child['parent_id']})
        MERGE (child) - [:HAS_PARENT] -> (parent)
        CYPHER)->getSummary();
    }

    public function connectCommentToArticles(): ResultSummary
    {
        return $this->session->run(<<<'CYPHER'
        MATCH (c:Comment), (a:Article {id: c['article_id']})
        MERGE (c) - [:COMMENTED_ON] -> (a)
        CYPHER)->getSummary();
    }

    public function connectParentComments(): ResultSummary
    {
        return $this->session->run(<<<'CYPHER'
        MATCH (c:Comment), (p:Comment {id: c['parent_id']})
        MERGE (c) - [:COMMENTED_ON] -> (p)
        CYPHER)->getSummary();
    }

    public function connectCommentsToUsers(): ResultSummary
    {
        return $this->session->run(<<<'CYPHER'
        MATCH (c:Comment), (u:User {id: c['user_id']})
        MERGE (u) - [:COMMENTED] -> (c)
        CYPHER)->getSummary();
    }

    public function connectCategories(): ResultSummary
    {
        return $this->session->run(<<<'CYPHER'
        MATCH (c:Category), (x {id: c.resource_id})
        WHERE c.label IN labels(x)
        MERGE (c) - [:CATEGORIZES] -> (x)
        CYPHER)->getSummary();
    }

    public function connectTags(): ResultSummary
    {
        return $this->session->run(<<<'CYPHER'
        MATCH (at:ArticleTag), (t:Tag {id: at['tag_id']}), (a:Article {id: at['article_id']})
        MERGE (t) - [ta:TAGS] -> (a)
        ON CREATE SET ta = at
        CYPHER)->getSummary();
    }
}