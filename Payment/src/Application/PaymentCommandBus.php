<?php

declare(strict_types=1);

namespace Payment\Application;

use Payment\Application\Projection\PaymentProjector;
use Payment\Infrastructure\Di\ZendServiceManager\Alias\IncomingMessageStore;
use Payment\Infrastructure\Di\ZendServiceManager\Alias\PaymentRepository;
use Psr\Container\ContainerInterface;
use Payment\Infrastructure\Di\ZendServiceManager\Alias\OutgoingMessageStore;
use Payment\Infrastructure\Di\ZendServiceManager\Alias\PaymentProjectionTable;
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

class PaymentCommandBus implements CommandBus
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
            new PersistProjectionMiddleware($this->container->get(PaymentProjectionTable::class)),
            new PersistMessagesMiddleware(
                new ClockImpl(),
                $this->container->get(OutgoingMessageStore::class),
                $this->container->get(IncomingMessageStore::class),
                new UuidIdentifierGenerator(),
                $boundedContextName,
                $boundedContextName,
                $this->trace
            ),
            new ProjectEventStreamOnProjectionMiddleware($this->container->get(PaymentProjectionTable::class), $this->container->get(PaymentProjector::class)),
            new CommandHandlerSelectorMiddleware($this->container, $this->container->get(PaymentRepository::class))
        );

        return $pipeline($command);
    }
}
