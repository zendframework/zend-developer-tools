Module for developer and debug tools for working with the ZF2 MVC layer.


Install
=======

Copy `ZendDeveloperTools/config/zenddevelopertools.local.php.dist` to ./config/autoload/zenddevelopertools.local.php.

In your `index.php` add the following:

    define('REQUEST_MICROTIME', microtime(true));


If you wish to profile Zend\Db, you have to install and enable [BjyProfiler](https://github.com/bjyoungblood/BjyProfiler). You can do so by running composer's `require` command.

    php composer.phar require bjyoungblood/BjyProfiler:dev-master

Zend Developer Tools will try to grab the Profiler from your Zend\Db adapter instance, using the `Zend\Db\Adapter\Adapter` or `ZDT_Zend_Db` service name.

Redux
=====

* **Collectors**
    * Allow third-party collectors via ServiceManager/PluginManager. [Done]
    * Every collector runs after `MvcEvent::EVENT_FINISH`. [Done]
        * Possible exceptions are the `TimeCollector` and `MemoryCollector`. They could listens to every `MvcEvent` to provide more accurate data. This behavior can be turned off via config. [Done]
    * Listen with a low priority and flush the response before the collector starts collecting if the Toolbar/FirePHP is disabled && the profiler is in silence mode. [Done]
    * Default Collectors
        * Db (`Zend\Db`)
        * Event
        * Time
        * Mail
        * Memory
        * Request
        * Configuration ¹
* **Configuration**
    * Dis/enable every collector via config. [Done]
    * Dis/enable a strict error handling (throw exceptions). If the strict mode is disabled, every error will be saved in the report. [Done]
    * Matcher: Dis/enable the profiler if the request matches your pattern. Possible matches would be request url/path, ip, time, access role. The "matcher" should support custom matches.
* **Report**
    * In-depth Web Profile Viewer
        * Requires saved reports or an imported report.
        * Every collection (even third-party) should provide a template.
    * Web Toolbar
        * Resizable toolbar – hides some parts on lower resolutions/window sizes. [Done]
        * Template overloading, so you can add your preferred collector data to the toolbar. [Done]
    * Storage
        * Support for memchache(d)?, Redis, MySQL (`mysqli`, `PDO` or `Zend\Db`?), SQLite, PostgreSQL?, MongoDB, CouchDB?
    * FirePHP
        * Provides the same information as the web toolbar.
* **CLI**
    * Debug helper for routes, config, template etc.


¹) Which config key got overwritten by which module. Won't save any config values in the report!
