<?php

namespace SlimCounter\Application\Service\Oauth2;

use SlimCounter\Application\Query\ListClientsHandler;

class ListClientsService
{

    /**
     * Handler.
     *
     * @var \SlimCounter\Application\Query\ListClientsHandler
     */

    private $handler;

    /**
     * ListClientsService constructor.
     *
     * @param \SlimCounter\Application\Query\ListClientsHandler $aHandler
     */
    public function __construct(ListClientsHandler $aHandler)
    {
        $this->handler = $aHandler;
    }

    /**
     * Execute
     *
     * Will handle Oauth2 Client queries.
     * {@inheritdoc}
     *
     * @param null $request
     *
     * @return mixed
     */
    public function execute($request = null)
    {
        // this service executes a query and therefore returns something
        return $this->handler->__invoke($request);
    }
}
