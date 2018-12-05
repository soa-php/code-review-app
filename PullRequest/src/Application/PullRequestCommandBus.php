<?php

declare(strict_types=1);

namespace PullRequest\Application;

use PullRequest\Application\Projection\PullRequestProjector;
use Psr\Container\ContainerInterface;
use PullRequest\Infrastructure\Di\ZendServiceManager\Alias\IncomingMessageStore;
use PullRequest\Infrastructure\Di\ZendServiceManager\Alias\OutgoingMessageStore;
use PullRequest\Infrastructure\Di\ZendServiceManager\Alias\PullRequestProjectionTable;
use PullRequest\Infrastructure\Di\ZendServiceManager\Alias\PullRequestRepository;
use Soa\Clock\ClockImpl;
use Soa\EventSourcing\Command\Command;
use Soa\EventSourcing\Command\CommandBus;
use Soa\EventSourcing\Command\CommandResponse;
use Soa\EventSourcingMiddleware\Middleware\CommandHandlerSelectorMiddleware;
use Soa\EventSourcingMiddleware\Middleware\MiddlewarePipelineFactory;
use Soa\EventSourcingMiddleware\Middleware\PersistMessagesMiddleware;
use Soa\EventSourcingMiddleware\Middleware\PersistProjectionMiddleware;
use Soa\EventSourcingMiddleware\Middleware\ProjectEventStreamOnProjectionMiddleware;
use Soa\IdentifierGenerator\UuidIdentifierGenerator;
use Soa\Traceability\Trace;

class PullRequestCommandBus implements CommandBus
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var Trace
     */
    private $trace;

    public function __construct(ContainerInterface $container, Trace $trace)
    {
        $this->container = $container;
        $this->trace     = $trace;
    }

    public function handle(Command $command): CommandResponse
    {
        $boundedContextName = $this->container->get('config')['bounded-context'];

        $pipeline = MiddlewarePipelineFactory::create(
            new PersistProjectionMiddleware($this->container->get(PullRequestProjectionTable::class)),
            new PersistMessagesMiddleware(
                new ClockImpl(),
                $this->container->get(OutgoingMessageStore::class),
                $this->container->get(IncomingMessageStore::class),
                new UuidIdentifierGenerator(),
                $boundedContextName,
                $boundedContextName,
                $this->trace
            ),
            new ProjectEventStreamOnProjectionMiddleware($this->container->get(PullRequestProjectionTable::class), $this->container->get(PullRequestProjector::class)),
            new CommandHandlerSelectorMiddleware($this->container, $this->container->get(PullRequestRepository::class))
        );

        return $pipeline($command);
    }
}
