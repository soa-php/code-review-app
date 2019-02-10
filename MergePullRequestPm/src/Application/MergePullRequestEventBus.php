<?php

declare(strict_types=1);

namespace MergePullRequestPm\Application;

use Common\Di\Alias\IncomingMessageStore;
use Common\Di\Alias\OutgoingMessageStore;
use Psr\Container\ContainerInterface;
use Soa\Clock\ClockImpl;
use Soa\IdentifierGenerator\UuidIdentifierGenerator;
use Soa\ProcessManager\Application\EventBus;
use Soa\ProcessManager\Application\MiddlewarePipelineFactory;
use Soa\ProcessManager\Domain\DomainEvent;
use Soa\ProcessManager\Domain\Transition;
use Soa\ProcessManager\Infrastructure\Persistence\Repository;
use Soa\ProcessManagerMiddleware\DomainEventHandlerSelectorMiddleware;
use Soa\ProcessManagerMiddleware\PersistMessagesMiddleware;
use Soa\ProcessManagerMiddleware\PersistProcessMiddleware;
use Soa\Traceability\Trace;

class MergePullRequestEventBus implements EventBus
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

    public function handle(DomainEvent $domainEvent): Transition
    {
        $processManagerName = $this->container->get('config')['service-name'];

        $pipeline = MiddlewarePipelineFactory::create(
            new PersistProcessMiddleware($this->container->get(Repository::class)),
            new PersistMessagesMiddleware(
                new ClockImpl(),
                $processManagerName,
                $this->container->get(OutgoingMessageStore::class),
                $this->container->get(IncomingMessageStore::class),
                $this->trace,
                $processManagerName,
                new UuidIdentifierGenerator()
            ),
            new DomainEventHandlerSelectorMiddleware($this->container, $this->container->get(Repository::class))
        );

        return $pipeline($domainEvent);
    }
}
