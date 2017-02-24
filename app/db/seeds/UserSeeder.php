<?php

use Phinx\Seed\AbstractSeed;

class UserSeeder extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeders is available here:
     * http://docs.phinx.org/en/latest/seeding.html
     */
    public function run()
    {
        $data = array(
          array(
            'id' => 'librarian',
//          TODO: hash passwords in db
            'password' => 'secret',
            'confirmation_token_token' => 'secret',
            'remember_password_token_token' => 'secret',
            'email' => 'a@b.c',
            'roles' => '{"0":"admin","1":"user"}',
            'invitation_token_token' => 'a@b.c',
          ),
          array(
            'id' => 'testuser',
//          TODO: hash passwords in db
            'password' => 'testuser',
            'confirmation_token_token' => 'testuser',
            'remember_password_token_token' => 'testuser',
            'email' => 'user@b.c',
            'roles' => '{"1":"user"}',
            'invitation_token_token' => 'a@b.c',
          ),

        );

        $user = $this->table('user');
        $user->insert($data)
          ->save();

        $data = array(
        array(
          'username' => 'librarian',
//          TODO: hash passwords in db
          'password' => 'secret',
          'first_name' => 'librarian',
          'last_name' => 'librarian',
          'scope' => 'write:counters read:counters',
          'email' => 'a@b.c',
          'email_verified' => 1,
        ),
          array(
            'username' => 'oauthuser',
//          TODO: hash passwords in db
            'password' => 'oauthuser',
            'first_name' => 'oauthuser',
            'last_name' => 'oauthuser',
            'scope' => 'write:counters read:counters',
            'email' => 'aoauthuser@b.c',
            'email_verified' => 1,
          ),
      );

      $oauth_users = $this->table('oauth_users');
      $oauth_users->insert($data)
        ->save();


      $data = [
        [
          'client_id'    => 'librarian',
          'user_id' => 'librarian',
          'client_secret'    => 'secret',
          'grant_types' => 'implicit password client_credentials refresh_token authorization_code',
          'scope' => 'write:counters read:counters',
          'redirect_uri' => '/receive-code',
        ],
        [
          'client_id'    => 'student',
          'user_id' => 'student',
          'client_secret'    => 's3cr3t',
          'grant_types' => 'implicit password client_credentials refresh_token authorization_code',
          'scope' => 'write:counters read:counters',
          'redirect_uri' => '172.25.0.2/o2c.html',
        ],
        [
          'client_id' => 'swagger-editor',
          'user_id' => 'swagger-editor',
          'client_secret' => 's3cr3t',
          'grant_types' => 'implicit password client_credentials refresh_token authorization_code',
          'scope' => 'write:counters read:counters',
          'redirect_uri' => '172.25.0.2/o2c.html',
        ],

      ];

      $oauth_clients = $this->table('oauth_clients');
      $oauth_clients->insert($data)
        ->save();


      $data = [
        [
          'scope' => 'readCounter',
          'is_default' => 1,

        ],
        [
          'scope' => 'writeCounter',
          'is_default' => 0,

        ],
      ];
      $scopes = $this->table('oauth_scopes');
      $scopes->insert($data)
        ->save();
    }
}
