<?php
/**
 * Created by PhpStorm.
 * User: rosenstrauch
 * Date: 2/15/17
 * Time: 10:55 PM
 */

namespace SlimCounter\Application\Service\Oauth2;

use Ddd\Application\Service\ApplicationService;
use SlimCounter\Application\Command\Oauth2\AddClientHandler;

class AddClientService implements ApplicationService
{
    /**
     * The command handler.
     *
     * @var WithConfirmationSignUpUserHandler
     */
    private $handler;

    /**
     * Constructor.
     *
     * @param WithConfirmationSignUpUserHandler $aHandler The command handler
     */
    public function __construct(AddClientHandler $aHandler)
    {
        $this->handler = $aHandler;
    }

    /**
     * {@inheritdoc}
     */
    public function execute($request = null)
    {
        $this->handler->__invoke($request);
    }
}
