<?php
/**
 * Zend Developer Tools for Zend Framework (http://framework.zend.com/)
 *
 * @link       http://github.com/zendframework/ZendDeveloperTools for the canonical source repository
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd New BSD License
 * @package    ZendDeveloperTools
 * @subpackage Listener
 */

namespace ZendDeveloperTools\Listener;

use Zend\Stdlib\PriorityQueue;
use Zend\ServiceManager\ServiceManager;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use ZendDeveloperTools\Profiler\ProfilerEvent;
use ZendDeveloperTools\Exception\CollectorException;
use ZendDeveloperTools\Collector\CollectorInterface;
use ZendDeveloperTools\Collector\Feature\CollectListenerInterface;
use ZendDeveloperTools\Collector\Feature\PriorityProviderInterface;
use ZendDeveloperTools\Collector\Feature\InjectableMvcEventInterface;

/**
 * @category   Zend
 * @package    ZendDeveloperTools
 * @subpackage Listener
 */
class CollectorListener implements ListenerAggregateInterface
{
    /**
     * @var \Zend\Stdlib\CallbackHandler[]
     */
    protected $listeners = array();

    /**
     * @inheritdoc
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(ProfilerEvent::EVENT_COLLECT, array($this, 'onCollect'));
    }

    /**
     * @inheritdoc
     */
    public function detach(EventManagerInterface $events)
    {
        foreach ($this->listeners as $index => $listener) {
            if ($events->detach($listener)) {
                unset($this->listeners[$index]);
            }
        }
    }

    /**
     * Runs every registered collector and populates the profiler report.
     *
     * @param  ProfilerEvent $event
     * @return boolean|null
     */
    public function onCollect(ProfilerEvent $event)
    {
        $profiler    = $event->getProfiler();
        $application = $event->getApplication();
        $report      = $profiler->getReport();
        $options     = $profiler->getOptions();
        $mvcEvent    = $application->getEvent();
        $registered  = array_diff($options->getCollectors(), $options->getUnregisteredCollectors());
        $collectors  = $this->createCollectorQueue($registered, $profiler->getServiceManager());

        if ($collectors->isEmpty()) {
            throw new CollectorException('No collectors registered.');
        }

        $report->setToken(uniqid('ZDT'))
               ->setUri($mvcEvent->getRequest()->getUriString())
               ->setMethod($mvcEvent->getRequest()->getMethod())
               ->setTime(new \DateTime('now', new \DateTimeZone('UTC')))
               ->setIp($mvcEvent->getRequest()->getServer()->get('REMOTE_ADDR'));

        foreach ($collectors as $collector) {
            if ($collector instanceof InjectableMvcEventInterface) {
                $collector->setMvcEvent($mvcEvent);
            }

            if ($collector instanceof CollectListenerInterface) {
                $collector->onCollect();
            }

            $report->addCollector(unserialize(serialize($collector)));
        }
    }

    /**
     * Creates a priority queue for the registered collectors.
     *
     * @param  array          $collectors
     * @param  ServiceManager $serviceManager
     * @return PriorityQueue
     */
    protected function createCollectorQueue(array $collectors, ServiceManager $serviceManager)
    {
        $queue = new PriorityQueue();

        foreach ($collectors as $serviceName) {
            if ($serviceManager->has($serviceName)) {
                $collector = $serviceManager->get($serviceName);

                if (!$collector instanceof CollectorInterface) {
                    throw new CollectorException(sprintf(
                        'A collector (%s) must implement ZendDeveloperTools\Collector\CollectorInterface.',
                        $serviceName
                    ));
                }

                if (is_string($collector->getName())) {
                    throw new CollectorException(sprintf(
                        '%s::getName must return a string, %s given.',
                        get_class($collector),
                        gettype($priority)
                    ));
                }

                if ($collector instanceof PriorityProviderInterface) {
                    $priority = $collector->getPriority();

                    if (!is_int($priority)) {
                        throw new CollectorException(sprintf(
                            '%s::getPriority must return an integer, %s given.',
                            get_class($collector),
                            gettype($priority)
                        ));
                    }
                } else {
                    $priority = 0;
                }

                $queue->insert($collector, $priority);
            } else {
                throw new CollectorException(sprintf('Unable to fetch or create an instance for %s.', $serviceName));
            }
        }

        return $queue;
    }
}