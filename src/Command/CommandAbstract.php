<?php

namespace Continuous\Cli\Command;

use Symfony\Component\Console\Command\Command;
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
     * CommandAbstract constructor.
     *
     * @param null $name
     */
    public function __construct($name = null)
    {
        parent::__construct($name);

        $this->addTokenOption();
    }

    protected function addTokenOption()
    {
        $this
            ->addOption(
                'token',
                't',
                InputOption::VALUE_OPTIONAL,
                'The token of continuousphp user',
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

        if (null === $token && false === ($token = getenv('CPHP_TOKEN'))) {
            $output->writeln("<comment>WARNING : ContinuousPHP Token was not found</comment>");
        }

        $this->continuousClient = \Continuous\Sdk\Service::factory([
            'token' => $token
        ]);
    }
}