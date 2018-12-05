<?php

declare(strict_types=1);

use MergePullRequestPm\Domain\UseCase\MoneyCollectedHandler;
use MergePullRequestPm\Domain\UseCase\MoneyPayedHandler;
use MergePullRequestPm\Domain\UseCase\PullRequestMarkedAsMergeableHandler;
use MergePullRequestPm\Domain\UseCase\PullRequestMergedHandler;
use MergePullRequestPm\Domain\Provider\PullRequestProvider;
use MergePullRequestPm\Infrastructure\Di\ZendServiceManager\Factory\AbstractMessageListenerFactory;
use MergePullRequestPm\Infrastructure\Di\ZendServiceManager\Factory\AmqpMessageSubscriberFactory;
use MergePullRequestPm\Infrastructure\Di\ZendServiceManager\Factory\ErrorMessageTimeoutTrackerMongoDbFactory;
use MergePullRequestPm\Infrastructure\Di\ZendServiceManager\Factory\MoneyCollectedHandlerFactory;
use MergePullRequestPm\Infrastructure\Di\ZendServiceManager\Factory\MoneyPayedHandlerFactory;
use MergePullRequestPm\Infrastructure\Di\ZendServiceManager\Factory\PullRequestMarkedAsMergeableHandlerFactory;
use MergePullRequestPm\Infrastructure\Di\ZendServiceManager\Factory\PullRequestProviderMongoDbFactory;
use MergePullRequestPm\Infrastructure\Di\ZendServiceManager\Factory\RepositoryMongoDbFactory;
use MergePullRequestPm\Infrastructure\Di\ZendServiceManager\Alias\DatabaseIdentifierGenerator;
use MergePullRequestPm\Infrastructure\Di\ZendServiceManager\Factory\MessageRouterFactory;
use MergePullRequestPm\Infrastructure\Di\ZendServiceManager\Factory\RestFullMiddlewareAbstractFactory;
use MongoDB\Client;
use MongoDB\Database;
use Psr\Log\LoggerInterface;
use MergePullRequestPm\Infrastructure\Di\ZendServiceManager\Factory\AmqpMessagePublisherFactory;
use MergePullRequestPm\Infrastructure\Di\ZendServiceManager\Factory\ClientMongoDbFactory;
use MergePullRequestPm\Infrastructure\Di\ZendServiceManager\Factory\DatabaseMongoFactory;
use MergePullRequestPm\Infrastructure\Di\ZendServiceManager\Factory\IdentifierGeneratorAutoIncrementFactory;
use MergePullRequestPm\Infrastructure\Di\ZendServiceManager\Factory\IncomingMessageStoreMongoDbFactory;
use MergePullRequestPm\Infrastructure\Di\ZendServiceManager\Factory\LoggerInterfaceStdoutFactory;
use MergePullRequestPm\Infrastructure\Di\ZendServiceManager\Factory\MessageDeliveryServiceFactory;
use MergePullRequestPm\Infrastructure\Di\ZendServiceManager\Factory\OutgoingMessageStoreMongoDbFactory;
use MergePullRequestPm\Infrastructure\Di\ZendServiceManager\Factory\PublishedMessageTrackerMongoDbFactory;
use MergePullRequestPm\Infrastructure\Di\ZendServiceManager\Alias\IncomingMessageStore;
use MergePullRequestPm\Infrastructure\Di\ZendServiceManager\Alias\OutgoingMessageStore;
use Soa\Clock\Clock;
use Soa\Clock\ClockImpl;
use Soa\MessageStore\Publisher\MessageDeliveryService;
use Soa\MessageStore\Publisher\MessagePublisher;
use Soa\MessageStore\Publisher\PublishedMessageTracker;
use Soa\MessageStore\Subscriber\Error\ErrorMessageTimeoutTracker;
use Soa\MessageStore\Subscriber\Listener\MessageRouter;
use Soa\MessageStore\Subscriber\MessageSubscriber;
use Soa\ProcessManager\Infrastructure\Persistence\Repository;
use Zend\ServiceManager\AbstractFactory\ConfigAbstractFactory;

return [
    ConfigAbstractFactory::class => [
    ],
    'dependencies' => [
        'aliases' => [
        ],
        'factories'  => [
            Client::class                              => ClientMongoDbFactory::class,
            Database::class                            => DatabaseMongoFactory::class,
            PublishedMessageTracker::class             => PublishedMessageTrackerMongoDbFactory::class,
            MessageDeliveryService::class              => MessageDeliveryServiceFactory::class,
            MessagePublisher::class                    => AmqpMessagePublisherFactory::class,
            IncomingMessageStore::class                => IncomingMessageStoreMongoDbFactory::class,
            Repository::class                          => RepositoryMongoDbFactory::class,
            PullRequestMarkedAsMergeableHandler::class => PullRequestMarkedAsMergeableHandlerFactory::class,
            OutgoingMessageStore::class                => OutgoingMessageStoreMongoDbFactory::class,
            LoggerInterface::class                     => LoggerInterfaceStdoutFactory::class,
            DatabaseIdentifierGenerator::class         => IdentifierGeneratorAutoIncrementFactory::class,
            MessageRouter::class                       => MessageRouterFactory::class,
            MessageSubscriber::class                   => AmqpMessageSubscriberFactory::class,
            PullRequestProvider::class                 => PullRequestProviderMongoDbFactory::class,
            MoneyPayedHandler::class                   => MoneyPayedHandlerFactory::class,
            MoneyCollectedHandler::class               => MoneyCollectedHandlerFactory::class,
            ErrorMessageTimeoutTracker::class          => ErrorMessageTimeoutTrackerMongoDbFactory::class,
        ],
        'invokables' => [
            Clock::class                                          => ClockImpl::class,
            PullRequestMergedHandler::class                       => PullRequestMergedHandler::class,
        ],
        'abstract_factories' => [
            RestFullMiddlewareAbstractFactory::class,
            ConfigAbstractFactory::class,
            AbstractMessageListenerFactory::class,
        ],
    ],
    'process-manager'          => 'merge_pull_request_pm',
    'managed-bounded-contexts' => ['pull_request', 'payment'],
    'mongo-db'                 => 'mongodb://mongo:27017',
    'rabbitmq'                 => [
        'credentials' => [
            'host'     => 'rabbitmq',
            'vhost'    => 'devhost',
            'login'    => 'devuser',
            'password' => 'devpass',
        ],
    ],
    // Toggle the configuration cache. Set this to boolean false, or remove the
    // directive, to disable configuration caching. Toggling development mode
    // will also disable it by default; clear the configuration cache using
    // `composer clear-config-cache`.
//    ConfigAggregator::ENABLE_CACHE => true,

    // Enable debugging; typically used to provide debugging information within templates.
    'debug' => false,

    'zend-expressive' => [
        // Provide templates for the error handling middleware to use when
        // generating responses.
        'error_handler' => [
            'template_404'   => 'error::404',
            'template_error' => 'error::error',
        ],
    ],
];
