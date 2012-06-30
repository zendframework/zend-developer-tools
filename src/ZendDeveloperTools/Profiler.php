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
 * @subpackage Profiler
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
 * @subpackage Profiler
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Profiler implements EventManagerAwareInterface
{
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
     * Setss collectors.
     *
     * @param  array $collectors
     * @return self
     */
    public function setCollectors(array $collectors)
    {
        if (!isset($this->collectors)) {
            $this->collectors = new PriorityQueue();
        }

        foreach ($collectors as $collector) {
            if ($collector instanceof Collector\CollectorInterface) {
                $this->collectors->insert($collector, $collector->getPriority());
            } else {
                $this->report->setErrors(array(
                    'collector' => sprintf('The service named %s must implement CollectorInterface.')
                ));
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
                     ->setMethod($mvcEvent->getRequest()->getMethod())
                     ->setTime(new \DateTime('now', new \DateTimeZone('UTC')))
                     ->setIp($mvcEvent->getRequest()->server()->get('REMOTE_ADDR'));

        if (isset($this->collectors)) {
            foreach ($this->collectors as $collector) {
                $collector->collect($mvcEvent);

                $this->report->addCollector(unserialize(serialize($collector)));
            }

            $this->eventManager->trigger(ProfilerEvent::EVENT_COLLECTED, $this->event);
        } else {
            $report->setErrors(array('collect' => 'No collectors initialized.'));
        }

        return $this;
    }
}