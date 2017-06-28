<?php

namespace Continuous\Cli\Command\Project;

use Continuous\Cli\Command\CommandAbstract;
use Continuous\Sdk\Collection;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ProjectListCommand extends CommandAbstract
{
    protected function configure()
    {
        $this
            ->setName('project:list')
            ->setDescription('List of project configured.')
            ->setHelp('This command help you to find the repository already configured on ContinuousPHP.')
        ;

        $this
            ->addOption(
                'filter-name',
                null,
                InputOption::VALUE_OPTIONAL,
                'filter apply on name of repositories result'
            );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);

        $filterName = $input->getOption('filter-name');
        $this->showLoader($output, 'Loading projects from providers (github, bitbucket, gitlab)...');

        /** @var Collection $collection */
        $collection = $this->continuousClient->getProjects();
        $rows = [];

        $this->hideLoader($output);

        foreach ($collection as $id => $project) {
            $name = $project->get('name');

            if (null !== $filterName && false === strpos(strtolower($name), $filterName)) {
                continue;
            }

            $rows[] = [
                $project->getProvider()->get('name'),
                $name,
                $project->get('canSeeSettings') ? 'Yes' : "No",
                $project->get('canEditSettings') ? 'Yes' : "No",
                $project->get('canBuild') ? 'Yes' : "No",
            ];
        }

        $table = new Table($output);
        $table
            ->setHeaders(['Provider', 'Name', 'View settings', 'Edit settings', 'Run build'])
            ->setRows($rows)
            ->render()
        ;
    }
}