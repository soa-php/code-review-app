<?php

declare(strict_types=1);

namespace CommonTest\Ui\Http\Restful\Middleware;

use Common\Ui\Http\Restful\Authorization\AuthorizationRulesDefinitionException;
use Common\Ui\Http\Restful\Authorization\AuthorizationService;
use Common\Ui\Http\Restful\Authorization\AuthorizationType;
use Common\Ui\Http\Restful\Authorization\Token;
use Common\Ui\Http\Restful\Authorization\TokenParser;
use Common\Ui\Http\Restful\Middleware\AuthorizationMiddleware;
use Lukasoppermann\Httpstatus\Httpstatuscodes;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\Response\EmptyResponse;
use Zend\Diactoros\ServerRequestFactory;
use Zend\Expressive\Router\Route;
use Zend\Expressive\Router\RouteResult;

class AuthorizationMiddlewareTest extends TestCase
{
    const URI              = 'some_uri';
    const SECURED_METHOD   = 'secured_method';
    const UNSECURED_METHOD = 'unsecured_method';
    const AUTHORIZED_ROLE  = 'role1';
    const AUTHORIZED_ROLES = [self::AUTHORIZED_ROLE];

    private $authorizationRules = [
        self::URI => [
            self::SECURED_METHOD   => self::AUTHORIZED_ROLES,
            self::UNSECURED_METHOD => AuthorizationType::NO_AUTH,
        ],
    ];

    /**
     * @test
     */
    public function shouldContinuePipeline_when_userAuthorized()
    {
        $usedToken = new Token('user id', self::AUTHORIZED_ROLES, Token::ACCESS_TYPE);

        $authMiddleware = new AuthorizationMiddleware(new AuthorizationService($this->authorizationRules), new TokenParserStub($usedToken));
        $request        = $this->createServerRequest(self::URI, self::SECURED_METHOD);
        $requestHandler = new RequestHandlerSpy();
        $authMiddleware->process($request, $requestHandler);

        $this->assertEquals($usedToken, $requestHandler->handledRequest()->getAttribute('token'));
    }

    /**
     * @test
     */
    public function shouldContinuePipeline_when_routeAuthNotRequired()
    {
        $usedToken = new Token('user id', [], Token::ACCESS_TYPE);

        $authMiddleware = new AuthorizationMiddleware(new AuthorizationService($this->authorizationRules), new TokenParserStub($usedToken));
        $request        = $this->createServerRequest(self::URI, self::UNSECURED_METHOD);
        $requestHandler = new RequestHandlerSpy();
        $authMiddleware->process($request, $requestHandler);

        $this->assertEmpty($requestHandler->handledRequest()->getAttribute('token'));
    }

    /**
     * @test
     */
    public function shouldRaiseException_when_authorizationRuleNotDefinedForRoute()
    {
        $this->expectException(AuthorizationRulesDefinitionException::class);

        $usedToken = new Token('user id', [], Token::ACCESS_TYPE);

        $authMiddleware = new AuthorizationMiddleware(new AuthorizationService($this->authorizationRules), new TokenParserStub($usedToken));
        $request        = $this->createServerRequest(self::URI, 'undefined_method');
        $requestHandler = new RequestHandlerSpy();
        $authMiddleware->process($request, $requestHandler);
    }

    /**
     * @test
     */
    public function shouldReturnUnauthorized_when_authorizationHeaderNotProvided()
    {
        $usedToken = new Token('user id', [], Token::ACCESS_TYPE);

        $authMiddleware = new AuthorizationMiddleware(new AuthorizationService($this->authorizationRules), new TokenParserStub($usedToken));
        $request        = $this->createServerRequest(self::URI, self::SECURED_METHOD, false);
        $requestHandler = new RequestHandlerSpy();
        $response       = $authMiddleware->process($request, $requestHandler);

        $this->assertEquals(Httpstatuscodes::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function shouldReturnUnauthorized_when_userNotAuthorized()
    {
        $usedToken = new Token('user id', ['some unauthorized role'], Token::ACCESS_TYPE);

        $authMiddleware = new AuthorizationMiddleware(new AuthorizationService($this->authorizationRules), new TokenParserStub($usedToken));
        $request        = $this->createServerRequest(self::URI, self::SECURED_METHOD);
        $requestHandler = new RequestHandlerSpy();
        $response       = $authMiddleware->process($request, $requestHandler);

        $this->assertEquals(Httpstatuscodes::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    private function createServerRequest(string $expectedRequestedRoute, string $method, bool $withAuthHeader = true): ServerRequestInterface
    {
        $route = $this->createMock(Route::class);
        $route->method('getName')
            ->willReturn($expectedRequestedRoute);

        $routeResult = $this->createMock(RouteResult::class);
        $routeResult->method('getMatchedRoute')
            ->willReturn($route);

        $request = (new ServerRequestFactory())
            ->createServerRequest($method, 'uri')
            ->withAttribute(
                RouteResult::class,
                $routeResult
            );

        if ($withAuthHeader) {
            $request = $request->withHeader('Authorization', 'some token');
        }

        return $request;
    }
}

class TokenParserStub implements TokenParser
{
    /**
     * @var Token
     */
    private $token;

    public function __construct(Token $token)
    {
        $this->token = $token;
    }

    public function parse(string $token): Token
    {
        return $this->token;
    }
}

class RequestHandlerSpy implements RequestHandlerInterface
{
    /**
     * @var ServerRequestInterface
     */
    private $handledRequest;

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $this->handledRequest = $request;

        return new EmptyResponse();
    }

    public function handledRequest(): ServerRequestInterface
    {
        return $this->handledRequest;
    }
}
