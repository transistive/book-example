<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class PolymorphicCategorizerMigration extends AbstractMigration
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
		$this->table('polymorphic_categories')
			->addTimestamps()
			->addColumn('resource_id', 'integer')
			->addColumn('resource_table', 'string')
			->addColumn('category', 'string')
			->addIndex('resource_table')
			->addIndex('resource_id')
            ->addIndex(['category', 'resource_table', 'resource_id'], ['unique' => true])
			->create();
    }
}
