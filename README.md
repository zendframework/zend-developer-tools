Zend Developer Tools
=====================

Module for developer and debug tools for working with the ZF2 MVC layer.
While this is still an early version, it is planned to be finished before Zend
Framework 2.0 stable.


Install
=======
1. Add the `ZendDeveloperTools` module to the module section of your
   application.config.php
2. Copy `ZendDeveloperTools/config/zenddevelopertools.local.php.dist` to
   `./config/autoload/zenddevelopertools.local.php`. Change the settings
   if you like to.
3. Add the following in your `index.php`:
   ```
   define('REQUEST_MICROTIME', microtime(true));
   ```

> **Note:** The displayed execution time in the toolbar will be highly inaccurate
            if you don't define `REQUEST_MICROTIME`.


If you wish to profile Zend\Db, you have to install and enable [BjyProfiler](https://github.com/bjyoungblood/BjyProfiler).
You can do so by running composer's `require` command.

    php composer.phar require bjyoungblood/BjyProfiler:dev-master

Zend Developer Tools will try to grab the Profiler from your Zend\Db adapter
instance, using the `Zend\Db\Adapter\Adapter` or `Zend\Db\Adapter\ProfilingAdapter`
service name.
