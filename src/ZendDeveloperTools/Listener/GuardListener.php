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

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use ZendDeveloperTools\Profiler\ProfilerEvent;

/**
 * @category   Zend
 * @package    ZendDeveloperTools
 * @subpackage Listener
 */
class GuardListener implements ListenerAggregateInterface
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
        $this->listeners[] = $events->attach(ProfilerEvent::EVENT_COLLECT, array($this, 'onCollect'), 10000);
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
     * ProfilerEvent::EVENT_COLLECT callback
     *
     * @param  ProfilerEvent $event
     * @return boolean|null
     */
    public function onCollect(ProfilerEvent $event)
    {
        $profiler    = $event->getProfiler();
        $options     = $profiler->getOptions();
        $application = $event->getApplication();
        $response    = $application->getResponse();

        if ($options->isBrowserOutputEnabled()) {
            $profiler = $event->getProfiler();
            $events   = $profiler->getEventManager();

            $events->trigger(ProfilerEvent::EVENT_ACCESS, $event);

            if (!$event->isAccessible()) {
                return $this->flush($response);
            }
        } else {
            return $this->flush($response);
        }
    }

    /**
     * Tries to flush the repsonse.
     *
     * @param  mixed $response
     * @return boolean
     */
    protected function flush($response)
    {
        if (!$response instanceof ResponseInterface) {
            return false;
        }

        if (is_callable(array($response, 'send'))) {
            return $response->send();
        }
    }
}