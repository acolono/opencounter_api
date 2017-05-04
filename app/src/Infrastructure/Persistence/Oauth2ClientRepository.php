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

    /**
     * Get all Clients.
     *
     * method for listing all registered clients meant for Super admin.
     *
     * @return array
     */
    public function getAllClients()
    {
        $stmt = $this->db->prepare(sprintf(
            'SELECT * from %s',
            $this->config['client_table']
        ));
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function deleteClientById($client_id)
    {

        $stmt = $this->db->prepare(sprintf(
            'DELETE FROM %s WHERE client_id = :client_id',
            $this->config['client_table']
        ));
        $stmt->execute(['client_id' => $client_id]);
    }
}
