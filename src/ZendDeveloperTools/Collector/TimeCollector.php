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
use Zend\EventManager\Event;

/**
 * Time Data Collector.
 *
 * @category   Zend
 * @package    ZendDeveloperTools
 * @subpackage Collector
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class TimeCollector extends CollectorAbstract implements EventCollectorInterface
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
        if (!isset($this->data['event']['application'])) {
            return array();
        }

        $sc = $this->data['event']['application'];

        $result = array();
        $result['request']   = $sc['bootstrap'] - $this->data['start'];
        $result['bootstrap'] = $sc['dispatch'] - $sc['bootstrap'];
        $result['dispatch']  = $sc['render'] - $sc['dispatch'];
        $result['render']    = $sc['finish'] - $sc['render'];
        $result['finish']    = $this->data['end'] - $sc['finish'];

        return $result;
    }
}