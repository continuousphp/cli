<?php

namespace Continuous\Cli\Command\Pipeline;

use Continuous\Cli\Command\CommandAbstract;
use Continuous\Sdk\Collection;
use Continuous\Sdk\Decorator\Pipeline\PipelineExportDecorator;
use Continuous\Sdk\Entity\Pipeline;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class PipelineExportCommand extends CommandAbstract
{
    protected function configure()
    {
        $this
            ->setName('pipeline:export')
            ->setDescription('Export pipeline as configuration file.')
            ->setHelp('This command export a specific pipeline as a configuration file in YAML.')
            ->addArgument('provider', InputArgument::REQUIRED, 'The repository provider')
            ->addArgument('repository', InputArgument::REQUIRED, 'The repository name')
            ->addArgument('ref', InputArgument::REQUIRED, 'The git reference')
        ;

        $this
            ->addOption(
                'output',
                'o',
                InputOption::VALUE_OPTIONAL,
                'File where yaml output will be persisted'
            );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);

        $outFd = 'php://stdout';

        if (null !== ($outOpt = $input->getOption('output'))) {
            $outFd = getcwd() . DIRECTORY_SEPARATOR . $outOpt;
        }

        $ref = $input->getArgument('ref');
        $params = [
            'provider' => static::mapProviderToSdk($input->getArgument('provider')),
            'repository' => $input->getArgument('repository'),
            'ref' => $input->getArgument('ref'),
        ];

        $this->showLoader($output, 'Loading pipelines...');

        /** @var Collection $collection */
        $collection = $this->continuousClient->getPipelines($params);
        $entity = null;

        foreach ($collection->getIterator() as $entity) {
            /** @var Pipeline $entity */
            if ($ref === $entity->get('settingId')) {
                break;
            }

            $entity = null;
        }

        $exportDecorator = new PipelineExportDecorator($entity);

        $this->hideLoader($output);
        file_put_contents($outFd, $exportDecorator->toYaml());
    }
}
