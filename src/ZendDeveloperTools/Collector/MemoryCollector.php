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
 * @subpackage Collector
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendDeveloperTools\Collector;

use Zend\Mvc\MvcEvent;

/**
 * Memory Data Collector.
 *
 * @category   Zend
 * @package    ZendDeveloperTools
 * @subpackage Collector
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class MemoryCollector extends CollectorAbstract implements EventCollectorInterface
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
        $sc = $this->data['event']['application'];

        $result = array();
        $result['request']   = $sc['bootstrap'];
        $result['bootstrap'] = $sc['dispatch'] - $sc['bootstrap'];
        $result['dispatch']  = $sc['render'] - $sc['dispatch'];
        $result['render']    = $sc['finish'] - $sc['render'];
        $result['finish']    = $this->data['end'] - $sc['finish'];

        return $result;
    }
}
