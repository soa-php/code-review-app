<?php

declare(strict_types=1);

namespace Common\Ui\Http\Restful\Middleware;

use Lukasoppermann\Httpstatus\Httpstatuscodes;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Soa\EventSourcing\Command\CommandBus;
use Soa\IdentifierGenerator\IdentifierGenerator;
use Soa\IdentifierGenerator\UuidIdentifierGenerator;
use Soa\Traceability\Trace;
use Zend\ProblemDetails\ProblemDetailsResponseFactory;
use Zend\Stdlib\Parameters;

/**
 * @method ResponseInterface get(ServerRequestInterface $request)
 * @method ResponseInterface post(ServerRequestInterface $request)
 * @method ResponseInterface patch(ServerRequestInterface $request)
 * @method ResponseInterface put(ServerRequestInterface $request)
 * @method ResponseInterface delete(ServerRequestInterface $request)
 */
abstract class AbstractRestfulResourceMiddleware implements MiddlewareInterface
{
    /**
     * @var ProblemDetailsResponseFactory
     */
    protected $problemDetailsResponseFactory;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var ServerRequestInterface
     */
    private $request;

    /**
     * @var IdentifierGenerator
     */
    protected $identifierGenerator;

    public function __construct(ContainerInterface $container)
    {
        $this->problemDetailsResponseFactory = $container->get(ProblemDetailsResponseFactory::class);
        $this->container                     = $container;
        $this->identifierGenerator           = new UuidIdentifierGenerator();
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $this->request = $request;
        $httpVerb      = strtolower($request->getMethod());

        if (method_exists($this, $httpVerb)) {
            $response = $this->$httpVerb($request);
        } else {
            return $this->problemDetailsResponseFactory->createResponse(
                $request,
                Httpstatuscodes::HTTP_METHOD_NOT_ALLOWED,
                strtoupper($httpVerb) . ' method is not allowed'
            );
        }

        return $response;
    }

    protected function getParamsFromRequest(ServerRequestInterface $request): Parameters
    {
        return new Parameters(json_decode($request->getBody()->getContents(), true));
    }

    protected function commandBus(string $commandBusName): CommandBus
    {
        $requestId = $this->request->getHeaderLine('Id');
        if (!$requestId) {
            throw new \RuntimeException('Id header not provided');
        }

        $trace = new Trace(
            $requestId,
            \DateTimeImmutable::createFromFormat('U.u', (string) microtime(true))->format('Y-m-d H:i:s.u'),
            $requestId,
            $requestId,
            'request',
            ''
        );

        return new $commandBusName($this->container, $trace);
    }
}
