<?php

namespace SlimCounter\Application\Service\Counter;

use OpenCounter\Domain\Model\Counter\Counter;
use OpenCounter\Http\CounterBuildService;
use OpenCounter\Infrastructure\Persistence\InMemory\Repository\Counter\InMemoryCounterRepository;
use SlimCounter\Application\Command\Counter\CounterRemoveCommand;

class DeleteCounterServiceTest extends \PHPUnit_Framework_TestCase
{
    private $CounterRepository;

    private $deleteCounterService;

    private $dummyCounter;

    public function setUp()
    {
        $this->setupCounterRepository();

        $this->deleteCounterService = new CounterRemoveService(
            $this->CounterRepository
        );
    }

    private function setupCounterRepository()
    {
        $this->CounterRepository = new InMemoryCounterRepository();

        //
        $this->dummyCounter = new CounterBuildService($this->CounterRepository, $this->CounterFactory);
        $this->CounterRepository->add($this->dummyCounter);
    }

    /**
     * @test
     * @expectedException OpenCounter\Domain\Model\Counter\CounterDoesNotExistException
     */
    public function removingNonExistingCounterThrowsException()
    {
        $this->deleteCounterService->execute(
            new CounterRemoveCommand('non-existent')
        );
    }

    /**
     * @test
     */
    public function itShouldRemoveCounter()
    {
        $this->deleteCounterService->execute(
            new DeleteCounterRequest(
                $this->dummyCounter->id()->id(),
                $this->dummyUser->id()->id()
            )
        );

        $this->assertNull($this->CounterRepository->ofId($this->dummyCounter->id()));
    }
}
