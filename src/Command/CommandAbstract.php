<?php

namespace Continuous\Cli\Command;

use Symfony\Component\Console\Command\Command;

abstract class CommandAbstract extends Command
{
    protected $continuousClient;

    public function __construct($name = null)
    {
        parent::__construct($name);

        $this->continuousClient = \Continuous\Sdk\Service::factory();
    }
}