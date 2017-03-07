<?php
/**
 * AddClientHandler
 *
 * contains a class to handle AddClientCommands
 */
namespace SlimCounter\Application\Command\Oauth2;

use BenGorUser\User\Domain\Model\Exception\UserAlreadyExistException;
use BenGorUser\User\Domain\Model\UserPasswordEncoder;
use OAuth2\Storage\Pdo;
use SlimCounter\Application\Service\User\AddClientCommand;
use SlimCounter\Application\Service\User\AddClientRequest;

/**
 * Class AddClientHandler
 * @package SlimCounter\Application\Command\Oauth2
 */
class AddClientHandler
{
    /**
     * Oauth Storage
     *
     * @see http://bshaffer.github.io/oauth2-server-php-docs/overview/storage/
     * @var
     */
    private $oauth_storage;

    /**
     * Constructor
     *
     * @param Pdo $oauth2_storage
     */
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
