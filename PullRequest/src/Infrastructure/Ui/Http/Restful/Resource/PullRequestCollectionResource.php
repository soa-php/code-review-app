<?php

declare(strict_types=1);

namespace PullRequest\Infrastructure\Ui\Http\Restful\Resource;

use Common\Ui\Http\Restful\Authorization\Token;
use Common\Ui\Http\Restful\Middleware\AbstractRestfulResourceMiddleware;
use PullRequest\Application\PullRequestCommandBus;
use PullRequest\Domain\Event\PullRequestCreationFailed;
use PullRequest\Domain\UseCase\CreatePullRequestCommand;
use Lukasoppermann\Httpstatus\Httpstatuscodes;
use function Martinezdelariva\Functional\match;
use const Martinezdelariva\Functional\_;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Soa\EventSourcing\Command\CommandResponse;
use Soa\EventSourcing\Event\DomainEvent;
use Soa\EventSourcing\Repository\AggregateRootNotFound;
use Zend\Diactoros\Response\EmptyResponse;

class PullRequestCollectionResource extends AbstractRestfulResourceMiddleware
{
    public function post(ServerRequestInterface $request): ResponseInterface
    {
        $command = $this->buildCommand($request);

        $result = $this->commandBus(PullRequestCommandBus::class)->handle($command);

        return $this->buildResponse($request, $result);
    }

    private function buildCommand(ServerRequestInterface $request): CreatePullRequestCommand
    {
        /** @var Token $loggedUser */
        $loggedUser = $request->getAttribute(Token::class);
        $params     = $this->getParamsFromRequest($request);

        return new CreatePullRequestCommand(
            $this->identifierGenerator->nextIdentity(),
            $params->get('code'),
            $loggedUser->userId()
        );
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
                return (new EmptyResponse(Httpstatuscodes::HTTP_CREATED))->withHeader('Location', $domainEvent->streamId());
            },
        ];

        return match($pattern, $result->eventStream()->first());
    }
}
