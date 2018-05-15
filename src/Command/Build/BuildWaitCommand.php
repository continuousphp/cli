<?php

namespace Continuous\Cli\Command\Build;

use Continuous\Cli\Command\CommandAbstract;
use Continuous\Sdk\Entity\Build;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class BuildWaitCommand extends CommandAbstract
{
    const WAIT_SECONDS_INTERVAL = 5;

    protected function configure()
    {
        $this
            ->setName('build:wait')
            ->setDescription('Wait until the end of build.')
            ->setHelp('This command help you to wait unti the build finish.')
            ->addArgument('provider', InputArgument::REQUIRED, 'The repository provider')
            ->addArgument('repository', InputArgument::REQUIRED, 'The repository name')
            ->addArgument('buildId', InputArgument::REQUIRED, 'The build ID')
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);

        $params = [
            'provider' => static::mapProviderToSdk($input->getArgument('provider')),
            'repository' => $input->getArgument('repository'),
            'buildId' => $input->getArgument('buildId'),
        ];

        $table = new Table($output);
        $table
            ->setHeaders(['ID', 'Name', 'State', 'Result', 'Blocking', 'Duration', 'Start Time'])
            ->setRows([])
        ;

        do {
            /** @var Build $build */
            $build = $this->continuousClient->getBuild($params);
            $this->showActivitiesTable($build, $table);
        } while('complete' !== $build->get('state') && 0 === sleep(static::WAIT_SECONDS_INTERVAL));

        exit('success' === $build->get('result') ? 0 : 1);
    }

    /**
     * @param Build $build
     * @param Table $table
     * @throws \Exception
     */
    private function showActivitiesTable(Build $build, Table &$table)
    {
        $activities = $build->get('activities');
        $rows = [];

        foreach ($activities as $activity) {
            $resultOutput = 'pending';

            if ('finished' === $activity['state']) {
                $result = $activity['result'] ? 'success' : 'failed';
                $resultOutput = "<fg=white;bg=". ('failed' === $result ? 'red' : 'green') .">$result</>";
            }

            $startTime = $activity['startTime'] ?
                \DateTimeImmutable::createFromFormat('Y-m-d*H:i:sP', $activity['startTime'])
                : new \DateTimeImmutable();
            $endTime = $activity['endTime'] ?
                \DateTimeImmutable::createFromFormat('Y-m-d*H:i:sP', $activity['endTime'])
                : new \DateTimeImmutable();
            $duration = $startTime->diff($endTime);

            $rows[] = [
                $activity['id'],
                $activity['name'],
                $activity['state'],
                $resultOutput,
                $activity['blocking'] ? 'Yes' : 'No',
                $duration->format('%H:%I:%S'),
                $startTime->format('d/m/Y H:i:s'),
            ];
        }

        $table
            ->setRows($rows)
            ->render();
    }
}