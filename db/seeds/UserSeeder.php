<?php


use Faker\Factory;
use Phinx\Seed\AbstractSeed;

class UserSeeder extends AbstractSeed
{
    private \Faker\Generator $faker;

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
        $data  = [];
        for ($i = 0, $max = $this->faker->numberBetween(200, 2000); $i < $max; ++$i) {
            $data[] = ['username' => $this->faker->unique()->userName()];
        }

        $this->table('users')
            ->insert($data)
            ->saveData();
    }
}
