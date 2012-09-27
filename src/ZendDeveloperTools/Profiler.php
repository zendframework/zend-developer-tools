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

use Zend\Mvc\MvcEvent;
use Zend\Stdlib\PriorityQueue;
use Zend\EventManager\EventInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\EventManagerAwareInterface;

/**
 * @category   Zend
 * @package    ZendDeveloperTools
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
    const PRIORITY_FLUSH = -9400;

    /**
     * Profiler listener priority.
     *
     * @var int
     */
    const PRIORITY_PROFILER = -9500;

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
    const PRIORITY_TOOLBAR = 500;

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
     * @param ReportInterface $report
     */
    public function __construct(ReportInterface $report)
    {
        $this->report = $report;
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
     * Set the profiler event object.
     *
     * @param  EventInterface $event
     * @return self
     */
    public function setEvent(EventInterface $event)
    {
        $this->event = $event;

        return $this;
    }

    /**
     * Returns the profiler event object.
     *
     * @return self
     */
    public function getEvent()
    {
        if (!isset($this->event)) {
            $this->event = new ProfilerEvent();
            $this->event->setTarget($this);
            $this->event->setProfiler($this);
        }

        return $this->event;
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
     * @throws Exception\CollectorException
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
            }

            $this->report->addError($error);
        }

        return $this;
    }

    /**
     * Runs all collectors.
     *
     * @triggers ProfilerEvent::EVENT_COLLECTED
     * @param    MvcEvent $mvcEvent
     * @return   Profiler
     * @throws   Exception\ProfilerException
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

            $this->eventManager->trigger(ProfilerEvent::EVENT_COLLECTED, $this->getEvent());

            return $this;
        }

        if ($this->strict === true) {
            throw new Exception\ProfilerException('There is nothing to collect.');
        }

        $this->report->addError('There is nothing to collect.');

        return $this;
    }
}
