<?php

namespace Continuous\Cli\Command\Build;

use Continuous\Cli\Command\CommandAbstract;
use Continuous\Sdk\Entity\Build;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class BuildStartCommand extends CommandAbstract
{
    protected function configure()
    {
        $this
            ->setName('build:start')
            ->setDescription('start a build for specific project.')
            ->setHelp('This command help you to start build for specific pipeline project.')
            ->addArgument('provider', InputArgument::REQUIRED, 'The repository provider')
            ->addArgument('repository', InputArgument::REQUIRED, 'The repository name')
            ->addArgument('ref', InputArgument::REQUIRED, 'The git reference')
        ;

        $this
            ->addOption(
                'pull-request',
                'pr',
                InputOption::VALUE_OPTIONAL,
                'the PR id you want build'
            )
            ->addOption(
                'attach',
                'a',
                InputOption::VALUE_NONE,
                'attach the log'
            )
            ->addOption(
                'print-only-build-id',
                'p',
                InputOption::VALUE_NONE,
                'Output only the Build ID without any other message'
            );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);

        if (!$input->getOption('print-only-build-id')) {
            $this->showLoader($output, 'Starting builds...');
        }

        $params = [
            'provider' => static::mapProviderToSdk($input->getArgument('provider')),
            'repository' => $input->getArgument('repository'),
            'ref' => $input->getArgument('ref'),
        ];

        if ($pr = $input->getOption('pull-request')) {
            $params['pull_request'] = $pr;
        }

        /** @var Build $build */
        $build = $this->continuousClient->startBuild($params);

        if ($input->getOption('print-only-build-id')) {
            echo $build->get('buildId');
        } else {
            $output->writeln('Build started with ID ' . $build->get('buildId'));
        }
    }
}