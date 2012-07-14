<?php
/**
 * Zend Developer Tools for Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendDeveloperTools for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   ZendDeveloperTools
 */

namespace ZendDeveloperTools;

use Zend\Stdlib\AbstractOptions;

/**
 * @category   Zend
 * @package    ZendDeveloperTools
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
    protected $profiler = array(
        'enabled'     => false,
        'strict'      => true,
        'verbose'     => false,
        'flush_early' => false,
        'cache_dir'   => 'data/cache',
        'matcher'     => array(
            'enabled' => false,
            'rules'   => array(),
        ),
        'collectors' => array(
            'db'        => 'ZDT_DbCollector',
            'event'     => 'ZDT_EventCollector',
            'exception' => 'ZDT_ExceptionCollector',
            'request'   => 'ZDT_RequestCollector',
            'mail'      => 'ZDT_MailCollector',
            'memory'    => 'ZDT_MemoryCollector',
            'time'      => 'ZDT_TimeCollector',
        ),
        'verbose_listeners' => array(
            'application' => array(
                'ZDT_TimeCollectorListener'   => true,
                'ZDT_MemoryCollectorListener' => true,
            ),
        ),
    );

    /**
     * @var array
     */
    protected $toolbar = array(
        'enabled'       => false,
        'auto_hide'     => false,
        'position'      => 'bottom',
        'version_check' => false,
        'entries'       => array(
            'request' => 'zend-developer-tools/toolbar/request',
            'time'    => 'zend-developer-tools/toolbar/time',
            'memory'  => 'zend-developer-tools/toolbar/memory',
            'db'      => 'zend-developer-tools/toolbar/db',
            'mail'    => 'zend-developer-tools/toolbar/mail',
        ),
    );

    /**
     * Overloading Constructor.
     *
     * @param  array|Traversable|null $options
     * @param  ReportInterface        $report
     * @throws \Zend\Stdlib\Exception\InvalidArgumentException
     */
    public function __construct($options = null, ReportInterface $report)
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
        if (isset($options['enabled'])) {
            $this->profiler['enabled'] = (boolean) $options['enabled'];
        }
        if (isset($options['strict'])) {
            $this->profiler['strict'] = (boolean) $options['strict'];
        }
        if (isset($options['verbose'])) {
            $this->profiler['verbose'] = (boolean) $options['verbose'];
        }
        if (isset($options['flush_early'])) {
            $this->profiler['flush_early'] = (boolean) $options['flush_early'];
        }
        if (isset($options['cache_dir'])) {
            $this->profiler['cache_dir'] = (string) $options['cache_dir'];
        }
        if (isset($options['matcher'])) {
            $this->setMatcher($options['matcher']);
        }
        if (isset($options['collectors'])) {
            $this->setCollectors($options['collectors']);
        }
        if (isset($options['verbose_listeners'])) {
            $this->setVerboseListeners($options['verbose_listeners']);
        }
    }

    /**
     * Sets Profiler matcher options.
     *
     * @param array $options
     */
    protected function setMatcher($options)
    {
        if (!is_array($options)) {
            $report->addError(sprintf(
                '[\'zdt\'][\'profiler\'][\'matcher\'] must be an array, %s given.',
                gettype($options)
            ));

            return;
        }

        if (isset($options['enabled'])) {
            $this->profiler['matcher']['enabled'] = (boolean) $options['enabled'];
        }
        if (isset($options['rules']) && is_array($options['rules'])) {
            $arrayPath = '[\'zdt\'][\'profiler\'][\'matcher\']';
            $added     = array();

            foreach ($options['rules'] as $name => $rule) {
                if (is_array($rule)) {
                    $added[] = $name;

                    $this->profiler['matcher']['rules'][$name] = array();

                    if (isset($rule['action'])) {
                        if ($rule['action'] !== 'enable' && $rule['action'] !== 'disable') {
                            $report->addError(sprintf(
                                '%s[\'rules\'][\'%s\'][\'action\'] must be "enable" or "disable", %s given.',
                                $arrayPath,
                                $name,
                                $rule['action']
                            ));
                        } else {
                            $this->profiler['matcher']['rules'][$name]['action'] = $rule['action'];
                        }
                    } else {
                        $this->profiler['matcher']['rules'][$name]['action'] = 'enable';
                    }

                    if (isset($rule['match'])) {
                        if (!is_array($rule['match'])) {
                            $report->addError(sprintf(
                                '%s[\'rules\'][\'%s\'][\'match\'] must be an array, %s given.',
                                $arrayPath,
                                gettype($rule['match'])
                            ));
                        } else {
                            $this->profiler['matcher']['rules'][$name]['match'] = $rule['action'];
                        }
                    }
                } else {
                    $this->report->addError(sprintf(
                        '%s[\'rules\'][\'%s\'] must be an array, %s given.',
                        $arrayPath,
                        $name,
                        gettype($rule)
                    ));
                }
            }

            foreach ($added as $name) {
                if (!isset($this->profiler['matcher']['rules'][$name]['match'])) {
                    unset($this->profiler['matcher']['rules'][$name]);
                }
            }
        } else {
            $report->addError(sprintf(
                '[\'zdt\'][\'profiler\'][\'matcher\'][\'rules\'] must be an array, %s given.',
                gettype($options['rules'])
            ));
        }
    }

    /**
     * Sets Profiler collectors options.
     *
     * @param array $options
     */
    protected function setCollectors($options)
    {
        if (!is_array($options)) {
            $report->addError(sprintf(
                '[\'zdt\'][\'profiler\'][\'collectors\'] must be an array, %s given.',
                gettype($options)
            ));

            return;
        }

        foreach ($options as $name => $collector) {
            if (($collector === false || $collector === null)) {
                unset($this->profiler['collectors'][$name]);
            } else {
                $this->profiler['collectors'][$name] = $collector;
            }
        }
    }

    /**
     * Sets verbose listener options.
     *
     * @param array $options
     */
    protected function setVerboseListeners($options)
    {
        if (!is_array($options)) {
            $report->addError(sprintf(
                '[\'zdt\'][\'profiler\'][\'verbose_listeners\'] must be an array, %s given.',
                gettype($options)
            ));

            return;
        }

        foreach ($options as $id => $listeners) {
            if (!is_array($listeners)) {
                $report->addError(sprintf(
                    '[\'zdt\'][\'profiler\'][\'verbose_listeners\'][\'%s\'] must be an array, %s given.',
                    $id,
                    gettype($listeners)
                ));

                continue;
            }

            foreach ($listeners as $service => $mode) {
                if (!isset($this->profiler['verbose_listeners'][$id])) {
                    $this->profiler['verbose_listeners'][$id] = array();
                }

                $this->profiler['verbose_listeners'][$id][$service] = (boolean) $mode;
            }
        }
    }

    /**
     * Is the Profiler enabled?
     *
     * @return boolean
     */
    public function isEnabled()
    {
        return $this->profiler['enabled'];
    }

    /**
     * Is strict mode enabled?
     *
     * @return boolean
     */
    public function isStrict()
    {
        return $this->profiler['strict'];
    }

    /**
     * Is it allowed to flush the page before the collector runs?
     * Note: Only possible if the toolbar and firephp is disabled!
     *
     * @return boolean
     */
    public function canFlushEarly()
    {
        return ($this->profiler['flush_early'] && !$this->toolbar['enabled']);
    }

    /**
     * Is the verbose mode actived?
     *
     * @return boolean
     */
    public function isVerbose()
    {
        return $this->profiler['verbose'];
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
     * Returns the verbose listeners.
     *
     * @return array
     */
    public function getVerboseListeners()
    {
        return $this->profiler['verbose_listeners'];
    }

    /**
     * Sets Toolbar options.
     *
     * @param array $options
     */
    public function setToolbar(array $options)
    {
        if (isset($options['enabled'])) {
            $this->toolbar['enabled'] = (boolean) $options['enabled'];
        }

        if (isset($options['version_check'])) {
            $this->profiler['version_check'] = (boolean) $options['version_check'];
        }
        if (isset($options['position'])) {
            if ($options['position'] !== 'bottom' && $options['position'] !== 'top') {
                $report->addError(sprintf(
                    '[\'zdt\'][\'toolbar\'][\'position\'] must be "top" or "bottom", %s given.',
                    $options['position']
                ));
            } else {
                $this->toolbar['position'] = $options['position'];
            }
        }
        if (isset($options['entries'])) {
            if (is_array($options['entries'])) {
                foreach ($options['entries'] as $collector => $template) {
                    if ($template === false || $template === null) {
                        unset($this->toolbar['entries'][$collector]);
                    } else {
                        $this->toolbar['entries'][$collector] = $template;
                    }
                }
            } else {
                $report->addError(sprintf(
                    '[\'zdt\'][\'toolbar\'][\'entries\'] must be an array, %s given.',
                    gettype($options['entries'])
                ));
            }
        }
    }

    /**
     * Is the Toolbar enabled?
     *
     * @return boolean
     */
    public function isToolbarEnabled()
    {
        return $this->toolbar['enabled'];
    }

    /**
     * Is the Zend Framework version check enabled?
     *
     * @return boolean
     */
    public function isVersionCheckEnabled()
    {
        return $this->toolbar['version_check'];
    }

    /**
     * Can hide Toolbar entries?
     *
     * @return boolean
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

    // todo: storage and firephp options.
}
