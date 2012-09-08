<?php
/**
 * Zend Developer Tools for Zend Framework (http://framework.zend.com/)
 *
 * @link       http://github.com/zendframework/ZendDeveloperTools for the canonical source repository
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd New BSD License
 * @package    ZendDeveloperTools
 * @subpackage Profiler
 */

namespace ZendDeveloperTools\Profiler;

use Zend\Mvc\MvcEvent;
use Zend\Console\Console;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\EventManagerAwareInterface;
use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\ServiceManagerAwareInterface;
use ZendDeveloperTools\Options\ModuleOptions;

/**
 * @category   Zend
 * @package    ZendDeveloperTools
 * @subpackage Profiler
 */
class Profiler implements ProfilerInterface, EventManagerAwareInterface, ServiceManagerAwareInterface
{
    /**
     * @var boolean
     */
    protected $disabled = false;

    /**
     * @var boolean
     */
    protected $profiled = false;

    /**
     * @var ProfilerEvent
     */
    protected $event;

    /**
     * @var ReportInterface
     */
    protected $report;

    /**
     * @var ModuleOptions
     */
    protected $options;

    /**
     * @var EventManagerInterface
     */
    protected $eventManager;

    /**
     * @var ServiceManager
     */
    protected $serviceManager;

    /**
     * @inheritdoc
     */
    public function setEventManager(EventManagerInterface $eventManager)
    {
        $eventManager->addIdentifiers(array(
            __CLASS__,
            get_called_class(),
        ));

        $this->eventManager = $eventManager;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getEventManager()
    {
        return $this->eventManager;
    }

    /**
     * @inheritdoc
     */
    public function getServiceManager()
    {
        return $this->serviceManager;
    }

    /**
     * @inheritdoc
     */
    public function setServiceManager(ServiceManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getEvent()
    {
        return $event;
    }

    /**
     * @inheritdoc
     */
    public function setOptions(ModuleOptions $options)
    {
        $this->options = $options;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @inheritdoc
     */
    public function setReport(ReportInterface $report)
    {
        $this->report = $report;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getReport()
    {
        return $this->report;
    }

    /**
     * @inheritdoc
     */
    public function disable()
    {
        $this->disbaled = true;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function boostrap(MvcEvent $mvcEvent)
    {
        if (Console::isConsole()) {
            return $this;
        }

        $profilerOptions = $this->getOptions();
        if (!$profilerOptions->isEnabled()) {
            return $this;
        }

        $event = $this->event = new ProfilerEvent();
        $event->setTarget($this);
        $event->setProfiler($this);
        $event->setReport($this->getReport());
        $event->setApplication($mvcEvent->getApplication());

        $this->attachDefaultListeners($mvcEvent);

        $eventManager->trigger(ProfilerEvent::EVENT_BOOTSTRAP, $event);

        return $this;
    }

    /**
     * Run the profiler.
     *
     * @triggers collect(ProfilerEvent)
     * @triggers finish(ProfilerEvent)
     * @return   self
     */
    public function run()
    {
        if ($this->profiled || $this->disabled) {
            return $this;
        }

        $event = $this->getEvent();

        $eventManager->trigger(ProfilerEvent::EVENT_COLLECT, $event);

        if ($this->disabled) {
            return $this;
        }

        $eventManager->trigger(ProfilerEvent::EVENT_FINISH, $event);

        $this->profiled = true;

        return $this;
    }

    /**
     * Attaches the default listeners for all sub-components.
     *
     * @param  MvcEvent $event
     * @return self
     */
    protected function attachDefaultListeners(MvcEvent $event)
    {
        $options     = $this->getOptions();
        $events      = $this->getEventManager();
        $application = $event->getApplication();
        $appEvents   = $application->getEventManager();

        $appEvents->attach(MvcEvent::EVENT_FINISH, array($this, 'run'), -9900);

        if ($options->hasMatcher()) {
            $events->attachAggregate(new Listener\MatcherListener($options->getMatchingMode()));
        }

        if ($options->hasResponseHook()) {
            $events->attachAggregate(new Listener\WebListener());
        }

        $events->attachAggregate(new Listener\GuardListener());
        $events->attachAggregate(new Listener\StorageListener());
        $events->attachAggregate(new Listener\CollectorListener());

        return $this;
    }
}