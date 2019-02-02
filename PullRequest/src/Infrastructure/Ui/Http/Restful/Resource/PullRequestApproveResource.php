<?php

declare(strict_types=1);

namespace PullRequest\Infrastructure\Ui\Http\Restful\Resource;

use Common\Ui\Http\Restful\Middleware\AbstractRestfulResourceMiddleware;
use Lukasoppermann\Httpstatus\Httpstatuscodes;
use function Martinezdelariva\Functional\match;
use function Martinezdelariva\Hydrator\hydrate;
use const Martinezdelariva\Functional\_;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use PullRequest\Application\PullRequestCommandBus;
use PullRequest\Domain\Event\ApprovePullRequestFailed;
use PullRequest\Domain\UseCase\ApprovePullRequestCommand;
use Soa\EventSourcing\Command\CommandResponse;
use Soa\EventSourcing\Event\DomainEvent;
use Zend\Diactoros\Response\EmptyResponse;

class PullRequestApproveResource extends AbstractRestfulResourceMiddleware
{
    public function put(ServerRequestInterface $request): ResponseInterface
    {
        $command = $this->buildCommand($request->getBody()->getContents())->withAggregateRootId($request->getAttribute('id'));

        $result = $this->commandBus(PullRequestCommandBus::class)->handle($command);

        return $this->buildResponse($request, $result);
    }

    private function buildCommand(string $body): ApprovePullRequestCommand
    {
        return hydrate(ApprovePullRequestCommand::class, json_decode($body, true));
    }

    private function buildResponse(ServerRequestInterface $request, CommandResponse $result): ResponseInterface
    {
        $pattern = [
            ApprovePullRequestFailed::class => function (ApprovePullRequestFailed $event) use ($request) {
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
