<?php

declare(strict_types=1);

use Common\Di\Alias\DatabaseIdentifierGenerator;
use Common\Di\Alias\IncomingMessageStore;
use Common\Di\Alias\OutgoingMessageStore;
use Common\Di\Factory\AmqpMessagePublisherFactory;
use Common\Di\Factory\AmqpMessageSubscriberFactory;
use Common\Di\Factory\ClientMongoDbFactory;
use Common\Di\Factory\DatabaseMongoFactory;
use Common\Di\Factory\ErrorMessageTimeoutTrackerMongoDbFactory;
use Common\Di\Factory\IdentifierGeneratorAutoIncrementFactory;
use Common\Di\Factory\IncomingMessageStoreMongoDbFactory;
use Common\Di\Factory\LoggerInterfaceStdoutFactory;
use Common\Di\Factory\MessageDeliveryServiceFactory;
use Common\Di\Factory\MessageRouterFactory;
use Common\Di\Factory\MonologFileLoggerHandlerFactory;
use Common\Di\Factory\OutgoingMessageStoreMongoDbFactory;
use Common\Di\Factory\PublishedMessageTrackerMongoDbFactory;
use Common\Di\Factory\RestFullMiddlewareAbstractFactory;
use MergePullRequestPm\Domain\UseCase\MoneyCollectedHandler;
use MergePullRequestPm\Domain\UseCase\MoneyPayedHandler;
use MergePullRequestPm\Domain\UseCase\PullRequestMarkedAsMergeableHandler;
use MergePullRequestPm\Domain\UseCase\PullRequestMergedHandler;
use MergePullRequestPm\Domain\Provider\PullRequestProvider;
use MergePullRequestPm\Infrastructure\Di\ZendServiceManager\Factory\AbstractMessageListenerFactory;
use MergePullRequestPm\Infrastructure\Di\ZendServiceManager\Factory\MoneyCollectedHandlerFactory;
use MergePullRequestPm\Infrastructure\Di\ZendServiceManager\Factory\MoneyPayedHandlerFactory;
use MergePullRequestPm\Infrastructure\Di\ZendServiceManager\Factory\PullRequestMarkedAsMergeableHandlerFactory;
use MergePullRequestPm\Infrastructure\Di\ZendServiceManager\Factory\PullRequestProviderMongoDbFactory;
use MergePullRequestPm\Infrastructure\Di\ZendServiceManager\Factory\RepositoryMongoDbFactory;
use MongoDB\Client;
use MongoDB\Database;
use Monolog\Formatter\JsonFormatter;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
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
    'service-name'          => 'merge_pull_request_pm',
    'mongo-db'            => [
        'connection' => 'mongodb://mongo:27017',
        'database'   => 'code_review',
    ],
    'logger-handlers'      => [
        'file'      => [
            'level'     => Logger::toMonologLevel(LogLevel::INFO),
            'formatter' => JsonFormatter::class,
            'path'      => __DIR__ . '/../../../../../var/file.log',
        ],
    ],
    'enabled-loggers'      => [
        'file'      => MonologFileLoggerHandlerFactory::class,
    ],
    'rabbitmq'                 => [
        'credentials' => [
            'host'     => 'rabbitmq',
            'vhost'    => 'devhost',
            'login'    => 'devuser',
            'password' => 'devpass',
        ],
        'dead-letter-seconds' => 5,
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
