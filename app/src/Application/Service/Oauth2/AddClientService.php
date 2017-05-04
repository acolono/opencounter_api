<?php

namespace SlimCounter\Application\Service\Oauth2;

use OpenCounter\Application\Service\Counter\ApplicationService;
use SlimCounter\Application\Command\Oauth2\AddClientHandler;

/**
 * Class AddClientService.
 *
 * @package SlimCounter\Application\Service\Oauth2
 */
class AddClientService implements ApplicationService
{
  /**
   * CommandHandler.
   *
   * @var object
   */
    private $handler;

  /**
   * Constructor.
   *
   * @param \SlimCounter\Application\Command\Oauth2\AddClientHandler $aHandler
   */
    public function __construct(AddClientHandler $aHandler)
    {
        $this->handler = $aHandler;
    }

  /**
   * Execute()
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
