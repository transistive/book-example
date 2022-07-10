<?php

use Laudis\Neo4j\Basic\Driver;
use Nagels\BookExample\Helper;
use Nagels\BookExample\NodeRepository;
use Nagels\BookExample\PDORepository;
use Nagels\BookExample\RelationshipRepository;
use Nagels\BookExample\TablesEnum;

require __DIR__.'/vendor/autoload.php';

$driver = Driver::create('neo4j://neo4j:test@localhost');
$driver->verifyConnectivity() ?? throw new Error('Cannot connect to database');
$session = $driver->createSession();

$pdo = new PDORepository(new PDO('mysql:host=127.0.0.1;port=3306;dbname=test', 'test', 'sql'));
$nodes = new NodeRepository($session);
$relationships = new RelationshipRepository($session, $pdo);

$nodes->storeRowsAsNodes(TablesEnum::ARTICLES, $pdo->yieldTable(TablesEnum::ARTICLES));
$nodes->storeRowsAsNodes(TablesEnum::COMMENTS, $pdo->yieldTable(TablesEnum::COMMENTS));
$nodes->storeRowsAsNodes(TablesEnum::USERS, $pdo->yieldTable(TablesEnum::USERS));
$nodes->storeRowsAsNodes(TablesEnum::TAGS, $pdo->yieldTable(TablesEnum::TAGS));

$categories = $pdo->yieldTable(TablesEnum::POLYMORPHIC_CATEGORIES);
$categories = Helper::map($categories, static fn (array $x) => [
    ...$x,
    ...['label' => TablesEnum::from($x['resource_table'])->asTag()]
]);
$nodes->storeRowsAsNodes(TablesEnum::POLYMORPHIC_CATEGORIES, $categories);

$relationships->connectArticles();
$relationships->connectTags();
$relationships->connectComments();
$relationships->connectCategories();


