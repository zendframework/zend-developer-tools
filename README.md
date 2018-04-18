# Zend Developer Tools

[![Build Status](https://secure.travis-ci.org/zendframework/zend-developer-tools.svg?branch=master)](https://secure.travis-ci.org/zendframework/zend-developer-tools)
[![Coverage Status](https://coveralls.io/repos/github/zendframework/zend-developer-tools/badge.svg?branch=master)](https://coveralls.io/github/zendframework/zend-developer-tools?branch=master)

Module providing debug tools for use with [zend-mvc](https://docs.zendframework.com/zend-mvc) applications.

## Installation

1. Install the module via composer by running:

   ```bash
   $ composer require --dev zendframework/zend-developer-tools
   ```

   or download it directly from github and place it in your application's `module/` directory.

2. Add the `ZendDeveloperTools` module to the module section of your `config/application.config.php`.
   Starting with version 1.1.0, if you are using [zend-component-installer](https://docs.zendframework.com/zend-component-installer),
   this will be done for you automatically.

3. Copy `./vendor/zendframework/zend-developer-tools/config/zenddevelopertools.local.php.dist` to
   `./config/autoload/zenddevelopertools.local.php`. Change any settings in it
   according to your needs.

## Extensions

- [BjyProfiler](https://github.com/bjyoungblood/BjyProfiler) - profile `Zend\Db` queries
- [OcraServiceManager](https://github.com/Ocramius/OcraServiceManager) - track dependencies within your application
- [SanSessionToolbar](https://github.com/samsonasik/SanSessionToolbar) - preview `Zend\Session` data
- [ZfSnapEventDebugger](https://github.com/snapshotpl/ZfSnapEventDebugger) - debug events from `Zend\EventManager`
- [DoctrineORMModule](https://github.com/doctrine/DoctrineORMModule) - profile `DoctrineORM` queries
- [JhuZdtLoggerModule](https://github.com/jhuet/JhuZdtLoggerModule) - log data from `Zend\Log`
- [aist-git-tools](https://github.com/ma-si/aist-git-tools) - informations about current GIT repository
