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

namespace Acme\Contract\Command;

class EchoCommand
{
    public $text;

    public function __construct(string $text = '')
    {
        $this->text = $text;
    }
}
