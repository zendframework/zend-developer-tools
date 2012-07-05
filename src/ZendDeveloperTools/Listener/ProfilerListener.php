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
use ZendDeveloperTools\Options;
use ZendDeveloperTools\Profiler;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceNotFoundException;

/**
 * Profiler Listener
 *
 * Listens to the MvcEvent::EVENT_FINISH event and starts collecting data.
 *
 * @category   Zend
 * @package    ZendDeveloperTools
 * @subpackage Listener
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
     * @var Options
     */
    protected $options;

    /**
     * @var array
     */
    protected $listeners = array();

    /**
     * Constructor.
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @param Options                 $options
     */
    public function __construct(ServiceLocatorInterface $serviceLocator, Options $options)
    {
        $this->options        = $options;
        $this->serviceLocator = $serviceLocator;
    }

    /**
     * @inheritdoc
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_FINISH, array($this, 'onFinish'), -99999);
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
        $strict     = $this->options->isStrict();
        $collectors = $this->options->getCollectors();
        $report     = $this->serviceLocator->get('ZDT_Report');
        $profiler   = $this->serviceLocator->get('ZDT_Profiler');

        $profiler->setErrorMode($strict);

        foreach ($collectors as $name => $collector) {
            if ($this->serviceLocator->has($collector)) {
                $profiler->addCollector($this->serviceLocator->get($collector));
            } else {
                $error = sprintf('Unable to fetch or create an instance for %s.', $collector);
                if ($strict === true) {
                    throw new ServiceNotFoundException($error);
                } else {
                    $report->addError($error);
                }
            }
        }

        $profiler->collect($event);
    }
}