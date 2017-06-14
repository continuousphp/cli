<?php

namespace Continuous\Cli\Command\Company;

use Continuous\Cli\Command\CommandAbstract;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CompanyCommand
 * @package Continuous\Cli\Command
 */
class CompanyListCommand extends CommandAbstract
{
    protected function configure()
    {
        $this
            ->setName('company:list')
            ->setDescription('List Companies.')
            ->setHelp('This command related to companies declared on continuous.')
        ;

        $this
            ->addOption(
                'filter-name',
                null,
                InputOption::VALUE_OPTIONAL,
                'filter apply on name of companies result'
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

        $collection = $this->continuousClient->getCompanies();
        $rows = [];

        foreach ($collection as $id => $company) {
            $name = $company->get('name');

            if (null !== $filterName && false === strpos(strtolower($name), $filterName)) {
                continue;
            }

            $rows[] = [
                $id,
                $name,
                $company->get('website'),
                $company->get('email'),
                $company->get('vat'),
                $company->get('currency'),
            ];
        }

        $table = new Table($output);
        $table
            ->setHeaders(['ID', 'Name', 'Website', 'Email', 'Vat', 'Currency'])
            ->setRows($rows)
            ->render()
        ;
    }
}