<?php

use Laudis\Neo4j\Basic\Driver;
use Nagels\BookExample\Helper;
use Nagels\BookExample\NodeRepository;
use Nagels\BookExample\PDORepository;
use Nagels\BookExample\RelationshipRepository;
use Nagels\BookExample\TablesEnum;

require __DIR__.'/vendor/autoload.php';

$time = microtime(true);

// Create a driver to connect to Neo4J with user Neo4J and password test
$driver = Driver::create('neo4j://neo4j:test@localhost');
// Verify the connectivity beforehand to make sure the connection was successful.
$driver->verifyConnectivity() ?? throw new Error('Cannot connect to database');
// You can run queries on a session
$session = $driver->createSession();

$pdo = new PDORepository(new PDO('mysql:host=127.0.0.1;port=3306;dbname=test', 'test', 'sql'));
$nodes = new NodeRepository($session);
$relationships = new RelationshipRepository($session);

$categories = $pdo->yieldRows(TablesEnum::POLYMORPHIC_CATEGORIES);
$categories = Helper::map($categories, static fn (array $x) => [
    ...$x,
    ...['label' => TablesEnum::from($x['resource_table'])->asTag()]
]);

$session->run('CREATE CONSTRAINT article_id IF NOT EXISTS FOR (a:Article) REQUIRE a.id IS UNIQUE');
$session->run('CREATE CONSTRAINT comment_id IF NOT EXISTS FOR (a:Comment) REQUIRE a.id IS UNIQUE');
$session->run('CREATE CONSTRAINT user_id IF NOT EXISTS FOR (a:User) REQUIRE a.id IS UNIQUE');
$session->run('CREATE CONSTRAINT tag_id IF NOT EXISTS FOR (a:Tag) REQUIRE a.id IS UNIQUE');
$session->run('CREATE CONSTRAINT article_tag_id IF NOT EXISTS FOR (a:ArticleTag) REQUIRE a.id IS UNIQUE');
$session->run('CREATE CONSTRAINT category_id IF NOT EXISTS FOR (a:Category) REQUIRE a.id IS UNIQUE');

Helper::logCreatedNodes(TablesEnum::ARTICLES, $nodes->storeRowsAsNodes(TablesEnum::ARTICLES, $pdo->yieldRows(TablesEnum::ARTICLES)));
Helper::logCreatedNodes(TablesEnum::COMMENTS, $nodes->storeRowsAsNodes(TablesEnum::COMMENTS, $pdo->yieldRows(TablesEnum::COMMENTS)));
Helper::logCreatedNodes(TablesEnum::USERS, $nodes->storeRowsAsNodes(TablesEnum::USERS, $pdo->yieldRows(TablesEnum::USERS)));
Helper::logCreatedNodes(TablesEnum::TAGS, $nodes->storeRowsAsNodes(TablesEnum::TAGS, $pdo->yieldRows(TablesEnum::TAGS)));
Helper::logCreatedNodes(TablesEnum::ARTICLE_TAGS, $nodes->storeRowsAsNodes(TablesEnum::ARTICLE_TAGS, $pdo->yieldRows(TablesEnum::ARTICLE_TAGS)));
Helper::logCreatedNodes(TablesEnum::POLYMORPHIC_CATEGORIES, $nodes->storeRowsAsNodes(TablesEnum::POLYMORPHIC_CATEGORIES, $categories));

Helper::logCreatedRelationships('HAS_PARENT', $relationships->connectArticles());
Helper::logCreatedRelationships('TAGS', $relationships->connectTags());
Helper::logCreatedRelationships('COMMENTED_ON', $relationships->connectCommentToArticles());
Helper::logCreatedRelationships('COMMENTED', $relationships->connectCommentsToUsers());
Helper::logCreatedRelationships('COMMENTED_ON', $relationships->connectParentComments());
Helper::logCreatedRelationships('CATEGORIZES', $relationships->connectCategories());

$session->run('MATCH (x:ArticleTag) DELETE x');

echo sprintf(PHP_EOL.'Migrated to Neo4J in %s seconds'.PHP_EOL, round(microtime(true) - $time, 3));


