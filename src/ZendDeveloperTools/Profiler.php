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
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendDeveloperTools;

use Zend\Mvc\MvcEvent;
use Zend\Stdlib\PriorityQueue;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\EventManagerAwareInterface;

/**
 * @category   Zend
 * @package    ZendDeveloperTools
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Profiler implements EventManagerAwareInterface
{
    /**
     * Event collector listener priority.
     *
     * @var int
     */
    const PRIORITY_EVENT_COLLECTOR = PHP_INT_MAX;

    /**
     * FirePHP listener priority.
     *
     * @var int
     */
    const PRIORITY_FIREPHP = 500;

    /**
     * Flush listener priority.
     * Note: The Priority must be lower than PRIORITY_PROFILER!
     *
     * @var int
     */
    const PRIORITY_FLUSH = -99999;

    /**
     * Profiler listener priority.
     *
     * @var int
     */
    const PRIORITY_PROFILER = -100000;

    /**
     * Storage listener priority.
     *
     * @var int
     */
    const PRIORITY_STORAGE = 100;

    /**
     * Toolbar listener priority.
     *
     * @var int
     */
    const PRIORITY_TOOBAR = 500;

    /**
     * @var boolean
     */
    protected $strict;

    /**
     * @var ProfilerEvent
     */
    protected $event;

    /**
     * @var ReportInterface
     */
    protected $report;

    /**
     * @var PriorityQueue
     */
    protected $collectors;

    /**
     * @var EventManagerInterface
     */
    protected $eventManager;


    /**
     * Constructor.
     *
     * @param ProfilerEvent   $event
     * @param ReportInterface $report
     */
    public function __construct(ProfilerEvent $event, ReportInterface $report)
    {
        $this->event  = $event;
        $this->report = $report;

        $this->event->setTarget($this);
        $this->event->setProfiler($this)
                    ->setReport($report);
    }

    /**
     * Set the error mode.
     *
     * @param  boolean $mode
     * @return self
     */
    public function setErrorMode($mode)
    {
        $this->strict = $mode;

        return $this;
    }

    /**
     * Set the event manager instance
     *
     * @param  EventManagerInterface $eventManager
     * @return self
     */
    public function setEventManager(EventManagerInterface $eventManager)
    {
        $eventManager->addIdentifiers(array(__CLASS__, get_called_class(), 'profiler'));
        $this->eventManager = $eventManager;

        return $this;
    }

    /**
     * Get the event manager instance
     *
     * @return EventManagerInterface
     */
    public function getEventManager()
    {
        return $this->eventManager;
    }

    /**
     * Adds a collector.
     *
     * @param  Collector\CollectorInterface $collector
     * @return self
     */
    public function addCollector($collector)
    {
        if (!isset($this->collectors)) {
            $this->collectors = new PriorityQueue();
        }

        if ($collector instanceof Collector\CollectorInterface) {
            $this->collectors->insert($collector, $collector->getPriority());
        } else {
            $error = sprintf('%s must implement CollectorInterface.', get_class($collector));
            if ($this->strict === true) {
                throw new Exception\CollectorException($error);
            } else {
                $report->addError($error);
            }
        }

        return $this;
    }

    /**
     * Runs all collectors.
     *
     * @triggers ProfilerEvent::EVENT_COLLECTED
     * @param    MvcEvent $mvcEvent
     * @return   self
     */
    public function collect(MvcEvent $mvcEvent)
    {
        $this->report->setToken(uniqid('zdt'))
                     ->setUri($mvcEvent->getRequest()->getUriString())
                     ->setMethod($mvcEvent->getRequest()->getMethod())
                     ->setTime(new \DateTime('now', new \DateTimeZone('UTC')))
                     ->setIp($mvcEvent->getRequest()->getServer()->get('REMOTE_ADDR'));

        if (isset($this->collectors)) {
            foreach ($this->collectors as $collector) {
                $collector->collect($mvcEvent);

                $this->report->addCollector(unserialize(serialize($collector)));
            }

            $this->eventManager->trigger(ProfilerEvent::EVENT_COLLECTED, $this->event);
        } else {
            $error = 'There is nothing to collect.';
            if ($this->strict === true) {
                throw new Exception\ProfilerException($error);
            } else {
                $report->addError($error);
            }
        }

        return $this;
    }
}