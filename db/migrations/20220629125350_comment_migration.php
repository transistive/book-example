<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CommentMigration extends AbstractMigration
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
		$this->table('comments')
			->addColumn('user_id', 'integer', ['null' => true])
			->addColumn('article_id', 'integer', ['null' => true])
			->addColumn('parent_id', 'integer', ['null' => true])
			->addColumn('comment', 'string')
			->addForeignKey('user_id', 'users', 'id', ['delete'=> 'SET_NULL', 'update'=> 'NO_ACTION'])
			->addForeignKey('article_id', 'articles', 'id', ['delete'=> 'SET_NULL', 'update'=> 'NO_ACTION'])
			->addForeignKey('parent_id', 'comments', 'id', ['delete'=> 'SET_NULL', 'update'=> 'NO_ACTION'])
			->addTimestamps()
			->create();
    }
}
