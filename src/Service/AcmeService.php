<?php
/*
 * This file is part of PHPinnacle/Playground.
 *
 * (c) PHPinnacle Team <dev@phpinnacle.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Acme\Service;

use Acme\Contract\Command;
use Amp\Artax\DefaultClient;

class AcmeService
{
    private $client;

    public function __construct()
    {
        $this->client = new DefaultClient();
    }

    public function handleEchoCommand(Command\EchoCommand $command): void
    {
        echo $command->text;
    }

    public function handleRequestCommand(Command\RequestCommand $command)
    {
        $start = microtime(true);

        echo "Request start for uri {$command->uri}: " . (microtime(true)) . \PHP_EOL;

        $response = yield $this->client->request($command->uri);

        echo "Request end for uri {$command->uri}: " . (microtime(true)) . \PHP_EOL;

        return microtime(true) - $start;
    }
}
