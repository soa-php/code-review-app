<?php

declare(strict_types=1);

use Common\Di\Alias\DatabaseIdentifierGenerator;
use Common\Di\Alias\IncomingMessageStore;
use Common\Di\Alias\OutgoingMessageStore;
use Common\Di\Factory\AbstractMessageListenerFactory;
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
use MessagePublisher\Infrastructure\Di\ZendServiceManager\Factory\AmqpMessagePublisherFactory;
use Monolog\Formatter\JsonFormatter;
use Monolog\Logger;
use Payment\Application\Projection\PaymentProjector;
use Payment\Infrastructure\Di\ZendServiceManager\Alias\PaymentRepository;
use Payment\Domain\PaymentProvider;
use Payment\Domain\UseCase\CollectMoneyCommandHandler;
use Payment\Domain\UseCase\PayMoneyCommandHandler;
use Payment\Infrastructure\Di\ZendServiceManager\Alias\PaymentProjectionTable;
use Payment\Infrastructure\Di\ZendServiceManager\Factory\PaymentProjectionTableMongoDbFactory;
use Payment\Infrastructure\Di\ZendServiceManager\Factory\PaymentRepositoryMongoDbFactory;
use MongoDB\Client;
use MongoDB\Database;
use Payment\Infrastructure\Domain\PaymentProviderFake;
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
use Zend\ServiceManager\AbstractFactory\ConfigAbstractFactory;

return [
    ConfigAbstractFactory::class => [
        CollectMoneyCommandHandler::class => [PaymentProvider::class],
        PayMoneyCommandHandler::class     => [PaymentProvider::class],
    ],
    'dependencies' => [
        'factories'  => [
            Client::class                       => ClientMongoDbFactory::class,
            Database::class                     => DatabaseMongoFactory::class,
            PublishedMessageTracker::class      => PublishedMessageTrackerMongoDbFactory::class,
            MessageDeliveryService::class       => MessageDeliveryServiceFactory::class,
            MessagePublisher::class             => AmqpMessagePublisherFactory::class,
            IncomingMessageStore::class         => IncomingMessageStoreMongoDbFactory::class,
            OutgoingMessageStore::class         => OutgoingMessageStoreMongoDbFactory::class,
            LoggerInterface::class              => LoggerInterfaceStdoutFactory::class,
            DatabaseIdentifierGenerator::class  => IdentifierGeneratorAutoIncrementFactory::class,
            PaymentProjectionTable::class       => PaymentProjectionTableMongoDbFactory::class,
            MessageRouter::class                => MessageRouterFactory::class,
            PaymentRepository::class            => PaymentRepositoryMongoDbFactory::class,
            MessageSubscriber::class            => AmqpMessageSubscriberFactory::class,
            ErrorMessageTimeoutTracker::class   => ErrorMessageTimeoutTrackerMongoDbFactory::class,
        ],
        'invokables' => [
            PaymentProvider::class                         => PaymentProviderFake::class,
            Clock::class                                   => ClockImpl::class,
            PaymentProjector::class                        => PaymentProjector::class,
        ],
        'abstract_factories' => [
            RestFullMiddlewareAbstractFactory::class,
            ConfigAbstractFactory::class,
            AbstractMessageListenerFactory::class,
        ],
    ],
    'message-recipients' => ['payment', 'pull_request', 'merge_pull_request_pm', 'user_identity'],
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
    'mongo-db'            => [
        'connection' => 'mongodb://mongo:27017',
        'database'   => 'code_review',
    ],
    'rabbitmq'        => [
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
