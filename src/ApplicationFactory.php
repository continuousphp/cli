<?php

namespace Continuous\Cli;

use Continuous\Cli\Command\CompanyCommand;
use Symfony\Component\Console\Application;

final class ApplicationFactory
{
    const NAME = 'ContinuousPHP Cli';
    const VERSION = 'v0.0.1';

    /**
     * @return Application
     */
    public function create()
    {
        $companyCommand = new CompanyCommand();

        $application = new Application(self::NAME, self::VERSION);
        $application->add($companyCommand);

        return $application;
    }
}