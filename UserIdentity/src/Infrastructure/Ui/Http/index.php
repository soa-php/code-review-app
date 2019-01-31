<?php

declare(strict_types=1);

// Delegate static file requests back to the PHP built-in webserver
if (PHP_SAPI === 'cli-server' && __FILE__ !== $_SERVER['SCRIPT_FILENAME']) {
    return false;
}

chdir(dirname(__DIR__));
require __DIR__ . '/../../../../vendor/autoload.php';

/**
 * Self-called anonymous function that creates its own scope and keep the global namespace clean.
 */
(function () {
    /** @var \Psr\Container\ContainerInterface $container */
    $container = require __DIR__ . '/../../Di/ZendServiceManager/container.php';

    /** @var \Zend\Expressive\Application $app */
    $app = $container->get(\Zend\Expressive\Application::class);
    $factory = $container->get(\Zend\Expressive\MiddlewareFactory::class);

    // Execute programmatic/declarative middleware pipeline and routing
    // configuration statements
    (require __DIR__ . '/pipeline.php')($app, $factory, $container);
    (require __DIR__ . '/Restful/routes.php')($app, $factory, $container);

    $app->run();
})();
