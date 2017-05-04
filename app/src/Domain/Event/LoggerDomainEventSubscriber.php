<?php

namespace SlimCounter\Domain\Event;

use Ddd\Domain\DomainEventSubscriber;
use Elastica\Client;
use JMS\Serializer\SerializerBuilder;
use Monolog\Handler\ElasticSearchHandler;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Processor\MemoryPeakUsageProcessor;
use Monolog\Processor\MemoryUsageProcessor;
use Monolog\Processor\WebProcessor;

/**
 * Class LoggerDomainEventSubscriber.
 *
 * @package SlimCounter\Domain\Event
 */
class LoggerDomainEventSubscriber implements DomainEventSubscriber
{
  /**
   * Logger.
   *
   * @var \Monolog\Logger
   */
    private $logger;

  /**
   * Serializer.
   *
   * @var \JMS\Serializer\Serializer
   */
    private $serializer;

  /**
   * Constructor.
   */
    public function __construct()
    {
        $this->logger = new Logger('main');
        $this->logger->pushHandler(new StreamHandler('/tmp/app.log'));

        $options = array(
        'index' => 'last_wishes_logs',
        'type' => 'log_entry',
        );

        $this->logger->pushHandler(new ElasticSearchHandler(
            new Client(),
            $options
        ));
        $this->logger->pushProcessor(new WebProcessor());
        $this->logger->pushProcessor(new MemoryUsageProcessor());
        $this->logger->pushProcessor(new MemoryPeakUsageProcessor());

        $this->serializer = SerializerBuilder::create()->build();
    }

  /**
   * Handle.
   *
   * @param $aDomainEvent
   */
    public function handle($aDomainEvent)
    {
        $domainEventInArray = json_decode($this->serializer->serialize(
            $aDomainEvent,
            'json'
        ), true);

        try {
            $this->logger->addInfo(
                get_class($aDomainEvent),
                $domainEventInArray + [
                'name' => get_class($aDomainEvent),
                'occurred_on' => $aDomainEvent->occurredOn(),
                ]
            );
        } catch (\Exception $e) {
        }
    }

  /**
   * IsSubscribedTo.
   *
   * @param $aDomainEvent
   *
   * @return bool
   */
    public function isSubscribedTo($aDomainEvent)
    {
        return true;
    }
}
