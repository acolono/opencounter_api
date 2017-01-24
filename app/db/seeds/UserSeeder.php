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
          'client_id'    => 'librarian',
          'client_secret'    => 'secret',
          'scope'    => 'counterAdmin',
          'redirect_uri'    => '/receive-code',
        ),
        array(
          'client_id'    => 'student',
          'client_secret'    => 's3cr3t',
          'scope'    => '',
          'redirect_uri'    => '',
        ),

      );

      $oauth_clients = $this->table('oauth_clients');
      $oauth_clients->insert($data)
        ->save();
    }
}
