<?php

namespace Continuous\Cli\Command\Repository;

use Continuous\Cli\Command\CommandAbstract;
use Continuous\Sdk\Collection;
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
            ->setDescription('List of repositories not yet configured.')
            ->setHelp('This command help you to find the repository that you can configure on ContinuousPHP and has not yet be initialized.')
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

        /** @var Collection $collection */
        $collection = $this->continuousClient->getRepositories();
        $rows = [];

        $this->hideLoader($output);

        foreach ($collection as $id => $repository) {
            $name = $repository->get('name');

            if (null !== $filterName && false === strpos(strtolower($name), $filterName)) {
                continue;
            }

            $rows[] = [
                $repository->getProvider()->get('name'),
                $repository->get('isPrivate') ? 'Yes' : "No",
                $id,
                $name,
                $repository->get('owner'),
                $repository->get('htmlUrl'),
                $repository->get('description'),
            ];
        }

        $table = new Table($output);
        $table
            ->setHeaders(['Provider', 'Private', 'ID', 'Name', 'Owner', 'Url', 'Description'])
            ->setRows($rows)
            ->render()
        ;
    }
}