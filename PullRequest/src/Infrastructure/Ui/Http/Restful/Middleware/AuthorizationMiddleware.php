<?php

declare(strict_types=1);

namespace PullRequest\Infrastructure\Ui\Http\Restful\Middleware;

use Lukasoppermann\Httpstatus\Httpstatuscodes;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use PullRequest\Infrastructure\Ui\Http\Restful\Authorization\AuthorizationService;
use PullRequest\Infrastructure\Ui\Http\Restful\Authorization\TokenParser;
use Zend\Diactoros\Response\JsonResponse;
use Zend\Expressive\Router\RouteResult;

class AuthorizationMiddleware implements MiddlewareInterface
{
    /**
     * @var AuthorizationService
     */
    private $authorizationService;

    /**
     * @var TokenParser
     */
    private $tokenParser;

    public function __construct(AuthorizationService $authorizationService, TokenParser $tokenParser)
    {
        $this->authorizationService = $authorizationService;
        $this->tokenParser          = $tokenParser;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $route = $request->getAttribute(RouteResult::class, false);
        if ($route instanceof RouteResult) {
            $routeName = $route->getMatchedRoute()->getName();

            if (!$this->authorizationService->isAuthRequiredForRoute($routeName, $request->getMethod())) {
                return $handler->handle($request);
            }

            if (empty($request->getHeaderLine('Authorization'))) {
                return new JsonResponse('', Httpstatuscodes::HTTP_UNAUTHORIZED);
            }

            $token =  $this->tokenParser->parse($request->getHeaderLine('Authorization'));

            if (!$this->authorizationService->isUserAuthorizedForRoute($token->roles(), $routeName, $request->getMethod())) {
                return new JsonResponse('', Httpstatuscodes::HTTP_UNAUTHORIZED);
            }

            $request = $request->withAttribute('userId', $token->userId());
        }

        return $handler->handle($request);
    }
}
