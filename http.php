<?php

// Ignore this if-statement, it serves only to prevent running this file directly.
if (!class_exists(Aerys\Process::class, false)) {
    echo "This file is not supposed to be invoked directly. To run it, use `php bin/aerys -c http.php`.\n";
    exit(1);
}

use Aerys\{ Host, Request, Response, function root, function router };
use Acme\Contract\Command;
use Amp\Deferred;
use PHPinnacle\Core\Context\RootContext;

/* --- Global server options -------------------------------------------------------------------- */

const AERYS_OPTIONS = [
    "connectionTimeout" => 60,
    //"deflateMinimumLength" => 0,
];

/** @var \PHPinnacle\Core\MessageBus $messages */
$messages = require __DIR__ . '/app.php';

$router = router()
    ->route("POST", "/token", function(Request $req, Response $res) use ($messages) {
        $body = yield $req->getBody();
        $json = \json_decode($body, true);

        $uri = $json['uri'];
        $cmd = new Command\RequestCommand($uri);

        $deferred = new Deferred();

        $task = $messages->handle($cmd, new RootContext());
        $task->then(function ($v = null, \Throwable $e = null) use ($deferred, $uri) {
            if (null !== $e) {
                $deferred->fail($e);
            } else {
                $deferred->resolve($v);
            }
        });

        $v = yield $deferred->promise();

        $res->end("<html><body><h1>Response for uri: {$uri}</h1>".$v."</body></html>");
    })
    ->route("GET", "/favicon.ico", function(Request $req, Response $res) {
        $res->setStatus(404);
        $res->end(Aerys\makeGenericBody(404));
    })
;

// If none of our routes match try to serve a static file
$root = root($docrootPath = __DIR__);

// If no static files match fallback to this
$fallback = function(Request $req, Response $res) {
    $res->end("<html><body><h1>Fallback \o/</h1></body></html>");
};

return (new Host)
    ->expose("0.0.0.0", 8088)
    ->use($router)
    ->use($root)
    ->use($fallback)
;
