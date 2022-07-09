<?php


use Faker\Factory;
use Phinx\Seed\AbstractSeed;

class TagSeeder extends AbstractSeed
{
    private \Faker\Generator $faker;
    private array $articleIds = [];
    private array $tagIds = [];

    public function __construct()
    {
        parent::__construct();
        $this->faker = Factory::create();
    }

    public function getDependencies()
    {
        return array_merge(parent::getDependencies(), [
            ArticleSeeder::class,
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
        $tags = [];
        for ($i = 0, $iMax = $this->faker->numberBetween(20, 40); $i < $iMax; ++$i) {
            $tags[] = ['tag' => $this->faker->unique()->word()];
        }

        $this->table('tags')
             ->insert($tags)
             ->saveData();


        $this->articleIds = array_map(static fn(array $x) => $x['id'], $this->fetchAll('SELECT id FROM articles'));
        $this->tagIds     = array_map(static fn(array $x) => $x['id'], $this->fetchAll('SELECT id FROM tags'));
        $maxTag           = count($this->tagIds) - 1;

        $data = [];
        foreach ($this->articleIds as $articleId) {
            $this->faker->unique(true);
            while ($this->faker->boolean()) {
                $tagNumber = $this->faker->unique()->numberBetween(0, $maxTag);
                $data[] = [
                    'tag_id' => $this->tagIds[$tagNumber],
                    'article_id' => $articleId
                ];
            }
        }

        $this->table('article_tags')
            ->insert($data)
            ->saveData();
    }
}
