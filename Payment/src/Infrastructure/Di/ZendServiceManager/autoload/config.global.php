<?php

declare(strict_types=1);

use Payment\Application\Projection\PaymentProjector;
use Payment\Infrastructure\Di\ZendServiceManager\Alias\PaymentRepository;
use Payment\Domain\PaymentProvider;
use Payment\Domain\UseCase\CollectMoneyCommandHandler;
use Payment\Domain\UseCase\PayMoneyCommandHandler;
use Payment\Infrastructure\Di\ZendServiceManager\Alias\DatabaseIdentifierGenerator;
use Payment\Infrastructure\Di\ZendServiceManager\Alias\PaymentProjectionTable;
use Payment\Infrastructure\Di\ZendServiceManager\Factory\AbstractMessageListenerFactory;
use Payment\Infrastructure\Di\ZendServiceManager\Factory\AmqpMessageSubscriberFactory;
use Payment\Infrastructure\Di\ZendServiceManager\Factory\ErrorMessageTimeoutTrackerMongoDbFactory;
use Payment\Infrastructure\Di\ZendServiceManager\Factory\MessageRouterFactory;
use Payment\Infrastructure\Di\ZendServiceManager\Factory\PaymentProjectionTableMongoDbFactory;
use Payment\Infrastructure\Di\ZendServiceManager\Factory\PaymentRepositoryMongoDbFactory;
use Payment\Infrastructure\Di\ZendServiceManager\Factory\RestFullMiddlewareAbstractFactory;
use MongoDB\Client;
use MongoDB\Database;
use Payment\Infrastructure\Domain\PaymentProviderFake;
use Psr\Log\LoggerInterface;
use Payment\Infrastructure\Di\ZendServiceManager\Factory\AmqpMessagePublisherFactory;
use Payment\Infrastructure\Di\ZendServiceManager\Factory\ClientMongoDbFactory;
use Payment\Infrastructure\Di\ZendServiceManager\Factory\DatabaseMongoFactory;
use Payment\Infrastructure\Di\ZendServiceManager\Factory\IdentifierGeneratorAutoIncrementFactory;
use Payment\Infrastructure\Di\ZendServiceManager\Factory\IncomingMessageStoreMongoDbFactory;
use Payment\Infrastructure\Di\ZendServiceManager\Factory\LoggerInterfaceStdoutFactory;
use Payment\Infrastructure\Di\ZendServiceManager\Factory\MessageDeliveryServiceFactory;
use Payment\Infrastructure\Di\ZendServiceManager\Factory\OutgoingMessageStoreMongoDbFactory;
use Payment\Infrastructure\Di\ZendServiceManager\Factory\PublishedMessageTrackerMongoDbFactory;
use Payment\Infrastructure\Di\ZendServiceManager\Alias\IncomingMessageStore;
use Payment\Infrastructure\Di\ZendServiceManager\Alias\OutgoingMessageStore;
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
    'bounded-context' => 'payment',
    'mongo-db'        => 'mongodb://mongo:27017',
    'rabbitmq'        => [
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
