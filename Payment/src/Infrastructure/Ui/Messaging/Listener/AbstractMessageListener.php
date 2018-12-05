<?php

declare(strict_types=1);

namespace Payment\Infrastructure\Ui\Messaging\Listener;

use Psr\Container\ContainerInterface;
use Soa\EventSourcing\Command\CommandBus;
use Soa\MessageStore\Message;
use Soa\MessageStore\Subscriber\Listener\MessageListener;
use Soa\Traceability\Trace;

abstract class AbstractMessageListener implements MessageListener
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function commandBus(string $commandBusFqcn, Message $message): CommandBus
    {
        $trace = new Trace(
            $message->id(),
            \DateTimeImmutable::createFromFormat('U.u', (string) microtime(true))->format('Y-m-d H:i:s.u'),
            $message->correlationId(),
            $message->causationId(),
            $message->replyTo(),
            $message->processId()
        );

        return new $commandBusFqcn($this->container, $trace);
    }
}
