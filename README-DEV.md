Road map
========

* **Collectors**
    * Allow third-party collectors via ServiceManager/PluginManager. [Done]
    * Every collector runs after `MvcEvent::EVENT_FINISH`. [Done]
        * Possible exceptions are the `TimeCollector` and `MemoryCollector`.
          They could listens to every `MvcEvent` to provide more accurate data.
          This behavior can be turned off via config. [Done]

          > Note: This will most likely be removed due to event manager profiling
                  improvements.
    * Listen with a low priority and flush the response before the collector
      starts collecting if the Toolbar/Wildfire is disabled && the profiler is
      in silence mode. [Done]
    * Default Collectors
        * Db (`Zend\Db`) [Done]
        * Event
        * Time [Done]
        * Mail
        * Memory [Done]
        * Request [partly done – still missing features for the In-depth Web Profile Viewer]
* **Configuration**
    * Dis/enable every collector via config. [Done]
    * Dis/enable a strict error handling (throw exceptions). If the strict mode
      is disabled, every error will be saved in the report. [Done]
    * Matcher: Dis/enable the profiler if the request matches your pattern.
      Possible matches would be request url/path, ip, time, access role.
      The "matcher" should support custom matches.
* **Report**
    * In-depth Web Profile Viewer
        * Requires saved reports or an imported report.
        * Every collection (even third-party) should provide a template.
    * Web Toolbar
        * Resizable toolbar – hides some parts on lower resolutions/window
          sizes. [Done]
        * Template overloading, so you can add your preferred collector data to
          the toolbar. [Done]
    * Storage
        * Support for Redis, MySQL (`mysqli`, `PDO` or `Zend\Db`?), SQLite, MongoDB
        * Possible adapters for memchache, PostgreSQL, CouchDB – probably at a
          later stage
    * Wildfire
        * Provides the same information as the web toolbar, but displays it in
          the browser console.
        * Support for FirePHP and ChromePHP as of Zend Framework 2.1