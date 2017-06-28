<a href="http://continuous.lu">
  <img src="https://app.continuousphp.com/assets/logos/continuousphp.svg" alt="ContinuousPHP" width="250px" align="right"/>
</a>

<p align="left">
  <a href="https://continuousphp.com/git-hub/continuousphp/cli"><img alt="Build Status" src="https://status.continuousphp.com/git-hub/continuousphp/cli?token=9800bb61-98f2-447d-a331-025f0b9af298" /></a>
 <img src="https://img.shields.io/badge/version-alpha-red.svg" alt="Version" />
 <a href="https://packagist.org/packages/continuousphp/cli"><img src="https://img.shields.io/packagist/dt/continuousphp/cli.svg" alt="Packagist" /></a>
</p>
<p align="left">
    ContinuousPHP© is the first and only PHP-centric PaaS to build, package, test and deploy applications in the same workflow.
</p>

# ContinuousPHP\Cli

CLI for the ContinuousPHP platform. Manage projects and build easily from your favorite terminal.

## Installation as Phar ( Recommended )

Download the latest version of continuousphpcli as a Phar:

```sh
$ curl -LSs https://continuousphp.github.io/cli/phar-installer.php | php
```

The command will check your PHP settings, warn you of any issues, and then download it to the current directory.
From there, you may place it anywhere you want to make it easier to access (such as `/usr/local/bin`) and chmod it to 755.
You can even rename it to just `continuousphpcli` to avoid having to type the .phar extension every time.

## Contributing

1. Fork it :clap:
2. Create your feature branch: `git checkout -b feat/my-new-feature`
3. Write your Unit and Functional tests
4. Commit your changes: `git commit -am 'Add some feature'`
5. Push to the branch: `git push origin feat/my-new-feature`
6. Submit a pull request with the details of your implementation
7. Take a drink during our review and merge :beers:

