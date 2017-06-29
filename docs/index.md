# What is ContinuousPHP

ContinuousPHP is the first and only PHP-centric PaaS to build, package, test and deploy applications in the same workflow.

The ContinuousPHP CLI is a command line interface for the ContinuousPHP Platform.

## Installation

We recommend using the php installer script to install the latest version
of continuousphp PHAR.

    $ curl -LSs https://continuousphp.github.io/cli/phar-installer.php | php
    # Move the phar in your user bin directory
    $ mv continuousphp.phar /usr/local/bin/continuousphp

The command will check your PHP settings, warn you of any issues, and then download it to the current directory.
From there, you may place it anywhere you want to make it easier to access (such as `/usr/local/bin`) and chmod it to 755.
You can even rename it to just `continuousphp` to avoid having to type the .phar extension every time.

## Configuration

By default, some of the continuousphp API requests do not require to be authenticated.
But you will certainly need to authenticate for commands that require permissions, like starting or stopping a build.

The cli implements a profile system to easily use different continuousphp accounts.

Each profile must be configured with the continuousphp user token. You can find a personal token
on your credentials page at https://app.continuousphp.com/credentials

Configure a new profile in interactive mode with this command:

    $ continuousphp configure
     > Profile name [default]: myProfileName
     > User Token: XXXXXXXXXX
     < Profile myUserAccount saved in /home/user/.continuousphp/credentials

If you choose `default` as the profile name, the continuousphp command will automatically use this credential.
Otherwise, you must specify the option `--profile myProfileName` on each command.
