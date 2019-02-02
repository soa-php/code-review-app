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
use Common\Di\Factory\ErrorHandlerMiddlewareFactory;
use Common\Di\Factory\ErrorMessageTimeoutTrackerMongoDbFactory;
use Common\Di\Factory\IdentifierGeneratorAutoIncrementFactory;
use Common\Di\Factory\IncomingMessageStoreMongoDbFactory;
use Common\Di\Factory\LoggerInterfaceStdoutFactory;
use Common\Di\Factory\MessageDeliveryServiceFactory;
use Common\Di\Factory\MessageRouterFactory;
use Common\Di\Factory\OutgoingMessageStoreMongoDbFactory;
use Common\Di\Factory\PublishedMessageTrackerMongoDbFactory;
use Common\Di\Factory\RestFullMiddlewareAbstractFactory;
use Common\Ui\Http\Restful\Authorization\JwtToken\JwtTokenParser;
use Common\Ui\Http\Restful\Authorization\TokenParser;
use Common\Ui\Http\Restful\Middleware\AuthorizationMiddleware;
use Common\Ui\Http\Restful\Middleware\ErrorHandlerMiddleware;
use PullRequest\Application\Projection\PullRequestProjector;
use PullRequest\Infrastructure\Di\ZendServiceManager\Alias\PullRequestRepository;
use PullRequest\Domain\UseCase\ApprovePullRequestCommandHandler;
use PullRequest\Domain\UseCase\AssignPullRequestReviewerCommandHandler;
use PullRequest\Domain\UseCase\CreatePullRequestCommandHandler;
use PullRequest\Domain\UseCase\MergePullRequestCommandHandler;
use PullRequest\Infrastructure\Di\ZendServiceManager\Alias\PullRequestProjectionTable;
use PullRequest\Infrastructure\Di\ZendServiceManager\Factory\PullRequestProjectionTableMongoDbFactory;
use PullRequest\Infrastructure\Di\ZendServiceManager\Factory\PullRequestRepositoryMongoDbFactory;
use MongoDB\Client;
use MongoDB\Database;
use Psr\Log\LoggerInterface;
use PullRequest\Infrastructure\Ui\Http\Restful\AuthorizationRules;
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
            ErrorHandlerMiddleware::class       => ErrorHandlerMiddlewareFactory::class,
        ],
        'invokables' => [
            Clock::class                                   => ClockImpl::class,
            CreatePullRequestCommandHandler::class         => CreatePullRequestCommandHandler::class,
            ApprovePullRequestCommandHandler::class        => ApprovePullRequestCommandHandler::class,
            AssignPullRequestReviewerCommandHandler::class => AssignPullRequestReviewerCommandHandler::class,
            MergePullRequestCommandHandler::class          => MergePullRequestCommandHandler::class,
            PullRequestProjector::class                    => PullRequestProjector::class,
            TokenParser::class                             => JwtTokenParser::class,
        ],
        'abstract_factories' => [
            RestFullMiddlewareAbstractFactory::class,
            AbstractMessageListenerFactory::class,
            ConfigAbstractFactory::class,
        ],
    ],
    'authorization-rules' => AuthorizationRules::getRules(),
    'service-name'        => 'pull_request',
    'mongo-db'            => [
        'connection' => 'mongodb://mongo:27017',
        'database'   => 'pull_request',
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
