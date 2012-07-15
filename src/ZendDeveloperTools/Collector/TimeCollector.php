<?php
/**
 * Zend Developer Tools for Zend Framework (http://framework.zend.com/)
 *
 * @link       http://github.com/zendframework/ZendDeveloperTools for the canonical source repository
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd New BSD License
 * @package    ZendDeveloperTools
 * @subpackage Collector
 */

namespace ZendDeveloperTools\Collector;

use Zend\Mvc\MvcEvent;
use Zend\EventManager\Event;

/**
 * Time Data Collector.
 *
 * @category   Zend
 * @package    ZendDeveloperTools
 * @subpackage Collector
 */
class TimeCollector extends AbstractCollector implements EventCollectorInterface
{
    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'time';
    }

    /**
     * @inheritdoc
     */
    public function getPriority()
    {
        return PHP_INT_MAX;
    }

    /**
     * @inheritdoc
     */
    public function collect(MvcEvent $mvcEvent)
    {
        if (defined('REQUEST_MICROTIME')) {
            $start = REQUEST_MICROTIME;
        } else {
            $start = $mvcEvent->getRequest()->getServer()->get('REQUEST_TIME');
        }

        if (!isset($this->data)) {
            $this->data = array();
        }

        $this->data['start'] = $start;
        $this->data['end']   = microtime(true);
    }

    /**
     * Saves the current time in microseconds for a specific event.
     *
     * @param string $id
     * @param string $name
     */
    public function collectEvent($id, $name)
    {
        if (!isset($this->data)) {
            $this->data = array();
        }
        if (!isset($this->data['event'])) {
            $this->data['event'] = array();
        }
        if (!isset($this->data['event'][$id])) {
            $this->data['event'][$id] = array();
        }

        $this->data['event'][$id][$name] = microtime(true);
    }

    /**
     * Returns the total execution time.
     *
     * @return float
     */
    public function getStartTime()
    {
        return $this->data['start'];
    }

    /**
     * Returns the total execution time.
     *
     * @return float
     */
    public function getExecutionTime()
    {
        return $this->data['end'] - $this->data['start'];
    }

    /**
     * Event times collected?
     *
     * @return boolean
     */
    public function hasEventTimes()
    {
        return isset($this->data['event']);
    }

    /**
     * Returns the detailed application execution time.
     *
     * @return array
     */
    public function getApplicationEventTimes()
    {
        $result = array();

        if (!isset($this->data['event']['application'])) {
            return $result;
        }

        $app = $this->data['event']['application'];

        if (isset($app['bootstrap'])) {
            $result['request'] = $app['bootstrap'] - $this->data['start'];
        }

        if (isset($app['bootstrap']) && isset($app['route'])) {
            $result['bootstrap'] = $app['route'] - $app['bootstrap'];
        }

        if (isset($app['dispatch']) && isset($app['route'])) {
            $result['route'] = $app['dispatch']- $app['route'];
        }
        if (isset($app['dispatch.error']) && isset($app['route'])) {
            $result['route'] = $app['dispatch.error']- $app['route'];
        }

        if (isset($app['dispatch']) && isset($app['render'])) {
            $result['dispatch'] = $app['render'] - $app['dispatch'];
        }
        if (isset($app['dispatch.error']) && isset($app['render'])) {
            $result['dispatch (e)'] = $app['render'] - $app['dispatch.error'];
        }

        if (isset($app['render']) && isset($app['finish'])) {
            $result['render'] = $app['finish'] - $app['render'];
        }

        if (isset($app['finish'])) {
            $result['finish'] = $this->data['end'] - $app['finish'];
        }

        return $result;
    }
}