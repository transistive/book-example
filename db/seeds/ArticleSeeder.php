<?php


use Faker\Factory;
use Nagels\BookExample\IterableChunker;
use Phinx\Seed\AbstractSeed;

class ArticleSeeder extends AbstractSeed
{
    private \Faker\Generator $faker;
    private int $currentId = 0;

    public function __construct()
    {
        parent::__construct();
        $this->faker = Factory::create();
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
        $this->currentId = $this->fetchAll('SELECT id from articles ORDER BY id DESC LIMIT 1')[0]['id'] ?? 0;
        $table = $this->table('articles');
        foreach (IterableChunker::chunk($this->generateData(), 10) as $dataset) {
            $table->insert($dataset)
                ->saveData();
        }
    }

    private function createArticle(int $parentId = null): array
    {
        ++$this->currentId;
        return [
            'title'     => $this->faker->realText($this->faker->numberBetween(10, 255)),
            'content'   => $this->faker->realText($this->faker->numberBetween(255, 10000)),
            'id'        => $this->currentId,
            'parent_id' => $parentId,
        ];
    }

    private function addChildArticles(): Generator
    {
        $parentId = $this->currentId;
        while ($this->faker->boolean()) {
            yield $this->createArticle($parentId);
            yield from $this->addChildArticles();
        }
    }

    private function generateData(): Generator
    {
        for ($i = 0, $max = $this->faker->numberBetween(5, 10); $i < $max; ++$i) {
            yield $this->createArticle();
            yield from $this->addChildArticles();
        }
    }
}
