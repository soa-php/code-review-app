<?php

declare(strict_types=1);

namespace MergePullRequestPm\Infrastructure\Ui\Messaging\Listener;

use Psr\Container\ContainerInterface;
use Soa\MessageStore\Message;
use Soa\MessageStore\Subscriber\Listener\MessageListener;
use Soa\ProcessManager\Application\EventBus;
use Soa\Traceability\Trace;

abstract class AbstractMessageListener implements MessageListener
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var Message
     */
    private $message;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function handle(Message $message): void
    {
        $this->message = $message;

        $this->handleMessage($message);
    }

    protected function eventBus(string $eventBusFqcn): EventBus
    {
        $trace = new Trace(
            $this->message->id(),
            $this->message->occurredOn(),
            $this->message->correlationId(),
            $this->message->causationId(),
            $this->message->replyTo(),
            $this->message->processId()
        );

        return new $eventBusFqcn($this->container, $trace);
    }
}
