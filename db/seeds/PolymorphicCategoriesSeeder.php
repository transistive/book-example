<?php


use Faker\Factory;
use Phinx\Seed\AbstractSeed;

class PolymorphicCategoriesSeeder extends AbstractSeed
{
    private \Faker\Generator $faker;

    public function __construct()
    {
        parent::__construct();
        $this->faker = Factory::create();
    }

    public function getDependencies(): array
    {
        return array_merge(parent::getDependencies(), [
            ArticleSeeder::class,
            CommentSeeder::class,
            TagSeeder::class,
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
    public function run(): void
    {
        $idMapper = static fn (array $ids, string $table) =>
            array_map(static fn (array $x) => [
                'resource_id' => $x['id'],
                'resource_table' => $table
            ], $ids);

        $userSet = $this->fetchAll('SELECT id FROM users');
        $articleSet = $this->fetchAll('SELECT id FROM articles');
        $commentSet = $this->fetchAll('SELECT id FROM comments');
        $tagSet = $this->fetchAll('SELECT id FROM tags');

        $availableSets = [
            ...$idMapper($userSet, 'users'),
            ...$idMapper($articleSet, 'articles'),
            ...$idMapper($commentSet, 'comments'),
            ...$idMapper($tagSet, 'tags'),
        ];

        $maxInSet = count($availableSets) - 1;

        $categories = [];
        for($i = 0, $iMax = $this->faker->numberBetween(5, 25); $i < $iMax; ++$i) {
            $categories[] = $this->faker->unique()->word();
        }

        $data = [];
        foreach ($categories as $category) {
            $this->faker->unique(true);
            while ($this->faker->boolean(99)) {
                $setIndex = $this->faker->unique()->numberBetween(0, $maxInSet);
                $data[] = array_merge($availableSets[$setIndex], ['category' => $category]);
            }
        }

        $this->table('polymorphic_categories')
            ->insert($data)
            ->saveData();
    }
}
