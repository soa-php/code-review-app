<?php

declare(strict_types=1);

use Zend\Expressive\Container;
use Zend\Expressive\Middleware\ErrorResponseGenerator;
use Zend\ConfigAggregator\ConfigAggregator;

return [
    'debug'                        => true,
    ConfigAggregator::ENABLE_CACHE => false,
];
