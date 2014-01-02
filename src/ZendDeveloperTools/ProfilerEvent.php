<?php
/**
 * Zend Developer Tools for Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendDeveloperTools for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendDeveloperTools;

use Zend\EventManager\Event;
use Zend\Mvc\ApplicationInterface;

class ProfilerEvent extends Event
{
    /**
     * The EVENT_PROFILER_INIT occurs on bootstrap if the profiler is enabled.
     *
     * This event allows you to grab the report.
     *
     * @var string
     */
    const EVENT_PROFILER_INIT = 'profiler_init';

    /**
     * The EVENT_COLLECTED occurs after all data was collected.
     *
     * This event allows you to grab the report.
     *
     * @var string
     */
    const EVENT_COLLECTED = 'collected';

    /**
     * Set Application
     *
     * @return ApplicationInterface|null
     */
    public function getApplication()
    {
        return $this->getParam('application');
    }

    /**
     * Set Application
     *
     * @param  ApplicationInterface $application
     * @return self
     */
    public function setApplication(ApplicationInterface $application)
    {
        $this->setParam('application', $application);

        return $this;
    }

    /**
     * Get profiler
     *
     * @return string
     */
    public function getProfiler()
    {
        return $this->getParam('profiler');
    }

    /**
     * Set profiler
     *
     * @param  Profiler $profiler
     * @return self
     */
    public function setProfiler(Profiler $profiler)
    {
        $this->setParam('profiler', $profiler);

        return $this;
    }

    /**
     * Get report
     *
     * @return ReportInterface
     */
    public function getReport()
    {
        return $this->getParam('report');
    }

    /**
     * Set report
     *
     * @param  ReportInterface $report
     * @return self
     */
    public function setReport(ReportInterface $report)
    {
        $this->setParam('report', $report);

        return $this;
    }
}
