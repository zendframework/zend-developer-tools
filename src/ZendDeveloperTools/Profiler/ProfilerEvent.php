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

use Zend\EventManager\Event;
use Zend\Mvc\ApplicationInterface;

/**
 * @category   Zend
 * @package    ZendDeveloperTools
 * @subpackage Profiler
 */
class ProfilerEvent extends Event
{
    /**
     * The EVENT_BOOTSTRAP occurs on bootstrap if the profiler is enabled.
     *
     * @var string
     */
    const EVENT_BOOTSTRAP = 'bootstrap';

    /**
     * The EVENT_COLLECT occurs after the finish(MvcEvent) event.
     *
     * @var string
     */
    const EVENT_COLLECT = 'collect';

    /**
     * The EVENT_ACCESS occurs if anything web related was requested, like
     * rendering the toolbar or requesting a report thru the profile viewer.
     * It is meant to provide a way, to controll the access to these resources.
     *
     * @var string
     */
    const EVENT_ACCESS = 'access';

    /**
     * The EVENT_FINISH occurs after all data was gathered.
     *
     * @var string
     */
    const EVENT_FINISH = 'finish';

    /**
     * @var boolean
     */
    protected $access;

    /**
     * @var Profiler
     */
    protected $profiler;

    /**
     * @var ReportInterface
     */
    protected $report;

    /**
     * @var ApplicationInterface
     */
    protected $application;

    /**
     * Is the profile accessible thru web requests?
     *
     * @return boolean
     */
    public function isAccessible()
    {
        return $this->access;
    }

    /**
     * Disables the access to any profile data for web requests.
     *
     * @return self
     */
    public function denyAccess()
    {
        $this->access = false;

        return $this;
    }

    /**
     * Set Application
     *
     * @return string
     */
    public function getApplication()
    {
        return $this->application;
    }

    /**
     * Set Application
     *
     * @param  ApplicationInterface $application
     * @return self
     */
    public function setApplication(ApplicationInterface $application)
    {
        $this->application = $application;

        return $this;
    }

    /**
     * Get profiler
     *
     * @return Profiler
     */
    public function getProfiler()
    {
        return $this->profiler;
    }

    /**
     * Set profiler
     *
     * @param  Profiler $profiler
     * @return self
     */
    public function setProfiler(Profiler $profiler)
    {
        $this->profiler = $profiler;

        return $this;
    }

    /**
     * Get report
     *
     * @return ReportInterface
     */
    public function getReport()
    {
        return $this->report;
    }

    /**
     * Set report
     *
     * @param  ReportInterface $report
     * @return self
     */
    public function setReport(ReportInterface $report)
    {
        $this->report = $report;

        return $this;
    }
}