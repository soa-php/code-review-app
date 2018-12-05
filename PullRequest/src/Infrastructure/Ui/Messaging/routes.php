<?php

declare(strict_types=1);

use Psr\Container\ContainerInterface;
use PullRequest\Infrastructure\Ui\Messaging\Listener\MergePullRequestCommandListener;
use Soa\MessageStore\Subscriber\SubscriberApplication;

return function (SubscriberApplication $application, ContainerInterface $container): void {
//    $router->add(<topic>, <type>, <command>, <listener>);
    $boundedContext = $container->get('config')['bounded-context'];

    $application->addSubscription($boundedContext, 'com.pull_request.commands.merge_pull_request_command', MergePullRequestCommandListener::class);
};
