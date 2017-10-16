<?php

use Acme\Contract\Command;
use Acme\Service\AcmeService;
use PHPinnacle\Core\Context;
use PHPinnacle\Core\MessageBus;
use PHPinnacle\Core\Resolver\ClassNameResolver;
use PHPinnacle\System\Loop;
use PHPinnacle\System\TaskScheduler;

include __DIR__ . '/vendor/autoload.php';

$loop = new Loop\AmpLoop();
$scheduler = new TaskScheduler($loop);

$acme = new AcmeService();

$context = new Context\RootContext();
$resolver = new ClassNameResolver([
    Command\EchoCommand::class    => [$acme, 'handleEchoCommand'],
    Command\RequestCommand::class => [$acme, 'handleRequestCommand'],
]);

return new MessageBus($resolver, $scheduler);
