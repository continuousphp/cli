<?php

namespace Continuous\Cli\Command\Build;

use Continuous\Cli\Command\CommandAbstract;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class BuildStopCommand extends CommandAbstract
{
    protected function configure()
    {
        $this
            ->setName('build:stop')
            ->setDescription('stop a build.')
            ->setHelp('This command help you to stop build for specific pipeline project.')
            ->addArgument('provider', InputArgument::REQUIRED, 'The repository provider')
            ->addArgument('repository', InputArgument::REQUIRED, 'The repository name')
            ->addArgument('build-id', InputArgument::REQUIRED, 'The build id you want to stop')
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);

        $this->showLoader($output, 'stopping builds...');

        $params = [
            'provider' => static::mapProviderToSdk($input->getArgument('provider')),
            'repository' => $input->getArgument('repository'),
            'buildId' => $input->getArgument('build-id'),
        ];

        $result = $this->continuousClient->cancelBuild($params);
        var_dump($result);

        $output->writeln('Build has been cancelled.');
    }
}