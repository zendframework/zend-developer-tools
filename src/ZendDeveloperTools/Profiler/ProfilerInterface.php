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
use ZendDeveloperTools\Options\ModuleOptions;

/**
 * @category   Zend
 * @package    ZendDeveloperTools
 * @subpackage Profiler
 */
interface ProfilerInterface
{
    /**
     * Get the event manager instance
     *
     * @return EventManagerInterface
     */
    public function getEventManager();

    /**
     * Get the service manager instance
     *
     * @return ServiceManager
     */
    public function getServiceManager();

    /**
     * Returns the profiler event object.
     *
     * @return ProfilerEvent
     */
    public function getEvent();

    /**
     * Set the module option object.
     *
     * @param  ModuleOptions $options
     * @return self
     */
    public function setOptions(ModuleOptions $options);

    /**
     * Returns the profiler option object.
     *
     * @return ProfilerOptions
     */
    public function getOptions();

    /**
     * Set the report object.
     *
     * @param  ReportInterface $report
     * @return self
     */
    public function setReport(ReportInterface $report);

    /**
     * Returns the report object.
     *
     * @return ReportInterface
     */
    public function getReport();

    /**
     * Disables the profiler for this request.
     *
     * @return self
     */
    public function disable();

    /**
     * Bootstrap the profiler.
     *
     * @triggers bootstrap(ProfilerEvent)
     *           if the profiler is enabled and the application was not invoked
     *           from the console.
     * @param    MvcEvent $mvcEvent
     * @return   self
     */
    public function boostrap(MvcEvent $mvcEvent);
}