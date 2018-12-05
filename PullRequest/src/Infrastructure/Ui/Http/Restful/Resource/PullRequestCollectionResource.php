<?php

declare(strict_types=1);

namespace PullRequest\Infrastructure\Ui\Http\Restful\Resource;

use PullRequest\Application\PullRequestCommandBus;
use PullRequest\Domain\Event\PullRequestCreationFailed;
use PullRequest\Domain\UseCase\CreatePullRequestCommand;
use Lukasoppermann\Httpstatus\Httpstatuscodes;
use function Martinezdelariva\Functional\match;
use function Martinezdelariva\Hydrator\hydrate;
use const Martinezdelariva\Functional\_;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Soa\EventSourcing\Command\CommandResponse;
use Soa\EventSourcing\Event\DomainEvent;
use Soa\EventSourcing\Repository\AggregateRootNotFound;
use Zend\Diactoros\Response\JsonResponse;

class PullRequestCollectionResource extends AbstractRestfulResourceMiddleware
{
    public function post(ServerRequestInterface $request): ResponseInterface
    {
        $command = $this->buildCommand($request)->withAggregateRootId($this->identifierGenerator->nextIdentity());

        $result = $this->commandBus(PullRequestCommandBus::class)->handle($command);

        return $this->buildResponse($request, $result);
    }

    private function buildCommand(ServerRequestInterface $request): CreatePullRequestCommand
    {
        return hydrate(CreatePullRequestCommand::class, json_decode($request->getBody()->getContents(), true));
    }

    private function buildResponse(ServerRequestInterface $request, CommandResponse $result): ResponseInterface
    {
        $pattern = [
            PullRequestCreationFailed::class => function (PullRequestCreationFailed $event) use ($request) {
                return $this->problemDetailsResponseFactory->createResponse(
                    $request,
                    Httpstatuscodes::HTTP_CONFLICT,
                    $event->reason()
                );
            },

            AggregateRootNotFound::class => function (AggregateRootNotFound $event) use ($request) {
                return $this->problemDetailsResponseFactory->createResponse(
                    $request,
                    Httpstatuscodes::HTTP_NOT_FOUND,
                    $event->reason()
                );
            },

            _ => function (DomainEvent $domainEvent) {
                return (new JsonResponse([], Httpstatuscodes::HTTP_CREATED))->withHeader('Location', $domainEvent->streamId());
            },
        ];

        return match($pattern, $result->eventStream()->first());
    }
}
