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
 * @subpackage EventListener
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendDeveloperTools\Listener;

use Zend\Mvc\MvcEvent;
use ZendDeveloperTools\Profiler;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Profiler Listener
 *
 * Listens to the MvcEvent::EVENT_FINISH event and starts collecting data.
 *
 * @category   Zend
 * @package    ZendDeveloperTools
 * @subpackage EventListener
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ProfilerListener implements ListenerAggregateInterface
{
    /**
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator;

    /**
     * @var array
     */
    protected $listeners = array();

    /**
     * Constructor.
     *
     * @param ServiceLocatorInterface $serviceLocator
     */
    public function __construct(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    /**
     * @inheritdoc
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_FINISH, array($this, 'onFinish'));
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
        // todo: needs to be fixed, should úse serivice locator/plugin manager whatever.
        //       question of the day: does the service locator allow tagging?
        $temp_collector_dict = array(
            'zdt_data_collector_db'        => new \ZendDeveloperTools\Collector\DbCollector(),
            'zdt_data_collector_event'     => new \ZendDeveloperTools\Collector\EventCollector(),
            'zdt_data_collector_exception' => new \ZendDeveloperTools\Collector\ExceptionCollector(),
            'zdt_data_collector_request'   => new \ZendDeveloperTools\Collector\RequestCollector(),
            'zdt_data_collector_memory'    => new \ZendDeveloperTools\Collector\MemoryCollector(),
            'zdt_data_collector_time'      => new \ZendDeveloperTools\Collector\TimeCollector(),
        );

        $profiler = $this->serviceLocator->get('Profiler');
        $profiler->setCollectors($temp_collector_dict)
                 ->collect($event);
    }
}