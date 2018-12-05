<?php

declare(strict_types=1);

use MergePullRequestPm\Infrastructure\Ui\Messaging\Listener\MoneyCollectedListener;
use MergePullRequestPm\Infrastructure\Ui\Messaging\Listener\MoneyPayedListener;
use MergePullRequestPm\Infrastructure\Ui\Messaging\Listener\PullRequestMarkedAsMergeableListener;
use MergePullRequestPm\Infrastructure\Ui\Messaging\Listener\PullRequestMergedListener;
use Psr\Container\ContainerInterface;
use Soa\MessageStore\Subscriber\SubscriberApplication;

return function (SubscriberApplication $application, ContainerInterface $container): void {
//    $router->add(<exchange>, <type>, <listener>);
    $application->addSubscription(
        'pull_request',
        'com.pull_request.events.pull_request_marked_as_mergeable',
        PullRequestMarkedAsMergeableListener::class
    );
    $application->addSubscription(
        'payment',
        'com.payment.events.money_collected',
        MoneyCollectedListener::class
    );
    $application->addSubscription(
        'payment',
        'com.payment.events.money_payed',
        MoneyPayedListener::class
    );
    $application->addSubscription(
        'pull_request',
        'com.pull_request.events.pull_request_merged',
        PullRequestMergedListener::class
    );
};
