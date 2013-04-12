<?php
/**
 * Zend Developer Tools for Zend Framework (http://framework.zend.com/)
 *
 * @link       http://github.com/zendframework/ZendDeveloperTools for the canonical source repository
 * @copyright  Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendDeveloperTools\Collector;

use Zend\Mvc\MvcEvent;

/**
 * Memory Data Collector.
 *
 */
class MemoryCollector extends AbstractCollector implements EventCollectorInterface
{
    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'memory';
    }

    /**
     * @inheritdoc
     */
    public function getPriority()
    {
        return PHP_INT_MAX - 1;
    }

    /**
     * @inheritdoc
     */
    public function collect(MvcEvent $mvcEvent)
    {
        if (!isset($this->data)) {
            $this->data = array();
        }

        $this->data['memory'] = memory_get_peak_usage(true);
        $this->data['end']    = memory_get_usage(true);
    }

    /**
     * Saves the current memory usage.
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

        $this->data['event'][$id][$name] = memory_get_usage(true);
    }

    /**
     * Returns the used Memory (peak)
     *
     * @return integer Memory
     */
    public function getMemory()
    {
        return $this->data['memory'];
    }

    /**
     * Event memory collected?
     *
     * @return integer Memory
     */
    public function hasEventMemory()
    {
        return isset($this->data['event']);
    }

    /**
     * Returns the detailed application memory.
     *
     * @return array
     */
    public function getApplicationEventMemory()
    {
        $result = array();

        if (!isset($this->data['event']['application'])) {
            return $result;
        }

        $app = $this->data['event']['application'];

        if (isset($app['bootstrap'])) {
            $result['request'] = $app['bootstrap'];
        }

        if (isset($app['bootstrap']) && isset($app['route'])) {
            $result['bootstrap'] = $app['route'] - $app['bootstrap'];
        }

        if (isset($app['dispatch']) && isset($app['route'])) {
            $result['route'] = $app['route'] - $app['dispatch'];
        }
        if (isset($app['dispatch.error']) && isset($app['route'])) {
            $result['route'] = $app['route'] - $app['dispatch.error'];
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
