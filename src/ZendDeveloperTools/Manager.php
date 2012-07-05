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
 * @subpackage Manager
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendDeveloperTools;

use Zend\Mvc\MvcEvent;
use Zend\EventManager\EventManagerInterface as EventManager;
use Zend\ServiceManager\ServiceLocatorInterface as ServiceLocator;

/**
 * @category   Zend
 * @package    ZendDeveloperTools
 * @subpackage Manager
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Manager
{
    /**
     * @var Options
     */
    protected $options;

    /**
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator;

    /**
     * @var EventManagerInterface
     */
    protected $eventManager;

    /**
     * @var \Zend\ServiceManager\SharedEventManagerInterface
     */
    protected $sharedEventManager;

    /**
     * Constructor.
     *v
     * @param ServiceLocator $serviceLocator
     * @param ServiceLocatorInterface $serviceLocator
     * @param Options                 $options
     */
    public function __construct(ServiceLocator $serviceLocator, EventManager $eventManager, Options $options)
    {
        $this->options            = $options;
        $this->eventManager       = $eventManager;
        $this->serviceLocator     = $serviceLocator;
        $this->sharedEventManager = $eventManager->getSharedManager();
    }

    /**
     * Registers listeners if enabled.
     *
     * @return self
     */
    public function register()
    {
        if ($this->options->isEnabled()) {
            if ($this->options->isStrict()) {
                $report = $this->serviceLocator->get('ZDT_Report');

                if ($report->hasErrors()) {
                    throw new Exception\InvalidOptionException(implode(' ', $report->getErrors()));
                }
            }

            $this->eventManager->attachAggregate($this->serviceLocator->get('ZDT_ProfileListener'));
            $this->registerToolbar();
        }

        return $this;
    }

    /**
     * Registers toolbar listeners if enabled.
     *
     * @return self
     */
    public function registerToolbar()
    {
        if ($this->options->isToolbarEnabled()) {
            $this->sharedEventManager->attach('profiler', $this->serviceLocator->get('ZDT_ToolbarListener'), 2500);

            // todo: check if detailed profiling is énabled.
            // todo: test if there is a better way to track event time.
            $timeCollector   = $this->serviceLocator->get('ZDT_TimeCollectorListener');
            $memoryCollector = $this->serviceLocator->get('ZDT_MemoryCollectorListener');
            $timeCollector->onEvent(MvcEvent::EVENT_BOOTSTRAP);
            $memoryCollector->onEvent(MvcEvent::EVENT_BOOTSTRAP);

            $this->eventManager->attachAggregate($timeCollector);
            $this->eventManager->attachAggregate($memoryCollector);
        }

        return $this;
    }
}