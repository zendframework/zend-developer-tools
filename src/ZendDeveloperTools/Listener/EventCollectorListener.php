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

use Zend\Mvc\MvcEvent;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use ZendDeveloperTools\Profiler;
use ZendDeveloperTools\Collector\EventCollectorInterface;

/**
 * Event Collector Listener
 *
 * Listens to every MvcEvent event.
 *
 * @category   Zend
 * @package    ZendDeveloperTools
 * @subpackage Listener
 */
class EventCollectorListener implements ListenerAggregateInterface, DynamicIdentifierInterface
{
    /**
     * @var string
     */
    protected $identifier;

    /**
     * @var EventCollectorInterface
     */
    protected $collector;

    /**
     * @var array
     */
    protected $listeners = array();

    /**
     * Constructor.
     *
     * @param EventCollectorInterface $collector
     */
    public function __construct(EventCollectorInterface $collector)
    {
        $this->collector = $collector;
    }

    /**
     * @inheritdoc
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach('*', array($this, 'onEvent'), Profiler::PRIORITY_EVENT_COLLECTOR);

        if ($this->identifier === 'application') {
            $this->onEvent(MvcEvent::EVENT_BOOTSTRAP);
        }
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
     * @inheritdoc
     */
    public function setIdentifier($id)
    {
        $this->identifier = $id;

        return $this;
    }

    /**
     * MvcEvent::EVENT_BOOTSTRAP,
     * MvcEvent::EVENT_DISPATCH,
     * MvcEvent::EVENT_DISPATCH_ERROR,
     * MvcEvent::EVENT_ROUTE,
     * MvcEvent::EVENT_RENDER,
     * MvcEvent::EVENT_FINISH event callback
     *
     * @param MvcEvent|string $event
     */
    public function onEvent($event)
    {
        $this->collector->collectEvent($this->identifier, (is_string($event)) ? $event : $event->getName());
    }
}