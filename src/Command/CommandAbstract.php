<?php

namespace Continuous\Cli\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CommandAbstract
 * @package Continuous\Cli\Command
 */
abstract class CommandAbstract extends Command
{
    /**
     * @var \Continuous\Sdk\Client
     */
    protected $continuousClient;

    /**
     * @var ProgressBar
     */
    protected $loader;

    /**
     * CommandAbstract constructor.
     *
     * @param null $name
     */
    public function __construct($name = null)
    {
        parent::__construct($name);

        $this->addTokenOption();
    }

    public static function mapProviderToSdk($provider)
    {
        if (in_array(strtolower(trim($provider)), ['github', 'git hub']))
        {
            return 'git-hub';
        }

        if (in_array(strtolower(trim($provider)), ['bb']))
        {
            return 'bitbucket';
        }

        return $provider;
    }

    protected function addTokenOption()
    {
        $this
            ->addOption(
                'token',
                null,
                InputOption::VALUE_OPTIONAL,
                'The token of continuousphp user',
                null
            )
            ->addOption(
                'profile',
                null,
                InputOption::VALUE_OPTIONAL,
                'The profile of configure credentials. See route configure',
                null
            );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $token = $input->getOption('token');
        $profile = $input->getOption('profile');

        if (null === $token && false === ($token = getenv('CPHP_TOKEN'))) {

            $profile = empty($profile) ? 'default' : $profile;
            $token = ConfigureCommand::getToken($profile);

            if (null === $token) {
                $output->writeln("<comment>WARNING : ContinuousPHP Token was not found</comment>");
            }
        }

        $this->continuousClient = \Continuous\Sdk\Service::factory([
            'token' => $token
        ]);
    }

    protected function showLoader($output, $message = '')
    {
        $this->loader = new ProgressBar($output, 1);

        if ($message) {
            $this->loader->setFormatDefinition('custom', ' %current%/%max% -- %message%');
            $this->loader->setFormat('custom');
            $this->loader->setMessage($message);

            $this->loader->start();
            $this->loader->advance();
        } else {
            $this->loader->start();
        }
    }

    protected function hideLoader($output)
    {
        $this->loader->finish();
        $this->loader = null;

        $output->writeln("\n");
    }
}