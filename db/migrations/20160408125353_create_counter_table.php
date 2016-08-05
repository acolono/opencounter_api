<?php

use Phinx\Migration\AbstractMigration;

class CreateCounterTable extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
        $counts = $this->table('counters');
        $counts->addColumn('uuid', 'string', ['limit' => 40])
               ->addColumn('name', 'string', ['limit' => 40])
               ->addColumn('password', 'string', ['limit' => 50])
               ->addColumn('value','integer')
               ->addIndex(array('id'), array('unique' => true, 'name' => 'idx_name'))
               ->create();
    }
}
