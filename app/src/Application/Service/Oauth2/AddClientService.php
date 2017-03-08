<?php
/**
 * Created by PhpStorm.
 * User: rosenstrauch
 * Date: 2/15/17
 * Time: 10:55 PM
 */

namespace SlimCounter\Application\Service\Oauth2;

use OpenCounter\Application\Service\Counter\ApplicationService;
use SlimCounter\Application\Command\Oauth2\AddClientHandler;

/**
 * Class AddClientService
 * @package SlimCounter\Application\Service\Oauth2
 */
class AddClientService implements ApplicationService
{
    /**
     * CommandHandler
     * @var
     */
    private $handler;

    /**
     * Constructor
     * @param AddClientHandler $aHandler
     */
    public function __construct(AddClientHandler $aHandler)
    {
        $this->handler = $aHandler;
    }

    /**
     * execute()
     *
     * {@inheritdoc}
     *
     * @param null $request
     */
    public function execute($request = null)
    {
        $this->handler->__invoke($request);
    }
}
