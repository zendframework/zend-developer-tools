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
class WebListener implements ListenerAggregateInterface
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
        $this->listeners[] = $events->attach(ProfilerEvent::EVENT_FINISH, array($this, 'onFinish'));
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
     * Will, based on the options, inject the toolbar or cosole related
     * features such as FirePHP.
     *
     * @param ProfilerEvent $event
     */
    public function onFinish(ProfilerEvent $event)
    {
        if (!$event->isAccessible()) {
            return;
        }

        $application = $event->getApplication();
        $request     = $application->getRequest();

        if ($request->isXmlHttpRequest()) {
            return;
        }

        /**
         * @todo Grab the ViewModel from the Toolbar Controller instead of
         *       creating it inside the listener. The Toolbar Controller can
         *       also be used to reload the toolbar for ajax-based requests
         *       or applications.
         */
    }
}