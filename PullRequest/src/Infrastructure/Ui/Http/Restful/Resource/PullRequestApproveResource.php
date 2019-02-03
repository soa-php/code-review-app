<?php

declare(strict_types=1);

namespace PullRequest\Infrastructure\Ui\Http\Restful\Resource;

use Common\Ui\Http\Restful\Authorization\Token;
use Common\Ui\Http\Restful\Middleware\AbstractRestfulResourceMiddleware;
use Lukasoppermann\Httpstatus\Httpstatuscodes;
use function Martinezdelariva\Functional\match;
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
        $command = $this->buildCommand($request);

        $result = $this->commandBus(PullRequestCommandBus::class)->handle($command);

        return $this->buildResponse($request, $result);
    }

    private function buildCommand(ServerRequestInterface $request): ApprovePullRequestCommand
    {
        /** @var Token $loggedUser */
        $loggedUser = $request->getAttribute(Token::class);

        return new ApprovePullRequestCommand($request->getAttribute('id'), $loggedUser->userId());
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
