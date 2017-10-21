<?php

use Phinx\Migration\AbstractMigration;

class SetupOauth extends AbstractMigration
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



      $oauth_clients = $this->table('oauth_clients', array('id' => false, 'primary_key' => array('client_id')));
      $oauth_clients
        ->addColumn('client_id', 'string', ['limit' => 40, 'comment' => 'A unique client identifier'])
        ->addColumn('user_id', 'string', ['limit' => 40, 'null' => true, 'comment' => 'OAUTH_USERS.USER_ID'])
        ->addColumn('grant_types', 'string', ['limit' => 80, 'comment' => 'Space-delimited list of permitted grant types'])
        ->addColumn('client_secret', 'string', ['limit' => 40, 'comment' => 'Used to secure Client Credentials Grant'])
        ->addColumn('scope', 'string', ['limit' => 40, 'comment' => 'Space-delimited list of permitted scopes'])
        ->addColumn('redirect_uri', 'string', ['limit' => 50, 'comment' => 'Redirect URI used for Authorization Grant'])
        ->create();


      $oauth_access_tokens = $this->table('oauth_access_tokens', array('id' => false, 'primary_key' => array('access_token')));
      $oauth_access_tokens
        ->addColumn('access_token', 'string', ['limit' => 80, 'comment' => 'System generated access token'])
        ->addColumn('client_id', 'string', ['limit' => 80, 'comment' => 'OAUTH_CLIENTS.CLIENT_ID'])
        ->addColumn('user_id', 'string', ['limit' => 80, 'null' => true, 'comment' => 'OAUTH_USERS.USER_ID'])
        ->addColumn('expires', 'timestamp', ['limit' => 80, 'null' => false,  'comment' => 'When the token becomes invalid'])
        ->addColumn('scope', 'string', ['limit' => 80,  'comment' => 'Space-delimited list of scopes token can access'])
        ->create();



      $oauth_users = $this->table('oauth_users');
      $oauth_users
        ->addColumn('username', 'string', ['limit' => 40])
        ->addColumn('password', 'string', ['limit' => 40])
        ->addColumn('first_name', 'string', ['limit' => 40])
        ->addColumn('last_name', 'string', ['limit' => 50])
        ->addColumn('email', 'string', ['limit' => 50])
        ->addColumn('email_verified', 'boolean')
        ->addColumn('scope', 'string', ['limit' => 50, 'comment' => 'Space-delimited list scopes token can access'])
        ->create();

      $oauth_refresh_tokens = $this->table('oauth_refresh_tokens', array('id' => false, 'primary_key' => array('refresh_token')));
      $oauth_refresh_tokens
        ->addColumn('refresh_token', 'string', ['limit' => 40, 'comment' => 'System generated refresh token'])
        ->addColumn('client_id', 'string', ['limit' => 40, 'comment' => 'OAUTH_CLIENTS.CLIENT_ID'])
        ->addColumn('user_id', 'string', ['limit' => 40, 'null' => true, 'comment' => 'OAUTH_USERS.USER_ID'])
        ->addColumn('expires', 'timestamp', ['limit' => 40, 'null' => false, 'comment' => 'When the token becomes invalid'])
        ->addColumn('scope', 'string', ['limit' => 50, 'comment' => 'Space-delimited list scopes token can access'])

        ->create();



      $oauth_scopes = $this->table('oauth_scopes', array('id' => false, 'primary_key' => array('scope')));
      $oauth_scopes
        ->addColumn('is_default', 'boolean')
        ->addColumn('scope', 'string', ['limit' => 50, 'comment' => 'Space-delimited list scopes token can access'])

        ->create();


      $oauth_authorization_codes = $this->table('oauth_authorization_codes', array('id' => false, 'primary_key' => array('authorization_code')));
      $oauth_authorization_codes
        ->addColumn('authorization_code', 'string', ['limit' => 40, 'null' => false, 'comment' =>  'System generated authorization code'])
        ->addColumn('client_id', 'string', ['limit' => 40, 'comment' => 'A unique client identifier'])
        ->addColumn('user_id', 'string', ['limit' => 40, 'null' => true, 'comment' => 'OAUTH_USERS.USER_ID'])
        ->addColumn('scope', 'string', ['limit' => 40, 'comment' => 'Space-delimited list of permitted scopes'])
        ->addColumn('redirect_uri', 'string', ['limit' => 50, 'comment' => 'Redirect URI used for Authorization Grant'])
        ->addColumn('id_token', 'string', ['limit' => 50, 'comment' => 'JSON web token used for OpenID Connect'])
        ->addColumn('expires', 'timestamp', ['limit' => 40, 'null' => false, 'comment' => 'When the token becomes invalid'])

        ->create();





    }

  /**
   * Migrate Up.
   */
  public function up() {

  }

  /**
   * Migrate Down.
   */
  public function down() {

  }
}
