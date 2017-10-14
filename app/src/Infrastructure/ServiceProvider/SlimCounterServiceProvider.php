<?php

namespace SlimCounter\Infrastructure\ServiceProvider;

use OpenCounter\Application\Command\Counter\CounterAddHandler;
use OpenCounter\Application\Command\Counter\CounterIncrementValueHandler;
use OpenCounter\Application\Command\Counter\CounterRemoveHandler;
use OpenCounter\Application\Command\Counter\CounterResetValueHandler;
use OpenCounter\Application\Command\Counter\CounterSetStatusHandler;
use OpenCounter\Application\Query\Counter\CounterOfIdHandler;
use OpenCounter\Application\Query\Counter\CounterOfNameHandler;
use OpenCounter\Application\Query\Counter\CountersListService;
use OpenCounter\Application\Service\Counter\CounterAddService;
use OpenCounter\Application\Service\Counter\CounterBuildService;
use OpenCounter\Application\Service\Counter\CounterIncrementValueService;
use OpenCounter\Application\Service\Counter\CounterRemoveService;
use OpenCounter\Application\Service\Counter\CounterResetValueService;
use OpenCounter\Application\Service\Counter\CounterSetStatusService;
use OpenCounter\Application\Service\Counter\CounterViewService;
use OpenCounter\Infrastructure\Factory\Counter\CounterFactory;
use OpenCounter\Infrastructure\Persistence\Sql\Repository\Counter\SqlCounterRepository;
use OpenCounter\Infrastructure\Persistence\Sql\SqlManager;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Class OpenCounterServiceProvider.
 *
 * @package OpenCounter\Infrastructure\ServiceProvider
 */
class SlimCounterServiceProvider implements ServiceProviderInterface
{

    /**
     * The provides array is a way to let the container
     * know that a service is provided by this service
     * provider. Every service that is registered via
     * this service provider must have an alias added
     * to this array or it will be ignored.
     *
     * @var array
     */
    protected $provides = [
        'counter_mapper',
        'counter_repository',
        'counter_build_service',
        'CounterViewService',
        'CounterIncrementValueService',
        'CounterRemoveService',
        'add_counter_application_service',
        'CounterAddService',
        'CounterResetValueService',
    ];

    /**
     * Register()
     *
     * This is where the magic happens, within the method you can
     * access the container and register or retrieve anything
     * that you need to, but remember, every alias registered
     * within this method must be declared in the `$provides` array.
     *
     * @param \Pimple\Container $pimple
     */
    public function register(Container $pimple)
    {
        /**
         * Sql Manager.
         *
         * @param $pimple
         *
         * @return \OpenCounter\Infrastructure\Persistence\Sql\SqlManager
         */
        $pimple['counter_mapper'] = function ($pimple) {
            $counter_mapper = new SqlManager($pimple['pdo']);

            return $counter_mapper;
        };
        /**
         * counter_repository
         *
         * @param $pimple
         *
         * @return \OpenCounter\Infrastructure\Persistence\Sql\Repository\Counter\SqlCounterRepository
         */

        $pimple['counter_repository'] = function ($pimple) {
            $counter_mapper = $pimple['counter_mapper'];
            $counter_repository = new SqlCounterRepository($counter_mapper);

            return $counter_repository;
        };

        /**
         * counter_build_service
         */
        $pimple['counter_build_service'] = $pimple->factory(function ($pimple) {

            $factory = new CounterFactory();

            $counter_build_service = new CounterBuildService(
                $pimple['counter_repository'],
                $factory,
                $pimple['logger']
            );

            return $counter_build_service;
        });

        /**
         * Application service used to view a counter.
         *
         * @param $pimple
         *
         * @return \OpenCounter\Application\Service\Counter\CounterViewService
         */
        $pimple['CounterViewService'] = function ($pimple) {
            $CounterViewService = new CounterViewService(
                new CounterOfIdHandler(
                    $pimple['counter_repository']
                )
            );

            return $CounterViewService;
        };

        /**
         * Application service used to view a counter.
         *
         * @param $pimple
         *
         * @return \OpenCounter\Application\Service\Counter\CounterViewService
         */
        $pimple['CounterViewUiService'] = function ($pimple) {
            $CounterViewUiService = new CounterViewService(
                new CounterOfNameHandler(
                    $pimple['counter_repository']
                )
            );

            return $CounterViewUiService;
        };

        /**
         * CounterIncrementValueService.
         */
        $pimple['CounterIncrementValueService'] = $pimple->factory(function (
            $pimple
        ) {
            $CounterIncrementValueService = new CounterIncrementValueService(
                new CounterIncrementValueHandler(
                    $pimple['counter_repository']
                )
            );

            return $CounterIncrementValueService;
        });

        /**
         * Application service used to Remove Counters
         */
        $pimple['CounterRemoveService'] = $pimple->factory(function ($pimple) {
            $CounterRemoveService = new CounterRemoveService(
                new CounterRemoveHandler(
                    $pimple['counter_repository']
                )
            );

            return $CounterRemoveService;
        });

        /**
         * Application service used to create new counters
         *
         * @param $container
         *
         * @return \OpenCounter\Application\Service\Counter\CounterAddService
         */
        $pimple['CounterAddService'] = $pimple->factory(function ($pimple) {
            $CounterAddService = new CounterAddService(
                new CounterAddHandler(
                    $pimple['counter_repository'],
                    $pimple['counter_build_service']
                )
            );

            return $CounterAddService;
        });

        /**
         * Application service used to Lock and unlock
         */
        $pimple['CounterSetStatusService'] = $pimple->factory(function ($pimple
        ) {
            $CounterSetStatusService = new CounterSetStatusService(
                new CounterSetStatusHandler(
                    $pimple['counter_repository'],
                    $pimple['counter_build_service']
                )
            );

            return $CounterSetStatusService;
        });

        /**
         * Application service used to Reset counters
         */
        $pimple['CounterResetValueService'] = $pimple->factory(function ($pimple
        ) {
            $CounterResetValueService = new CounterResetValueService(
                new CounterResetValueHandler(
                    $pimple['counter_repository'],
                    $pimple['counter_build_service']
                )
            );

            return $CounterResetValueService;
        });

        /**
         * Application service used to List Counters
         */
        $pimple['CountersListService'] = $pimple->factory(function ($pimple
        ) {
            $CounterSetStatusService = new CountersListService(
                new CountersListHandler(
                    $pimple['counter_repository'],
                    $pimple['counter_build_service']
                )
            );

            return $CountersListService;
        });

    }
}
