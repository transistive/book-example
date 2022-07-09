<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class TagMigration extends AbstractMigration
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
		$this->table('tags')
			->addColumn('tag', 'string')
	        ->addIndex('tag', ['unique' => true])
			->addTimestamps()
			->create();

		$this->table('article_tags')
			->addColumn('tag_id', 'integer', ['null' => true])
			->addColumn('article_id', 'integer', ['null' => true])
			->addTimestamps()
			->addForeignKey('tag_id', 'tags', 'id', ['delete'=> 'SET_NULL', 'update'=> 'NO_ACTION'])
			->addForeignKey('article_id', 'articles', 'id', ['delete'=> 'SET_NULL', 'update'=> 'NO_ACTION'])
			->addIndex(['tag_id', 'article_id'], ['unique' => true])
			->create();
    }
}
