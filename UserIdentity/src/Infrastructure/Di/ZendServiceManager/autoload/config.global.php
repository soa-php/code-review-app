<?php

declare(strict_types=1);

use UserIdentity\Application\Projection\UserProjector;
use UserIdentity\Domain\PasswordEncryption;
use UserIdentity\Domain\TokenFactory;
use UserIdentity\Domain\UseCase\LogUserInWithPasswordCommandHandler;
use UserIdentity\Infrastructure\Di\ZendServiceManager\Alias\UserRepository;
use UserIdentity\Infrastructure\Di\ZendServiceManager\Alias\DatabaseIdentifierGenerator;
use UserIdentity\Infrastructure\Di\ZendServiceManager\Alias\UserProjectionTable;
use UserIdentity\Infrastructure\Di\ZendServiceManager\Factory\AbstractMessageListenerFactory;
use UserIdentity\Infrastructure\Di\ZendServiceManager\Factory\AmqpMessageSubscriberFactory;
use UserIdentity\Infrastructure\Di\ZendServiceManager\Factory\ErrorMessageTimeoutTrackerMongoDbFactory;
use UserIdentity\Infrastructure\Di\ZendServiceManager\Factory\JwtTokenBuilderFactory;
use UserIdentity\Infrastructure\Di\ZendServiceManager\Factory\MessageRouterFactory;
use UserIdentity\Infrastructure\Di\ZendServiceManager\Factory\UserProjectionTableMongoDbFactory;
use UserIdentity\Infrastructure\Di\ZendServiceManager\Factory\UserRepositoryMongoDbFactory;
use UserIdentity\Infrastructure\Di\ZendServiceManager\Factory\RestFullMiddlewareAbstractFactory;
use MongoDB\Client;
use MongoDB\Database;
use Psr\Log\LoggerInterface;
use UserIdentity\Infrastructure\Di\ZendServiceManager\Factory\AmqpMessagePublisherFactory;
use UserIdentity\Infrastructure\Di\ZendServiceManager\Factory\ClientMongoDbFactory;
use UserIdentity\Infrastructure\Di\ZendServiceManager\Factory\DatabaseMongoFactory;
use UserIdentity\Infrastructure\Di\ZendServiceManager\Factory\IdentifierGeneratorAutoIncrementFactory;
use UserIdentity\Infrastructure\Di\ZendServiceManager\Factory\IncomingMessageStoreMongoDbFactory;
use UserIdentity\Infrastructure\Di\ZendServiceManager\Factory\LoggerInterfaceStdoutFactory;
use UserIdentity\Infrastructure\Di\ZendServiceManager\Factory\MessageDeliveryServiceFactory;
use UserIdentity\Infrastructure\Di\ZendServiceManager\Factory\OutgoingMessageStoreMongoDbFactory;
use UserIdentity\Infrastructure\Di\ZendServiceManager\Factory\PublishedMessageTrackerMongoDbFactory;
use UserIdentity\Infrastructure\Di\ZendServiceManager\Alias\IncomingMessageStore;
use UserIdentity\Infrastructure\Di\ZendServiceManager\Alias\OutgoingMessageStore;
use Soa\Clock\Clock;
use Soa\Clock\ClockImpl;
use Soa\MessageStore\Publisher\MessageDeliveryService;
use Soa\MessageStore\Publisher\MessagePublisher;
use Soa\MessageStore\Publisher\PublishedMessageTracker;
use Soa\MessageStore\Subscriber\Error\ErrorMessageTimeoutTracker;
use Soa\MessageStore\Subscriber\Listener\MessageRouter;
use Soa\MessageStore\Subscriber\MessageSubscriber;
use UserIdentity\Infrastructure\Di\ZendServiceManager\Factory\UseCase\RegisterUserWithPasswordCommandHandlerFactory;
use UserIdentity\Infrastructure\Domain\BCryptPasswordEncryption;
use UserIdentity\Infrastructure\Domain\JwtTokenFactory;
use Zend\ServiceManager\AbstractFactory\ConfigAbstractFactory;

return [
    ConfigAbstractFactory::class => [
    ],
    'dependencies' => [
        'aliases' => [
        ],
        'factories'  => [
            Client::class                                 => ClientMongoDbFactory::class,
            Database::class                               => DatabaseMongoFactory::class,
            PublishedMessageTracker::class                => PublishedMessageTrackerMongoDbFactory::class,
            MessageDeliveryService::class                 => MessageDeliveryServiceFactory::class,
            MessagePublisher::class                       => AmqpMessagePublisherFactory::class,
            IncomingMessageStore::class                   => IncomingMessageStoreMongoDbFactory::class,
            OutgoingMessageStore::class                   => OutgoingMessageStoreMongoDbFactory::class,
            LoggerInterface::class                        => LoggerInterfaceStdoutFactory::class,
            DatabaseIdentifierGenerator::class            => IdentifierGeneratorAutoIncrementFactory::class,
            UserProjectionTable::class                    => UserProjectionTableMongoDbFactory::class,
            UserRepository::class                         => UserRepositoryMongoDbFactory::class,
            MessageRouter::class                          => MessageRouterFactory::class,
            MessageSubscriber::class                      => AmqpMessageSubscriberFactory::class,
            ErrorMessageTimeoutTracker::class             => ErrorMessageTimeoutTrackerMongoDbFactory::class,
            TokenFactory::class                           => JwtTokenBuilderFactory::class,
            LogUserInWithPasswordCommandHandler::class    => RegisterUserWithPasswordCommandHandlerFactory::class,
        ],
        'invokables' => [
            Clock::class              => ClockImpl::class,
            PasswordEncryption::class => BCryptPasswordEncryption::class,
            UserProjector::class      => UserProjector::class,
        ],
        'abstract_factories' => [
            RestFullMiddlewareAbstractFactory::class,
            AbstractMessageListenerFactory::class,
            ConfigAbstractFactory::class,
        ],
    ],
    'bounded-context' => 'user_identity',
    'mongo-db'        => 'mongodb://mongo:27017',
    'rabbitmq'        => [
        'credentials' => [
            'host'     => 'rabbitmq',
            'vhost'    => 'devhost',
            'login'    => 'devuser',
            'password' => 'devpass',
        ],
    ],
    'jwt' => [
        JwtTokenFactory::ISSUER                   => 'user_identity_bc',
        JwtTokenFactory::ACCESS_TOKEN_EXPIRATION  => '900',
        JwtTokenFactory::REFRESH_TOKEN_EXPIRATION => '86400',
        JwtTokenFactory::KEY                      => 'some random key',
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
