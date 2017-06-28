# What is ContinuousPHP

ContinuousPHP is the first and only PHP-centric PaaS to build, package, test and deploy applications in the same workflow.

The ContinuousPHP CLI is command line interface for ContinuousPHP Platform.

## Installation

We recommand to use the php installer script to install latest version
of continuousphpcli PHAR.

    $ curl -LSs https://continuousphp.github.io/cli/phar-installer.php | php
    # Move the phar in your user bin directory
    $ mv continuousphpcli.phar /usr/local/bin/continuousphpcli

The command will check your PHP settings, warn you of any issues, and the download it to the current directory. 
From there, you may place it anywhere that will make it easier for you to access (such as `/usr/local/bin`) and chmod it to 755. 
You can even rename it to just `continuousphpcli` to avoid having to type the .phar extension every time.

## Configuration

By default, some of continuousphp api request do not require to be authenticate.
But you will certeinly need it for command that required permission, like start or stop build.

The cli implement a system of profile, to be able to use easily different continuousphp account.

each profile must be configured with the continuousphp user token, you can find a personal token
 on your credentials page at https://app.continuousphp.com/credentials

Configure new profile in interactive mode with this command

    $ continuousphpcli configure
     > Profile name [default]: myProfileName
     > User Token: XXXXXXXXXX
     < Profile myUserAccount saved in /home/user/.continuousphp/credentials

If you let profile name at `default`, the continuousphpcli will automatically use this credential.
Otherwise, you must specify the option `--profile myProfileName` on each command.

