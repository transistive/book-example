<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class ArticleMigration extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change(): void
    {
		$this->table('articles')
			->addColumn('parent_id', 'integer', ['null' => true])
			->addColumn('content', 'string', ['limit' => 10000])
			->addColumn('title', 'string', ['limit' => 255])
			->addForeignKey('parent_id', 'articles', 'id', ['delete'=> 'SET_NULL', 'update'=> 'NO_ACTION'])
			->addTimestamps()
			->create();
    }
}
