<?php
/**
 * Created by PhpStorm.
 * User: rosenstrauch
 * Date: 5/3/17
 * Time: 12:45 AM
 */

namespace SlimCounter\Infrastructure\Persistence;

use OAuth2\Storage\Pdo;

class Oauth2ClientRepository extends Pdo
{
    // TODO: dont store anything in plain text

    // TODO: provide method for listing all registered clients meant for superadmin

    public function getAllClients()
    {
        $stmt = $this->db->prepare(sprintf(
            'SELECT * from %s',
            $this->config['client_table']
        ));
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
