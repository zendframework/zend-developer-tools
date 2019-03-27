<?php
/**
 * @see       https://github.com/zendframework/zend-developer-tools for the canonical source repository
 * @copyright Copyright (c) 2011-2018 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-developer-tools/blob/master/LICENSE.md New BSD License
 */

namespace ZendDeveloperTools;

use Zend\Stdlib\AbstractOptions;

/**
 * @todo storage and firephp options
 */
class Options extends AbstractOptions
{
    /**
     * @var ReportInterface
     */
    protected $report;

    /**
     * @var array
     */
    protected $profiler = [
        'enabled'     => false,
        'strict'      => true,
        'flush_early' => false,
        'cache_dir'   => 'data/cache',
        'matcher'     => [],
        'collectors' => [
            'db'        => DbCollector::class,
            'exception' => ExceptionCollector::class,
            'request'   => RequestCollector::class,
            'config'    => ConfigCollector::class,
            'memory'    => MemoryCollector::class,
            'time'      => TimeCollector::class,
        ],
    ];

    /**
     * Defaults for event-level logging
     * @var array
     */
    protected $events = [
        'enabled'    => false,
        'collectors' => [
            'memory' => MemoryCollector::class,
            'time'   => TimeCollector::class,
        ],
        'identifiers' => [
            'all' => '*'
        ]
    ];

    /**
     * @var array
     */
    protected $toolbar = [
        'enabled'       => false,
        'auto_hide'     => false,
        'position'      => 'bottom',
        'version_check' => false,
        'entries'       => [
            'request' => 'zend-developer-tools/toolbar/request',
            'time'    => 'zend-developer-tools/toolbar/time',
            'memory'  => 'zend-developer-tools/toolbar/memory',
            'config'  => 'zend-developer-tools/toolbar/config',
            'db'      => 'zend-developer-tools/toolbar/db',
        ],
    ];

    /**
     * Overloading Constructor.
     *
     * @param  array|Traversable|null $options
     * @param  ReportInterface        $report
     * @throws \Zend\Stdlib\Exception\InvalidArgumentException
     */
    public function __construct($options, ReportInterface $report)
    {
        $this->report = $report;

        parent::__construct($options);
    }

    /**
     * Sets Profiler options.
     *
     * @param array $options
     */
    public function setProfiler(array $options)
    {
        foreach ($options as $key => $value) {
            switch ($key) {
                case 'enabled':
                    // fall-through
                case 'strict':
                    // fall-through
                case 'flush_early':
                    $this->profiler[$key] = (bool) $value;
                    break;
                case 'cache_dir':
                    $this->profiler[$key] = (string) $value;
                    break;
                case 'matcher':
                    $this->setMatcher($value);
                    break;
                case 'collectors':
                    $this->setCollectors($value);
                    break;
                default:
                    // unknown option
                    break;
            }
        }
    }

    /**
     * Sets Event-level profiling options.
     *
     * @param array $options
     */
    public function setEvents(array $options)
    {
        if (isset($options['enabled'])) {
            $this->events['enabled'] = (bool) $options['enabled'];
        }

        if (isset($options['collectors'])) {
            $this->setEventCollectors($options['collectors']);
        }

        if (isset($options['identifiers'])) {
            $this->setEventIdentifiers($options['identifiers']);
        }
    }


    /**
     * Sets Profiler matcher options.
     *
     * @param array $options
     */
    protected function setMatcher($options)
    {
        if (! is_array($options)) {
            $this->report->addError(sprintf(
                "['zenddevelopertools']['profiler']['matcher'] must be an array, %s given.",
                gettype($options)
            ));
            return;
        }

        $this->profiler['matcher'] = $options;
    }

    /**
     * Sets Profiler collectors options.
     *
     * @param array $options
     */
    protected function setCollectors($options)
    {
        if (! is_array($options)) {
            $this->report->addError(sprintf(
                "['zenddevelopertools']['profiler']['collectors'] must be an array, %s given.",
                gettype($options)
            ));
            return;
        }

        foreach ($options as $name => $collector) {
            if (($collector === false || $collector === null)) {
                unset($this->profiler['collectors'][$name]);
                continue;
            }

            $this->profiler['collectors'][$name] = $collector;
        }
    }

    /**
     * Is the Profiler enabled?
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->profiler['enabled'];
    }

    /**
     * Sets Event-level collectors.
     *
     * @param array $options
     */
    public function setEventCollectors(array $options)
    {
        if (! is_array($options)) {
            $this->report->addError(sprintf(
                "['zenddevelopertools']['events']['collectors'] must be an array, %s given.",
                gettype($options)
            ));
            return;
        }

        foreach ($options as $name => $collector) {
            if (($collector === false || $collector === null)) {
                unset($this->events['collectors'][$name]);
                continue;
            }

            $this->events['collectors'][$name] = $collector;
        }
    }

    /**
     * Set Event-level collectors to listen to certain event identifiers. Defaults to '*' which causes the listener to
     * attach to all events.
     *
     * @param array $options
     */
    public function setEventIdentifiers(array $options)
    {
        if (! is_array($options)) {
            $this->report->addError(sprintf(
                '[\'zenddevelopertools\'][\'events\'][\'identifiers\'] must be an array, %s given.',
                gettype($options)
            ));
            return;
        }

        foreach ($options as $name => $identifier) {
            if (($identifier === false || $identifier === null)) {
                unset($this->events['identifiers'][$name]);
                continue;
            }

            $this->events['identifiers'][$name] = $identifier;
        }
    }

    /**
     * Is the event-level statistics collection enabled?
     *
     * @return bool
     */
    public function eventCollectionEnabled()
    {
        return $this->events['enabled'];
    }

    /**
     * Is strict mode enabled?
     *
     * @return bool
     */
    public function isStrict()
    {
        return $this->profiler['strict'];
    }

    /**
     * Is it allowed to flush the page before the collector runs?
     *
     * Note: Only possible if the toolbar, firephp and the strict mode is
     *       disabled.
     *
     * @return bool
     */
    public function canFlushEarly()
    {
        return ($this->profiler['flush_early']
            && ! $this->profiler['strict']
            && ! $this->toolbar['enabled']);
    }

    /**
     * Returns the cache directory that is used to store the version cache or
     * any report storage that writes to the disk.
     *
     * @return string
     */
    public function getCacheDir()
    {
        return $this->profiler['cache_dir'];
    }

    // todo: getter for matcher

    /**
     * Returns the collectors.
     *
     * @return array
     */
    public function getCollectors()
    {
        return $this->profiler['collectors'];
    }

    /**
     * Returns the event-level collectors.
     *
     * @return array
     */
    public function getEventCollectors()
    {
        return $this->events['collectors'];
    }

    /**
     * Returns the event identifiers.
     *
     * @return array
     */
    public function getEventIdentifiers()
    {
        return $this->events['identifiers'];
    }


    /**
     * Sets Toolbar options.
     *
     * @param array $options
     */
    public function setToolbar(array $options)
    {
        foreach ($options as $key => $value) {
            switch ($key) {
                case 'enabled':
                    // fall-through
                case 'auto_hide':
                    // fall-through
                case 'version_check':
                    $this->toolbar[$key] = (bool) $value;
                    break;
                case 'position':
                    if ($value !== 'bottom' && $value !== 'top') {
                        $this->report->addError(sprintf(
                            "['zenddevelopertools']['toolbar']['position'] must be 'top' or 'bottom', %s given.",
                            $value
                        ));
                        break;
                    }
                    $this->toolbar[$key] = $value;
                    break;
                case 'entries':
                    if (! is_array($value)) {
                        $this->report->addError(sprintf(
                            "['zenddevelopertools']['toolbar']['entries'] must be an array, %s given.",
                            gettype($value)
                        ));
                    }

                    foreach ($value as $collector => $template) {
                        if ($template === false || $template === null) {
                            unset($this->toolbar[$key][$collector]);
                            continue;
                        }

                        $this->toolbar[$key][$collector] = $template;
                    }

                    break;
                default:
                    // Unknown type; ignore
                    break;
            }
        }
    }

    /**
     * Is the Toolbar enabled?
     *
     * @return bool
     */
    public function isToolbarEnabled()
    {
        return $this->toolbar['enabled'];
    }

    /**
     * Is the Zend Framework version check enabled?
     *
     * @return bool
     */
    public function isVersionCheckEnabled()
    {
        return $this->toolbar['version_check'];
    }

    /**
     * Can hide Toolbar entries?
     *
     * @return bool
     */
    public function getToolbarAutoHide()
    {
        return $this->toolbar['auto_hide'];
    }

    /**
     * Returns the Toolbar position.
     *
     * @return array
     */
    public function getToolbarPosition()
    {
        return $this->toolbar['position'];
    }

    /**
     * Returns the Toolbar entries.
     *
     * @return array
     */
    public function getToolbarEntries()
    {
        return $this->toolbar['entries'];
    }
}
