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
 * @subpackage Listener
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendDeveloperTools\Listener;

use Zend\Mvc\MvcEvent;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use ZendDeveloperTools\Collector\EventCollectorInterface;

/**
 * Event Collector Listener
 *
 * Listens to every MvcEvent event.
 *
 * @category   Zend
 * @package    ZendDeveloperTools
 * @subpackage Listener
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class EventCollectorListener implements ListenerAggregateInterface
{
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
        $this->listeners[] = $events->attach('*', array($this, 'onEvent'), PHP_INT_MAX);
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
        $this->collector->collectEvent('application', (is_string($event)) ? $event : $event->getName());
    }
}