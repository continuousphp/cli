<?php

namespace Continuous\Cli\Command\Project;

use Continuous\Cli\Command\CommandAbstract;
use Continuous\Sdk\Collection;
use Continuous\Sdk\Entity\Project;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class ProjectResetHooksCommand extends CommandAbstract
{
    protected function configure()
    {
        $this
            ->setName('project:reset-hooks')
            ->setDescription('reset provider hooks. (YOU MUST HAVE WRITE ACCESS TO RESET HOOKS)')
            ->setHelp('This command help you to reset the provider hooks and ssh key on the repository configured with ContinuousPHP.')
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

        /** @var Project[] $projects */
        $projects = [];

        $this->hideLoader($output);

        foreach ($collection as $id => $project) {
            $name = $project->get('name');

            if (!$project->get('canEditSettings')) {
                continue;
            }

            if (null !== $filterName && false === strpos(strtolower($name), strtolower($filterName))) {
                continue;
            }

            $projects[] = $project;

            $rows[] = [
                $project->getProvider()->get('name'),
                $name,
            ];
        }

        $table = new Table($output);
        $table
            ->setHeaders(['Provider', 'Name'])
            ->setRows($rows)
            ->render()
        ;

        $question = new ConfirmationQuestion(
            'Confirm reset hooks for ALL the repositories listed? [Y/n]',
            true,
            '/^(y|yes|oui)/i'
        );
        $ack = new QuestionHelper();

        if (!$ack->ask($input, $output, $question)) {
            return;
        }

        $this->showLoader($output, 'Reset hooks in progress...', count($projects));

        foreach ($projects as $project) {
            $params = [
                'provider' => static::mapProviderToSdk($project->getProvider()->get('name')),
                'repository' => $project->get('name'),
            ];
            $this->continuousClient->resetWebHooks($params);
            $this->loader->advance();
        }

        $this->hideLoader($output);
    }
}