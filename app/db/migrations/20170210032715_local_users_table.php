<?php

use Phinx\Migration\AbstractMigration;

class LocalUsersTable extends AbstractMigration
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
        $user = $this->table('user', array('id' => false, 'primary_key' => array('id')));
        $user
          ->addColumn('id', 'string', ['limit' => 36, 'comment' => 'A unique user identifier'])
          ->addColumn('confirmation_token_token', 'string', ['limit' => 40, 'comment' => 'OAUTH_USERS.USER_ID', 'null' => true])
          ->addColumn('confirmation_token_created_on', 'datetime',['null' => true])
          ->addColumn('created_on', 'datetime')
          ->addColumn('email', 'string', ['limit' => 40, 'comment' => 'Used to secure Client Credentials Grant'])
          ->addColumn('invitation_token_token', 'string', ['limit' => 40, 'null' => true])
          ->addColumn('password', 'string', ['limit' => 40, 'comment' => 'passwordfield', 'null' => true])
          ->addColumn('salt', 'string', ['limit' => 40, 'comment' => 'encrption for password', 'null' => true])
          ->addColumn('remember_password_token_token', 'string', ['limit' => 40, 'null' => true])
          ->addColumn('roles', 'text', ['limit' => 40, 'comment' => '(DC2Type:user_roles)'])
          ->addColumn('invitation_token_created_on', 'datetime',['null' => true])
          ->addColumn('last_login', 'datetime', ['null' => true])
          ->addColumn('updated_on', 'datetime')
          ->addColumn('remember_password_token_created_on', 'datetime', ['null' => true])
          ->create();


    }
}
