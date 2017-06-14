<?php

namespace Continuous\Cli\Command\Repository;

use Continuous\Cli\Command\CommandAbstract;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class RepositoryListCommand extends CommandAbstract
{
    protected function configure()
    {
        $this
            ->setName('repo:list')
            ->setDescription('List of repositories authorized on provider application.')
            ->setHelp('This command help you to find the repository that continuousPHP have access and can be configured.')
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
        $this->showLoader($output, 'Loading repositories from providers (github, bitbucket, gitlab)...');

        $collection = $this->continuousClient->getRepositories();
        $rows = [];

        $this->hideLoader($output);

        foreach ($collection as $id => $repository) {
            $name = $repository->get('name');

            if (null !== $filterName && false === strpos(strtolower($name), $filterName)) {
                continue;
            }

            $rows[] = [
                $id,
                $name
            ];
        }

        $table = new Table($output);
        $table
            ->setHeaders(['ID', 'Name'])
            ->setRows($rows)
            ->render()
        ;
    }
}