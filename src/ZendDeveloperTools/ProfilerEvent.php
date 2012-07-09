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
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendDeveloperTools;

use Zend\EventManager\Event;
use Zend\Mvc\ApplicationInterface;

/**
 * @category   Zend
 * @package    ZendDeveloperTools
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ProfilerEvent extends Event
{
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
     * @return string
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
     * @param  Profiler $report
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