<?php

declare(strict_types=1);

use Common\Di\Alias\DatabaseIdentifierGenerator;
use Common\Di\Alias\IncomingMessageStore;
use Common\Di\Alias\OutgoingMessageStore;
use Common\Di\Factory\AbstractMessageListenerFactory;
use Common\Di\Factory\AmqpMessagePublisherFactory;
use Common\Di\Factory\AmqpMessageSubscriberFactory;
use Common\Di\Factory\AuthorizationMiddlewareFactory;
use Common\Di\Factory\ClientMongoDbFactory;
use Common\Di\Factory\DatabaseMongoFactory;
use Common\Di\Factory\ErrorMessageTimeoutTrackerMongoDbFactory;
use Common\Di\Factory\IdentifierGeneratorAutoIncrementFactory;
use Common\Di\Factory\IncomingMessageStoreMongoDbFactory;
use Common\Di\Factory\LoggerInterfaceStdoutFactory;
use Common\Di\Factory\MessageDeliveryServiceFactory;
use Common\Di\Factory\MessageRouterFactory;
use Common\Di\Factory\OutgoingMessageStoreMongoDbFactory;
use Common\Di\Factory\PublishedMessageTrackerMongoDbFactory;
use Common\Di\Factory\RestFullMiddlewareAbstractFactory;
use Common\Ui\Http\Restful\Authorization\JwtToken\JwtTokenFactory;
use Common\Ui\Http\Restful\Authorization\JwtToken\JwtTokenParser;
use Common\Ui\Http\Restful\Authorization\TokenFactory;
use Common\Ui\Http\Restful\Authorization\TokenParser;
use Common\Ui\Http\Restful\Authorization\TokenValidator;
use Common\Ui\Http\Restful\Middleware\AuthorizationMiddleware;
use UserIdentity\Application\Projection\UserProjector;
use UserIdentity\Domain\PasswordEncryption;
use UserIdentity\Domain\UseCase\LogUserInWithPasswordCommandHandler;
use UserIdentity\Domain\UseCase\RefreshUserAccessTokenCommandHandler;
use UserIdentity\Infrastructure\Di\ZendServiceManager\Alias\UserRepository;
use UserIdentity\Infrastructure\Di\ZendServiceManager\Alias\UserProjectionTable;
use UserIdentity\Infrastructure\Di\ZendServiceManager\Factory\JwtTokenFactoryFactory;
use UserIdentity\Infrastructure\Di\ZendServiceManager\Factory\JwtTokenValidatorFactory;
use UserIdentity\Infrastructure\Di\ZendServiceManager\Factory\UseCase\RefreshUserAccessTokenCommandHandlerFactory;
use UserIdentity\Infrastructure\Di\ZendServiceManager\Factory\UserProjectionTableMongoDbFactory;
use UserIdentity\Infrastructure\Di\ZendServiceManager\Factory\UserRepositoryMongoDbFactory;
use MongoDB\Client;
use MongoDB\Database;
use Psr\Log\LoggerInterface;
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
use UserIdentity\Infrastructure\Ui\Http\Restful\AuthorizationRules;
use Zend\ServiceManager\AbstractFactory\ConfigAbstractFactory;

return [
    ConfigAbstractFactory::class => [
    ],
    'dependencies' => [
        'aliases' => [
        ],
        'factories'  => [
            Client::class                               => ClientMongoDbFactory::class,
            Database::class                             => DatabaseMongoFactory::class,
            PublishedMessageTracker::class              => PublishedMessageTrackerMongoDbFactory::class,
            MessageDeliveryService::class               => MessageDeliveryServiceFactory::class,
            MessagePublisher::class                     => AmqpMessagePublisherFactory::class,
            IncomingMessageStore::class                 => IncomingMessageStoreMongoDbFactory::class,
            OutgoingMessageStore::class                 => OutgoingMessageStoreMongoDbFactory::class,
            LoggerInterface::class                      => LoggerInterfaceStdoutFactory::class,
            DatabaseIdentifierGenerator::class          => IdentifierGeneratorAutoIncrementFactory::class,
            UserProjectionTable::class                  => UserProjectionTableMongoDbFactory::class,
            UserRepository::class                       => UserRepositoryMongoDbFactory::class,
            MessageRouter::class                        => MessageRouterFactory::class,
            MessageSubscriber::class                    => AmqpMessageSubscriberFactory::class,
            ErrorMessageTimeoutTracker::class           => ErrorMessageTimeoutTrackerMongoDbFactory::class,
            TokenFactory::class                         => JwtTokenFactoryFactory::class,
            TokenValidator::class                       => JwtTokenValidatorFactory::class,
            LogUserInWithPasswordCommandHandler::class  => RegisterUserWithPasswordCommandHandlerFactory::class,
            RefreshUserAccessTokenCommandHandler::class => RefreshUserAccessTokenCommandHandlerFactory::class,
            AuthorizationMiddleware::class              => AuthorizationMiddlewareFactory::class,
        ],
        'invokables' => [
            Clock::class              => ClockImpl::class,
            PasswordEncryption::class => BCryptPasswordEncryption::class,
            TokenParser::class        => JwtTokenParser::class,
            UserProjector::class      => UserProjector::class,
        ],
        'abstract_factories' => [
            RestFullMiddlewareAbstractFactory::class,
            AbstractMessageListenerFactory::class,
            ConfigAbstractFactory::class,
        ],
    ],
    'authorization-rules' => AuthorizationRules::getRules(),
    'service-name'        => 'user_identity',
    'mongo-db'            => [
        'connection' => 'mongodb://mongo:27017',
        'database'   => 'user_identity',
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
