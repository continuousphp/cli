<?php

namespace Continuous\Cli\Command\Package;

use Continuous\Cli\Command\CommandAbstract;
use Continuous\Sdk\Collection;
use Continuous\Sdk\Entity\Build;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class PackageDownloadCommand extends CommandAbstract
{
    const PACKAGE_TYPES = ['deploy', 'test'];

    protected function configure()
    {
        $this
            ->setName('package:download')
            ->setDescription('Download package of continuousphp build.')
            ->setHelp('This command download package for latest build of pipeline the one you specify by build ID.')
            ->addArgument('provider', InputArgument::REQUIRED, 'The repository provider')
            ->addArgument('repository', InputArgument::REQUIRED, 'The repository name')
            ->addArgument('destination', InputArgument::OPTIONAL, 'The destination path of package file, by default current workdir')
        ;

        $this
            ->addOption(
                'ref',
                'r',
                InputOption::VALUE_OPTIONAL,
                'Pipeline git reference (e.g refs/heads/master)'
            )
            ->addOption(
                'id',
                'i',
                InputOption::VALUE_OPTIONAL,
                'The build ID'
            )
            ->addOption(
                'type',
                't',
                InputOption::VALUE_OPTIONAL,
                'The package type (deploy|test)',
                'deploy'
            )
            ->addOption(
                'pr',
                'p',
                InputOption::VALUE_OPTIONAL,
                'The Pull Request ID'
            );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     * @throws \Continuous\Sdk\Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);

        $packageType = $input->getOption('type');
        $buildId = $input->getOption('id');
        $destination = getcwd() . '/' . $input->getArgument('destination');

        if (false === file_exists($destination)) {
            return $output->writeln(
                "<error>ERROR : directory $destination not exist</error>"
            );
        }

        if (!$buildId && $latestBuild = $this->findLastBuildId($input, $output)) {
            $buildId = $latestBuild->get('buildId');
        }

        if (false === in_array($packageType, static::PACKAGE_TYPES)) {
            return $output->writeln(
                "<error>ERROR : package type option must be <b>deploy</b> or <b>test</b> only</error>"
            );
        }

        if (!$buildId) {
            return $output->writeln(
                "<error>ERROR : no build ID has been found</error>"
            );
        }

        $package = $this->continuousClient->downloadPackage([
            'provider' => static::mapProviderToSdk($input->getArgument('provider')),
            'repository' => $input->getArgument('repository'),
            'buildId' => $buildId,
            'packageType' => $packageType,
            'destination' => getcwd() . '/' . $input->getArgument('destination')
        ]);

        $output->writeln('Package downloaded at ' . $package['path']);
    }

    /**
     * Find the latest build of repository
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return mixed|null
     */
    private function findLastBuildId(InputInterface $input, OutputInterface $output): ?Build
    {
        $pr = $input->getOption('pr');
        $filters = [
            'provider' => static::mapProviderToSdk($input->getArgument('provider')),
            'repository' => $input->getArgument('repository'),
            'result' => ['success', 'warning'],
            'state' => ['complete'],
            'exclude_pull_requests' => !$pr ? 1 : 0,
            'page_size' => 1,
        ];

        if ($input->hasOption('ref')) {
            $filters['pipeline_id'] = $input->getOption('ref');
        }

        if ($pr) {
            $filters['pull_request_id'] = (int)$pr;
        }

        /** @var Collection $builds $builds */
        $builds = $this->continuousClient->getBuilds($filters);

        return 0 === $builds->count() ? null : $builds->getIterator()->current();
    }
}
