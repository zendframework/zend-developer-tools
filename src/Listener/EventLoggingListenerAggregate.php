<?php
/**
 * Zend Developer Tools for Zend Framework (http://framework.zend.com/)
 *
 * @link http://github.com/zendframework/ZendDeveloperTools for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendDeveloperTools\Listener;

use Zend\EventManager\Event;
use Zend\EventManager\SharedEventManagerInterface;
use ZendDeveloperTools\Collector\CollectorInterface;
use ZendDeveloperTools\Collector\EventCollectorInterface;
use ZendDeveloperTools\Profiler;

/**
 * Listens to defined events to allow event-level collection of statistics.
 *
 * @author Mark Garrett <mark@moderndeveloperllc.com>
 * @since 0.0.3
 */
class EventLoggingListenerAggregate
{
    /**
     * @var EventCollectorInterface[]
     */
    protected $collectors;

    /**
     * @var The event identifiers to collect
     */
    protected $identifiers;

    /**
     * Constructor.
     *
     * @param EventCollectorInterface[] $collectors
     * @param string[]                                                $identifiers
     */
    public function __construct(array $collectors, array $identifiers)
    {
        $this->collectors = array_map(
            function (CollectorInterface $collector) {
                return $collector;
            },
            $collectors
        );
        $this->identifiers = array_values(array_map(
            function ($identifier) {
                return (string) $identifier;
            },
            $identifiers
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function attachShared(SharedEventManagerInterface $events)
    {
        foreach ($this->identifiers as $id) {
            $events->attach($id, '*', [$this, 'onCollectEvent'], Profiler::PRIORITY_EVENT_COLLECTOR);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function detachShared(SharedEventManagerInterface $events)
    {
        // can't be detached
    }

    /**
     * Callback to process events
     *
     * @param Event $event
     *
     * @return bool
     *
     * @throws ServiceNotFoundException
     */
    public function onCollectEvent(Event $event)
    {
        foreach ($this->collectors as $collector) {
            $collector->collectEvent('application', $event);
        }
    }
}
