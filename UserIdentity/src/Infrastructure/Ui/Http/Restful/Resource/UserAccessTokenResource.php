<?php

declare(strict_types=1);

namespace UserIdentity\Infrastructure\Ui\Http\Restful\Resource;

use Lukasoppermann\Httpstatus\Httpstatuscodes;
use function Martinezdelariva\Functional\match;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Soa\EventSourcing\Command\CommandResponse;
use UserIdentity\Application\UserIdentityCommandBus;
use UserIdentity\Domain\Event\RefreshUserAccessTokenFailed;
use UserIdentity\Domain\Event\UserAccessTokenRefreshed;
use UserIdentity\Domain\UseCase\RefreshUserAccessTokenCommand;
use Zend\Diactoros\Response\JsonResponse;

class UserAccessTokenResource extends AbstractRestfulResourceMiddleware
{
    public function put(ServerRequestInterface $request): ResponseInterface
    {
        $command = $this->buildCommand($request->getBody()->getContents())->withAggregateRootId($request->getAttribute('id'));

        $result = $this->commandBus(UserIdentityCommandBus::class)->handle($command);

        return $this->buildResponse($request, $result);
    }

    private function buildCommand(string $body): RefreshUserAccessTokenCommand
    {
        return new RefreshUserAccessTokenCommand();
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
