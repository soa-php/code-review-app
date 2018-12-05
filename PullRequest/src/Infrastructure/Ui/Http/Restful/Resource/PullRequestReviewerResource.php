<?php

declare(strict_types=1);

namespace PullRequest\Infrastructure\Ui\Http\Restful\Resource;

use PullRequest\Application\PullRequestCommandBus;
use PullRequest\Domain\Event\PullRequestReviewerAssignationFailed;
use PullRequest\Domain\UseCase\AssignPullRequestReviewerCommand;
use Lukasoppermann\Httpstatus\Httpstatuscodes;
use function Martinezdelariva\Functional\match;
use function Martinezdelariva\Hydrator\hydrate;
use const Martinezdelariva\Functional\_;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Soa\EventSourcing\Command\CommandResponse;
use Soa\EventSourcing\Event\DomainEvent;
use Zend\Diactoros\Response\EmptyResponse;

class PullRequestReviewerResource extends AbstractRestfulResourceMiddleware
{
    public function put(ServerRequestInterface $request): ResponseInterface
    {
        $command = $this->assignPullRequestReviewerCommand($request->getBody()->getContents())->withAggregateRootId($request->getAttribute('id'));

        $result = $this->commandBus(PullRequestCommandBus::class)->handle($command);

        return $this->buildResponse($request, $result);
    }

    private function assignPullRequestReviewerCommand(string $body): AssignPullRequestReviewerCommand
    {
        return hydrate(AssignPullRequestReviewerCommand::class, json_decode($body, true));
    }

    private function buildResponse(ServerRequestInterface $request, CommandResponse $result): ResponseInterface
    {
        $pattern = [
            PullRequestReviewerAssignationFailed::class => function (PullRequestReviewerAssignationFailed $event) use ($request) {
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
