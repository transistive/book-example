<?php


use Faker\Factory;
use Nagels\BookExample\Helper;
use Phinx\Seed\AbstractSeed;

class CommentSeeder extends AbstractSeed
{
    private \Faker\Generator $faker;
    private int $currentId = 0;
    private array $userIds = [];

    public function __construct()
    {
        parent::__construct();
        $this->faker = Factory::create();
    }

    public function getDependencies(): array
    {
        return array_merge(parent::getDependencies(), [
           ArticleSeeder::class,
           UserSeeder::class
        ]);
    }

    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeders is available here:
     * https://book.cakephp.org/phinx/0/en/seeding.html
     */
    public function run()
    {
        $this->currentId = $this->fetchAll('SELECT id from comments ORDER BY id DESC LIMIT 1')[0]['id'] ?? 0;

        $this->userIds = array_map(static fn (array $x) => $x['id'], $this->fetchAll('SELECT id FROM users'));
        $table = $this->table('comments');
        foreach (Helper::chunk($this->generateData(), 10) as $dataset) {
            $table->insert($dataset)
                  ->saveData();
        }
    }

    private function createComment(?int $articleId, int $parentId = null): array
    {
        ++$this->currentId;
        $userIdIndex = $this->faker->numberBetween(0, count($this->userIds) - 1);
        return [
            'comment'     => $this->faker->realText($this->faker->numberBetween(10, 255)),
            'id'        => $this->currentId,
            'parent_id' => $parentId,
            'article_id' => $articleId,
            'user_id' => $this->userIds[$userIdIndex]
        ];
    }

    private function createChildComments(?int $articleId): Generator
    {
        $parentId = $this->currentId;
        while ($this->faker->boolean(20)) {
            yield $this->createComment($articleId, $parentId);
            yield from $this->createChildComments(null);
        }
    }

    private function generateData(): Generator
    {
        $articleIds = array_map(static fn (array $x) => $x['id'], $this->fetchAll('SELECT id FROM articles'));
        foreach ($articleIds as $articleId) {
            if ($this->faker->boolean()) {
                yield $this->createComment($articleId);
                yield from $this->createChildComments(null);
            }
        }
    }
}
