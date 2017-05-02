<?php
/**
 * Created by PhpStorm.
 * User: rosenstrauch
 * Date: 5/3/17
 * Time: 12:45 AM
 */

namespace SlimCounter\Infrastructure;

use OAuth2\Storage\Pdo;

class Oauth2ClientStorage extends Pdo
{
    // TODO: dont store anything in plain text

    // TODO: provide method for listing all registered clients meant for superadmin

}