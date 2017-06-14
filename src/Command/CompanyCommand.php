<?php

namespace Continuous\Cli\Command;

use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CompanyCommand extends CommandAbstract
{
    protected function configure()
    {
        $this
            ->setName('company:list')
            ->setDescription('List Companies.')
            ->setHelp('This command related to companies declared on continuous.')
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $collection = $this->continuousClient->getCompanies();
        $rows = [];

        foreach ($collection as $id => $company) {
            $rows[] = [$id, $company->get('name')];
        }

        $table = new Table($output);
        $table
            ->setHeaders(['ID', 'Name'])
            ->setRows($rows)
            ->render()
        ;
    }
}