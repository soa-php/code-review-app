<?php

declare(strict_types=1);

namespace UserIdentity\Application;

use Common\Di\Alias\IncomingMessageStore;
use Common\Di\Alias\OutgoingMessageStore;
use UserIdentity\Application\Projection\UserProjector;
use Psr\Container\ContainerInterface;
use UserIdentity\Infrastructure\Di\ZendServiceManager\Alias\UserProjectionTable;
use UserIdentity\Infrastructure\Di\ZendServiceManager\Alias\UserRepository;
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

class UserIdentityCommandBus implements CommandBus
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
        $boundedContextName = $this->container->get('config')['service-name'];

        $pipeline = MiddlewarePipelineFactory::create(
            new PersistProjectionMiddleware($this->container->get(UserProjectionTable::class)),
            new PersistMessagesMiddleware(
                new ClockImpl(),
                $this->container->get(OutgoingMessageStore::class),
                $this->container->get(IncomingMessageStore::class),
                new UuidIdentifierGenerator(),
                $boundedContextName,
                $boundedContextName,
                $this->trace
            ),
            new ProjectEventStreamOnProjectionMiddleware($this->container->get(UserProjectionTable::class), $this->container->get(UserProjector::class)),
            new CommandHandlerSelectorMiddleware($this->container, $this->container->get(UserRepository::class))
        );

        return $pipeline($command);
    }
}
