<?php

namespace Continuous\Cli\Command\Build;

use Continuous\Cli\Command\CommandAbstract;
use Continuous\Sdk\Collection;
use Continuous\Sdk\Entity\Build;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class BuildListCommand extends CommandAbstract
{
    protected function configure()
    {
        $this
            ->setName('build:list')
            ->setDescription('List of builds for specific project.')
            ->setHelp('This command help you to list the builds of specific project and pipeline.')
            ->addArgument('provider', InputArgument::REQUIRED, 'The repository provider')
            ->addArgument('repository', InputArgument::REQUIRED, 'The repository name')
        ;

        $this
            ->addOption(
                'ref',
                'r',
                InputOption::VALUE_OPTIONAL,
                'the pipeline ref'
            )
            ->addOption(
                'state',
                's',
                InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY,
                'the build status',
                Build::STATES
            )
            ->addOption(
                'noPr',
                null,
                InputOption::VALUE_NONE,
                'remove the PullRequest of result'
            )
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);

        $this->showLoader($output, 'Loading builds...');
        $ref = $input->getOption('ref');
        $state = $input->getOption('state');

        $params = [
            'provider' => static::mapProviderToSdk($input->getArgument('provider')),
            'repository' => $input->getArgument('repository'),
            'state' => $state,
        ];

        if ($ref) {
            $params['pipeline_id'] = $ref;
        }

        if (true === $input->getOption('noPr')) {
            $params['exclude_pull_requests'] = '1';
        }

        /** @var Collection $collection */
        $collection = $this->continuousClient->getBuilds($params);
        $rows = [];

        $this->hideLoader($output);

        foreach ($collection as $id => $build) {

            $created = \DateTimeImmutable::createFromFormat('Y-m-d*H:i:sP', $build->get('created'));

            $launchUser = $build->getLaunchUser();
            $result = $build->get('result');
            $resultOutput = "<fg=white;bg=". ('failed' === $result ? 'red' : 'green') .">$result</>";

            $successActivities = array_filter($build->get('activities'), function($item) {
                return true === $item['result'];
            });

            $rows[] = [
                $id,
                $build->get('ref'),
                $build->get('pullRequestNumber') ? $build->get('pullRequestNumber') : "-",
                $build->get('state'),
                $resultOutput,
                $build->getDuration()->format('%H:%I:%S'),
                count($successActivities) . '/' . count($build->get('activities')),
                round($build->get('codeCoverage')) . "%",
                $launchUser->displayName(),
                $created->format('d/m/Y H:i:s'),
            ];
        }

        $table = new Table($output);
        $table
            ->setHeaders(['ID', 'Ref', 'PR', 'State', 'Result', 'Duration', 'Activities Success', 'Code Coverage', 'Launch by', 'date'])
            ->setRows($rows)
            ->render()
        ;
    }
}