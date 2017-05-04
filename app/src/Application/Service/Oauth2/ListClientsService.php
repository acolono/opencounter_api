<?php

namespace SlimCounter\Application\Service\Oauth2;

use SlimCounter\Application\Query\ListClientsHandler;

/**
 * Class ListClientsService
 *
 * @package SlimCounter\Application\Service\Oauth2
 */
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
   * Execute.
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
        // This service executes a query and therefore returns something.
        return $this->handler->__invoke($request);
    }
}
