<?php

declare(strict_types=1);

namespace UserIdentity\Infrastructure\Ui\Http\Restful\Resource;

use Lukasoppermann\Httpstatus\Httpstatuscodes;
use function Martinezdelariva\Functional\match;
use function Martinezdelariva\Hydrator\hydrate;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Soa\EventSourcing\Command\CommandResponse;
use UserIdentity\Application\UserIdentityCommandBus;
use UserIdentity\Domain\Event\RefreshUserAccessTokenFailed;
use UserIdentity\Domain\Event\UserAccessTokenRefreshed;
use UserIdentity\Domain\UseCase\RefreshUserAccessTokenCommand;
use Zend\Diactoros\Response\JsonResponse;

class UserRefreshTokenResource extends AbstractRestfulResourceMiddleware
{
    public function post(ServerRequestInterface $request): ResponseInterface
    {
        $command = $this->buildCommand($request->getBody()->getContents())->withAggregateRootId($this->identifierGenerator->nextIdentity());

        $result = $this->commandBus(UserIdentityCommandBus::class)->handle($command);

        return $this->buildResponse($request, $result);
    }

    private function buildCommand(string $body): RefreshUserAccessTokenCommand
    {
        return hydrate(RefreshUserAccessTokenCommand::class, json_decode($body, true));
    }

    private function buildResponse(ServerRequestInterface $request, CommandResponse $result): ResponseInterface
    {
        $pattern = [
            RefreshUserAccessTokenFailed::class => function (RefreshUserAccessTokenFailed $event) use ($request) {
                return $this->problemDetailsResponseFactory->createResponse(
                    $request,
                    Httpstatuscodes::HTTP_CONFLICT,
                    $event->reason()
                );
            },

            UserAccessTokenRefreshed::class => function (UserAccessTokenRefreshed $domainEvent) use ($request) {
                $responseContent = [
                    'access-token'  => $domainEvent->accessToken(),
                ];

                return (new JsonResponse($responseContent))
                    ->withStatus(Httpstatuscodes::HTTP_OK);
            },
        ];

        return match($pattern, $result->eventStream()->first());
    }
}
