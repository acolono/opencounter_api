<?php

namespace SlimCounter\Application\Query;

use SlimCounter\Infrastructure\Persistence\Oauth2ClientRepository;

class ListClientsHandler
{

    protected $oauth2ClientRepository;

    public function __construct(
      Oauth2ClientRepository $oauth2ClientRepository
    ) {
        $this->oauth2ClientRepository = $oauth2ClientRepository;
    }

    public function __invoke(ListClientsQuery $aQuery)
    {
        try {
            $oauthClients = $this->oauth2ClientRepository->getAllClients();
        } catch (\Exception $e) {
            throw new NoOauthClientsFoundException('No clients found');
        }

        return $oauthClients;
    }
}
