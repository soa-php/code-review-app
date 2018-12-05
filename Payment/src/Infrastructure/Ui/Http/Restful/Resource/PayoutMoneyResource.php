<?php

declare(strict_types=1);

namespace Payment\Infrastructure\Ui\Http\Restful\Resource;

use Soa\EventSourcing\Command\CommandResponse;
use Soa\EventSourcing\Event\DomainEvent;
use Lukasoppermann\Httpstatus\Httpstatuscodes;
use const Martinezdelariva\Functional\_;
use function Martinezdelariva\Functional\match;
use function Martinezdelariva\Hydrator\hydrate;
use Payment\Application\PaymentCommandBus;
use Payment\Domain\Event\PayMoneyFailed;
use Payment\Domain\UseCase\PayMoneyCommand;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\EmptyResponse;

class PayoutMoneyResource extends AbstractRestfulResourceMiddleware
{
    public function post(ServerRequestInterface $request): ResponseInterface
    {
        $command = $command = $this->buildCommand($request->getBody()->getContents())->withAggregateRootId($this->identifierGenerator->nextIdentity());

        $result = $this->commandBus(PaymentCommandBus::class)->handle($command);

        return $this->buildResponse($request, $result);
    }

    private function buildCommand(string $body): PayMoneyCommand
    {
        return hydrate(PayMoneyCommand::class, json_decode($body, true));
    }

    private function buildResponse(ServerRequestInterface $request, CommandResponse $result): ResponseInterface
    {
        $pattern = [
            PayMoneyFailed::class => function (PayMoneyFailed $event) use ($request) {
                return $this->problemDetailsResponseFactory->createResponse(
                    $request,
                    Httpstatuscodes::HTTP_CONFLICT,
                    $event->reason()
                );
            },

            _ => function (DomainEvent $domainEvent) use ($request) {
                return new EmptyResponse(Httpstatuscodes::HTTP_NO_CONTENT);
            },
        ];

        return match($pattern, $result->eventStream()->first());
    }
}
