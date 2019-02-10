<?php

declare(strict_types=1);

namespace Common\Ui\Http\Restful\Middleware;

use Common\Ui\Http\Restful\Authorization\Token;
use Lukasoppermann\Httpstatus\Httpstatuscodes;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Common\Ui\Http\Restful\Authorization\AuthorizationService;
use Common\Ui\Http\Restful\Authorization\TokenParser;
use Zend\Diactoros\Response\EmptyResponse;
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
        if (($route instanceof RouteResult) && $route->getMatchedRoute()) {
            $routeName = $route->getMatchedRoute()->getName();

            if (!$this->authorizationService->isAuthRequiredForRoute($routeName, $request->getMethod())) {
                return $handler->handle($request);
            }

            $token = str_replace('Bearer ', '', $request->getHeaderLine('Authorization'));

            if (empty($token)) {
                return new EmptyResponse(Httpstatuscodes::HTTP_UNAUTHORIZED);
            }

            $token =  $this->tokenParser->parse($token);

            if (!$this->authorizationService->isUserAuthorizedForRoute($token->roles(), $routeName, $request->getMethod())) {
                return new EmptyResponse(Httpstatuscodes::HTTP_UNAUTHORIZED);
            }

            $request = $request->withAttribute(Token::class, $token);
        }

        return $handler->handle($request);
    }
}
