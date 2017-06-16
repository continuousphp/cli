<?php

namespace Continuous\Cli\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class ConfigureCommand extends CommandAbstract
{
    protected function configure()
    {
        $this
            ->setName('configure')
            ->setDescription('Configure cphp profile.')
            ->setHelp('This command help you to create multiple profile corresponding to cphp user token. Can be found at https://app.continuousphp.com/credentials')
        ;

        $this
            ->addOption(
                'profile',
                null,
                InputOption::VALUE_OPTIONAL,
                'Profile name'
            );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);

        $this->obtainProfileToken($input, $output, $profile, $token);
        $path = $this->saveProfile($profile, $token);

        $output->writeln("Profile $profile saved in $path");
    }

    protected function obtainProfileToken(InputInterface $input, OutputInterface $output, & $profile, & $token)
    {
        $profile = $input->getOption('profile');
        $token = $input->getOption('token');

        if (true === $input->getOption('no-interaction')) {
            if (!$profile && !$token) {
                $output->writeln(
                    "<error>ERROR : no-interaction was specified, you must declare profile and token as option</error>"
                );
            }

            return;
        }

        $helper = $this->getHelper('question');

        if (null === $profile) {
            $profile = $helper->ask(
                $input, $output,
                new Question('Profile name [default]: ', 'default')
            );
        }

        if (null === $token) {
            $token = $helper->ask(
                $input, $output,
                new Question('User Token: ')
            );
        }
    }

    protected function saveProfile($profile, $token)
    {
        $profiles = self::getProfiles();
        $profiles[$profile] = [
            'token' => $token
        ];

        $path = static::getCredentialsPath();
        $pathDir = dirname($path);

        if (!file_exists($pathDir)) {
            mkdir($pathDir);
        }

        $handle = fopen($path, 'w+');

        if (false === is_resource($handle)) {
            throw new \Exception("Error during opening/creating credentials file at $path");
        }

        foreach ($profiles as $name => $profile) {
            fwrite($handle, "[$name]\n");

            foreach ($profile as $k => $v) {
                fwrite($handle, "$k = $v\n");
            }
        }

        fclose($handle);

        return $path;
    }

    protected static function getCredentialsPath()
    {
        $home = getenv('HOME');

        if (empty($home)) {
            $home = '~';
        }

        return "{$home}/.continuousphp/credentials";
    }

    public static function getProfiles()
    {
        $path = static::getCredentialsPath();

        if (false === file_exists($path)) {
            return [];
        }

        return parse_ini_file($path, true);
    }

    public static function getToken($profile)
    {
        $profiles = static::getProfiles();

        return !empty($profiles[$profile]) ? $profiles[$profile]['token'] : null;
    }
}