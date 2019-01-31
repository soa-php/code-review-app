<?php

declare(strict_types=1);

namespace UserIdentity\Infrastructure\Ui\Http\Restful\Resource;

use Lukasoppermann\Httpstatus\Httpstatuscodes;
use function Martinezdelariva\Functional\match;
use function Martinezdelariva\Hydrator\hydrate;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use UserIdentity\Application\UserIdentityCommandBus;
use UserIdentity\Domain\Event\LogUserInWithPasswordFailed;
use UserIdentity\Domain\Event\UserWithPasswordLoggedIn;
use Soa\EventSourcing\Command\CommandResponse;
use UserIdentity\Domain\UseCase\LogUserInWithPasswordCommand;
use Zend\Diactoros\Response\JsonResponse;

class UserWithPasswordCollectionResource extends AbstractRestfulResourceMiddleware
{
    public function post(ServerRequestInterface $request): ResponseInterface
    {
        $command = $this->buildCommand($request->getBody()->getContents())->withAggregateRootId($this->identifierGenerator->nextIdentity());

        $result = $this->commandBus(UserIdentityCommandBus::class)->handle($command);

        return $this->buildResponse($request, $result);
    }

    private function buildCommand(string $body): LogUserInWithPasswordCommand
    {
        return hydrate(LogUserInWithPasswordCommand::class, json_decode($body, true));
    }

    private function buildResponse(ServerRequestInterface $request, CommandResponse $result): ResponseInterface
    {
        $pattern = [
            LogUserInWithPasswordFailed::class => function (LogUserInWithPasswordFailed $event) use ($request) {
                return $this->problemDetailsResponseFactory->createResponse(
                    $request,
                    Httpstatuscodes::HTTP_CONFLICT,
                    $event->reason()
                );
            },

            UserWithPasswordLoggedIn::class => function (UserWithPasswordLoggedIn $domainEvent) use ($request) {
                $responseContent = [
                    'access-token'  => $domainEvent->accessToken(),
                    'refresh-token' => $domainEvent->refreshToken(),
                ];

                return (new JsonResponse($responseContent))
                    ->withStatus(Httpstatuscodes::HTTP_OK)
                    ->withHeader('Location', $domainEvent->streamId());
            },
        ];

        return match($pattern, $result->eventStream()->first());
    }
}
