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
          'username' => 'librarian',
//          TODO: hash passwords in db
          'password' => 'secret',
          'first_name' => 'librarian',
          'last_name' => 'librarian',
          'scope' => 'write:counters read:counters',
          'email' => 'a@b.c',
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
