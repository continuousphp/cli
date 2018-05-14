<?php

namespace Continuous\Cli;

use Continuous\Cli\Command\Build\BuildListCommand;
use Continuous\Cli\Command\Build\BuildStartCommand;
use Continuous\Cli\Command\Build\BuildStopCommand;
use Continuous\Cli\Command\Build\BuildWaitCommand;
use Continuous\Cli\Command\Company\CompanyListCommand;
use Continuous\Cli\Command\ConfigureCommand;
use Continuous\Cli\Command\Pipeline\PipelineExportCommand;
use Continuous\Cli\Command\Package\PackageDownloadCommand;
use Continuous\Cli\Command\Project\ProjectListCommand;
use Continuous\Cli\Command\Repository\RepositoryListCommand;
use Symfony\Component\Console\Application;

/**
 * Class ApplicationFactory
 * @package Continuous\Cli
 */
final class ApplicationFactory
{
    const NAME = 'ContinuousPHP Cli';

    protected static $version;

    /**
     * @return Application
     */
    public function create()
    {
        $application = new Application(self::NAME, self::getVersion());
        $application->add(new ConfigureCommand());
        $application->add(new CompanyListCommand());
        $application->add(new RepositoryListCommand());
        $application->add(new ProjectListCommand());
        $application->add(new BuildListCommand());
        $application->add(new BuildStartCommand());
        $application->add(new BuildStopCommand());
        $application->add(new PipelineExportCommand());
        $application->add(new PackageDownloadCommand());
        $application->add(new BuildWaitCommand());

        return $application;
    }

    /**
     * Return the current version of continuousphp cli.
     *
     * @return string
     */
    public static function getVersion()
    {
        return constant(__NAMESPACE__ . '\\' . 'version');
    }
}