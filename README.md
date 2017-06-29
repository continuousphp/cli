<a href="http://continuous.lu">
  <img src="https://app.continuousphp.com/assets/logos/continuousphp.svg" alt="ContinuousPHP" width="250px" align="right"/>
</a>

<p align="left">
  <a href="https://continuousphp.com/git-hub/continuousphp/cli"><img alt="Build Status" src="https://status.continuousphp.com/git-hub/continuousphp/cli?token=8eb1b41e-343a-41b5-b68f-179fb1ce1ffe&branch=master" /></a>
</p>

<p align="left">
    ContinuousPHP© is the first and only PHP-centric PaaS to build, package, test and deploy applications in the same workflow.
</p>

# ContinuousPHP\Cli

CLI for the ContinuousPHP platform. Manage projects and build easily from your favorite terminal.

## Installation as Phar ( Recommended )

Download the latest version of continuousphp cli as a Phar:

```sh
$ curl -LSs https://continuousphp.github.io/cli/phar-installer.php | php
```

The command will check your PHP settings, warn you of any issues, and then download it to the current directory.
From there, you may place it anywhere you want to make it easier to access (such as `/usr/local/bin`) and chmod it to 755.
You can even rename it to just `continuousphp` to avoid having to type the .phar extension every time.

## Documentation

You can find Markdown documentation into `docs` subfolder or on web version at https://continuousphp.github.io/cli/doc
Thanks to open an issue if you see something missing in our documentation.

## Credit

This project was made based on Open-Source project, thanks to them!

 * [Box](https://github.com/box-project/box2) - PHAR builder
 * [Symfony\Console](https://github.com/symfony/console) - PHP Console Service
 * [Hoa\Console](https://github.com/hoaproject/Console) - PHP Console library

## Contributing

1. Fork it :clap:
2. Create your feature branch: `git checkout -b feat/my-new-feature`
3. Write your Unit and Functional tests
4. Commit your changes: `git commit -am 'Add some feature'`
5. Push to the branch: `git push origin feat/my-new-feature`
6. Submit a pull request with the details of your implementation
7. Take a drink during our review and merge :beers:

