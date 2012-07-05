Module for developer and debug tools for working with the ZF2 MVC layer.


Copy `ZendDeveloperTools/config/zenddevelopertools.local.php.dist` to ./config/autoload/zenddevelopertools.local.php.


Redux
=====

* **Collectors**
    * Allow third-party collectors via ServiceManager/PluginManager.
    * Every collector runs after `MvcEvent::EVENT_FINISH`.
        * Possible exceptions are the `TimeCollector` and `MemoryCollector`. They could listens to every `MvcEvent` to provide more accurate data. This behavior can be turned off via config.
    * Listen with a low priority and flush the response before the collector starts collecting if the web toolbar/FirePHP is disabled && the profiler is in silence mode.
    * Default Collectors
        * Db (`Zend\Db`)
        * Event
        * Time
        * Memory
        * Route
        * Request
        * Configuration ¹?
* **Configuration**
    * Dis/enable every collector via config.
    * Dis/enable a strict error handling (throw exceptions). If the strict mode is disabled, every error will be saved in the report.
    * Matcher: Dis/enable the profiler if the request matches your pattern. Possible matches would be request url/path, ip, time, access role. The "matcher" should support custom matches.
* **Report**
    * In-depth Web Profile Viewer
        * Requires saved reports or an imported report.
        * Every collection (even third-party) should provide a template.
    * Web Toolbar
        * Listens to the `ProfilerEvent::EVENT_COLLECTED`.
        * Resizable toolbar – hides some parts on lower resolutions/window sizes.
        * Template overloading, so you can add your preferred collector data to the toolbar ².
    * Storage
        * Listens to the `ProfilerEvent::EVENT_COLLECTED`.
        * Support for memchache(d)?, Redis, MySQL (`mysqli`, `PDO` or `Zend\Db`?), SQLite, PostgreSQL?, MongoDB, CouchDB?
    * FirePHP?
        * Listens to the `ProfilerEvent::EVENT_COLLECTED`.
        * Provides the same information as the web toolbar.
* **CLI ³**
    * Debug helper for routes, config etc.?
    * Developer Sever (obviously PHP >=5.4 only)?


¹) Which config key got overwritten by which module. Won't save any config values in the report!
²) Don't know yet how templates in ZF2 work and if it is possible to achieve this in a nice fashion.
³) Does that interfere with ZF2 Tool?
