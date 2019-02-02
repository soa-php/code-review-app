<?php

declare(strict_types=1);

namespace Common\Ui\Http\Restful\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;
use Zend\ProblemDetails\ProblemDetailsResponseFactory;

class ErrorHandlerMiddleware implements MiddlewareInterface
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var ProblemDetailsResponseFactory
     */
    private $problemDetailsResponseFactory;

    public function __construct(LoggerInterface $logger, ProblemDetailsResponseFactory $problemDetailsResponseFactory)
    {
        $this->logger                        = $logger;
        $this->problemDetailsResponseFactory = $problemDetailsResponseFactory;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        set_error_handler($this->createErrorHandler());

        try {
            $response = $handler->handle($request);
        } catch (\Throwable $e) {
            return $this->problemDetailsResponseFactory->createResponseFromThrowable($request, $e);
        }

        restore_error_handler();

        return $response;
    }

    private function createErrorHandler(): callable
    {
        return function (int $errno, string $errstr, string $errfile, int $errline): void {
            if (!(error_reporting() & $errno)) {
                // error_reporting does not include this error
                return;
            }

            throw new \ErrorException($errstr, 0, $errno, $errfile, $errline);
        };
    }
}
