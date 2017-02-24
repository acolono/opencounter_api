<?php

/*
 * This file is part of the BenGorUser package.
 *
 * (c) Beñat Espiña <benatespina@gmail.com>
 * (c) Gorka Laucirica <gorka.lauzirika@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SlimCounter\Application\Command\Oauth2;

use BenGorUser\User\Domain\Model\Exception\UserAlreadyExistException;
use BenGorUser\User\Domain\Model\UserPasswordEncoder;
use OAuth2\Storage\Pdo;
use SlimCounter\Application\Service\User\AddClientCommand;
use SlimCounter\Application\Service\User\AddClientRequest;

/**
 * With confirmation sign up user user command handler class.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 * @author Gorka Laucirica <gorka.lauzirika@gmail.com>
 */
class AddClientHandler
{
    /**
     * The user password encoder.
     *
     * @var UserPasswordEncoder
     */
    private $oauth_storage;

    public function __construct(
        Pdo $oauth2_storage
    ) {

        $this->oauth2_storage = $oauth2_storage;
    }

    /**
     * Handles the given command.
     *
     * @param WithConfirmationSignUpUserCommand $aCommand The command
     *
     * @throws UserAlreadyExistException when the user id is already exists
     */
    public function __invoke(
        \SlimCounter\Application\Command\Oauth2\AddClientCommand $aCommand
    ) {

        //return $this->user_repository->all();
        $this->oauth2_storage->setClientDetails(
            $aCommand->clientId(),
            $aCommand->clientSecret(),
            $aCommand->redirectUrl(),
            $aCommand->grantTypes(),
            $aCommand->scopes(),
            $aCommand->userId()
        );
    }
}
