<?php

declare(strict_types=1);

use Payment\Infrastructure\Ui\Messaging\Listener\CollectMoneyCommandListener;
use Payment\Infrastructure\Ui\Messaging\Listener\PayMoneyCommandListener;
use Psr\Container\ContainerInterface;
use Soa\MessageStore\Subscriber\SubscriberApplication;

return function (SubscriberApplication $application, ContainerInterface $container): void {
//    $router->add(<topic>, <type>, <command>, <listener>);
    $boundedContext = $container->get('config')['bounded-context'];

    $application->addSubscription($boundedContext, 'com.payment.commands.collect_money_command', CollectMoneyCommandListener::class);
    $application->addSubscription($boundedContext, 'com.payment.commands.pay_money_command', PayMoneyCommandListener::class);
};
