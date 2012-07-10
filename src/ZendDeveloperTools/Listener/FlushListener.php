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
use ZendDeveloperTools\Profiler;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;

/**
 * Flush Listener
 *
 * Listens to the MvcEvent::EVENT_FINISH event with a low priority and flushes the page.
 *
 * @category   Zend
 * @package    ZendDeveloperTools
 * @subpackage Listener
 */
class FlushListener implements ListenerAggregateInterface
{
    /**
     * @var array
     */
    protected $listeners = array();

    /**
     * @inheritdoc
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(
            MvcEvent::EVENT_FINISH,
            array($this, 'onFinish'),
            Profiler::PRIORITY_FLUSH
        );
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
     * MvcEvent::EVENT_FINISH event callback
     *
     * @param MvcEvent $event
     */
    public function onFinish(MvcEvent $event)
    {
        $event->getResponse()->send();
    }
}