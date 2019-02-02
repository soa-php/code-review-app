<?php

declare(strict_types=1);

use PullRequest\Application\Projection\PullRequestProjector;
use PullRequest\Infrastructure\Di\ZendServiceManager\Alias\PullRequestRepository;
use PullRequest\Domain\UseCase\ApprovePullRequestCommandHandler;
use PullRequest\Domain\UseCase\AssignPullRequestReviewerCommandHandler;
use PullRequest\Domain\UseCase\CreatePullRequestCommandHandler;
use PullRequest\Domain\UseCase\MergePullRequestCommandHandler;
use PullRequest\Infrastructure\Di\ZendServiceManager\Alias\DatabaseIdentifierGenerator;
use PullRequest\Infrastructure\Di\ZendServiceManager\Alias\PullRequestProjectionTable;
use PullRequest\Infrastructure\Di\ZendServiceManager\Factory\AbstractMessageListenerFactory;
use PullRequest\Infrastructure\Di\ZendServiceManager\Factory\AmqpMessageSubscriberFactory;
use PullRequest\Infrastructure\Di\ZendServiceManager\Factory\AuthorizationMiddlewareFactory;
use PullRequest\Infrastructure\Di\ZendServiceManager\Factory\ErrorMessageTimeoutTrackerMongoDbFactory;
use PullRequest\Infrastructure\Di\ZendServiceManager\Factory\MessageRouterFactory;
use PullRequest\Infrastructure\Di\ZendServiceManager\Factory\PullRequestProjectionTableMongoDbFactory;
use PullRequest\Infrastructure\Di\ZendServiceManager\Factory\PullRequestRepositoryMongoDbFactory;
use PullRequest\Infrastructure\Di\ZendServiceManager\Factory\RestFullMiddlewareAbstractFactory;
use MongoDB\Client;
use MongoDB\Database;
use Psr\Log\LoggerInterface;
use PullRequest\Infrastructure\Di\ZendServiceManager\Factory\AmqpMessagePublisherFactory;
use PullRequest\Infrastructure\Di\ZendServiceManager\Factory\ClientMongoDbFactory;
use PullRequest\Infrastructure\Di\ZendServiceManager\Factory\DatabaseMongoFactory;
use PullRequest\Infrastructure\Di\ZendServiceManager\Factory\IdentifierGeneratorAutoIncrementFactory;
use PullRequest\Infrastructure\Di\ZendServiceManager\Factory\IncomingMessageStoreMongoDbFactory;
use PullRequest\Infrastructure\Di\ZendServiceManager\Factory\LoggerInterfaceStdoutFactory;
use PullRequest\Infrastructure\Di\ZendServiceManager\Factory\MessageDeliveryServiceFactory;
use PullRequest\Infrastructure\Di\ZendServiceManager\Factory\OutgoingMessageStoreMongoDbFactory;
use PullRequest\Infrastructure\Di\ZendServiceManager\Factory\PublishedMessageTrackerMongoDbFactory;
use PullRequest\Infrastructure\Di\ZendServiceManager\Alias\IncomingMessageStore;
use PullRequest\Infrastructure\Di\ZendServiceManager\Alias\OutgoingMessageStore;
use PullRequest\Infrastructure\Ui\Http\Restful\Authorization\AuthorizationRules;
use PullRequest\Infrastructure\Ui\Http\Restful\Authorization\TokenParser;
use PullRequest\Infrastructure\Ui\Http\Restful\Middleware\AuthorizationMiddleware;
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
    ],
    'dependencies' => [
        'aliases' => [
        ],
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
            PullRequestProjectionTable::class   => PullRequestProjectionTableMongoDbFactory::class,
            PullRequestRepository::class        => PullRequestRepositoryMongoDbFactory::class,
            MessageRouter::class                => MessageRouterFactory::class,
            MessageSubscriber::class            => AmqpMessageSubscriberFactory::class,
            ErrorMessageTimeoutTracker::class   => ErrorMessageTimeoutTrackerMongoDbFactory::class,
            AuthorizationMiddleware::class      => AuthorizationMiddlewareFactory::class,
        ],
        'invokables' => [
            Clock::class                                   => ClockImpl::class,
            CreatePullRequestCommandHandler::class         => CreatePullRequestCommandHandler::class,
            ApprovePullRequestCommandHandler::class        => ApprovePullRequestCommandHandler::class,
            AssignPullRequestReviewerCommandHandler::class => AssignPullRequestReviewerCommandHandler::class,
            MergePullRequestCommandHandler::class          => MergePullRequestCommandHandler::class,
            PullRequestProjector::class                    => PullRequestProjector::class,
            TokenParser::class                             => TokenParser\JwtTokenParser::class,
        ],
        'abstract_factories' => [
            RestFullMiddlewareAbstractFactory::class,
            AbstractMessageListenerFactory::class,
            ConfigAbstractFactory::class,
        ],
    ],
    'authorization-rules' => AuthorizationRules::getRules(),
    'bounded-context' => 'pull_request',
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
