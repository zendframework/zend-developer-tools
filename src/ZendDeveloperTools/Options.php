<?php
/**
 * ZendDeveloperTools
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    ZendDeveloperTools
 * @subpackage Option
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendDeveloperTools;

use Zend\Stdlib\AbstractOptions;

/**
 * @category   Zend
 * @package    ZendDeveloperTools
 * @subpackage Option
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
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
        'enabled' => false,
        'strict'  => true,
        'matcher' => array(
            'enabled' => false,
            'rules'   => array(),
        ),
    );

    /**
     * @var array
     */
    protected $collectors = array(
        'collectors' => array(
            'db'        => 'ZDT_Collector_Db',
            'event'     => 'ZDT_Collector_Event',
            'exception' => 'ZDT_Collector_Exception',
            'request'   => 'ZDT_Collector_Request',
            'memory'    => 'ZDT_Collector_Memory',
            'time'      => 'ZDT_Collector_Time',
        ),
        'options' => array(
            'time' => 'ZDT_Collector_Options_Time',
        ),
    );

    /**
     * @var array
     */
    protected $toolbar = array(
        'enabled'  => false,
        'position' => 'bottom',
        'entries'  => array(
            'time'   => 'zend-developer-tools/toolbar/time',
            'memory' => 'zend-developer-tools/toolbar/memory',
        )
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
        parent::__construct($options);

        $this->report = $report;
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
        if (isset($options['matcher'])) {
            $this->setProfilerMatcher($options['matcher']);
        }
    }

    /**
     * Sets Profiler matcher options.
     *
     * @param array $options
     */
    protected function setProfilerMatcher(array $options)
    {
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
                    $report->addError(sprintf(
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

    // todo: getter for matcher

    /**
     * Sets Collector options.
     *
     * @param array $options
     */
    public function setCollectors(array $options)
    {
        foreach (array('collectors' => true, 'options' => false) as $key => $unset) {
            if (isset($options['collectors'][$key])) {
                if (is_array($options['collectors'][$key])) {
                    foreach ($options['collectors'][$key] as $name => $collector) {
                        if (($collector === false || $collector === null) && $unset === true) {
                            unset($this->collectors['options'][$name]);
                            unset($this->collectors['collectors'][$name]);
                        } else {
                            $this->collectors[$key][$name] = $collector;
                        }
                    }
                } else {
                    $report->addError(sprintf(
                        '[\'zdt\'][\'collectors\'][\'%s\'] must be an array, %s given.',
                        $key,
                        gettype($options['collectors'][$key])
                    ));
                }
            }
        }
    }

    /**
     * Returns the collectors.
     *
     * @return array
     */
    public function getCollectors()
    {
        return $this->collectors['collectors'];
    }

    /**
     * Returns the collector options.
     *
     * @return array
     */
    public function getCollectorOptions()
    {
        return $this->collectors['options'];
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
        if (isset($options['position'])) {
            if ($rule['position'] !== 'bottom' && $rule['position'] !== 'top') {
                $report->addError(sprintf(
                    '[\'zdt\'][\'toolbar\'][\'position\'] must be "top" or "bottom", %s given.',
                    $rule['position']
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