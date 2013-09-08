Zend Developer Tools
====================

Module providing debug tools for working with the [Zend Framework 2](https://github.com/zendframework/zf2) MVC
layer.

Installation
============

1. Install the module via composer by running:

   ```sh
   php composer.phar require zendframework/zend-developer-tools:dev-master
   ```
   or download it directly from github and place it in your application's `module/` directory.
2. Add the `ZendDeveloperTools` module to the module section of your `config/application.config.php`
3. Copy `ZendDeveloperTools/config/zenddevelopertools.local.php.dist` to
   `./config/autoload/zenddevelopertools.local.php`. Change any settings in it
   according to your needs.


If you wish to profile `Zend\Db` queries, you will have to install and enable
[BjyProfiler](https://github.com/bjyoungblood/BjyProfiler).
