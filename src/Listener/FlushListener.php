<?php
/**
 * Zend Developer Tools for Zend Framework (http://framework.zend.com/)
 *
 * @link       http://github.com/zendframework/ZendDeveloperTools for the canonical source repository
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendDeveloperTools\Listener;

use Zend\Mvc\MvcEvent;
use ZendDeveloperTools\Profiler;
use Zend\Stdlib\ResponseInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\EventManager\ListenerAggregateTrait;

/**
 * Flush Listener
 *
 * Listens to the MvcEvent::EVENT_FINISH event with a low priority and flushes the page.
 *
 */
class FlushListener implements ListenerAggregateInterface
{
    use ListenerAggregateTrait;

    /**
     * {@inheritdoc}
     */
    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $this->listeners[] = $events->attach(
            MvcEvent::EVENT_FINISH,
            [$this, 'onFinish'],
            Profiler::PRIORITY_FLUSH
        );
    }

    /**
     * MvcEvent::EVENT_FINISH event callback
     *
     * @param MvcEvent $event
     */
    public function onFinish(MvcEvent $event)
    {
        $response = $event->getResponse();
        if (! $response instanceof ResponseInterface) {
            return;
        }

        if (is_callable([$response, 'send'])) {
            return $response->send();
        }
    }
}
