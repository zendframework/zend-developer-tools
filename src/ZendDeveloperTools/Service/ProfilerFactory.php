<?php
/**
 * Zend Developer Tools for Zend Framework (http://framework.zend.com/)
 *
 * @link       http://github.com/zendframework/ZendDeveloperTools for the canonical source repository
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd New BSD License
 * @package    ZendDeveloperTools
 * @subpackage Service
 */

namespace ZendDeveloperTools\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use ZendDeveloperTools\Exception\RuntimeException;
use ZendDeveloperTools\Profiler\Report;
use ZendDeveloperTools\Profiler\Profiler;
use ZendDeveloperTools\Profiler\ReportInterface;
use ZendDeveloperTools\Profiler\ProfilerInterface;

/**
 * @category   Zend
 * @package    ZendDeveloperTools
 * @subpackage Service
 */
class ProfilerFactory implements FactoryInterface
{
    /**
     * Creates a instance of the profiler.
     *
     * The factory respects the following service names to allow easy extending
     * of the profiler:
     *  - ZendDeveloperTools\Profiler\ReportInterface
     *    (must implement ZendDeveloperTools\Profiler\ReportInterface)
     *  - ZendDeveloperTools\Profiler\ProfilerInterface
     *    (must implement ZendDeveloperTools\Profiler\ProfilerInterface)
     *
     * After the instantiation is done, the factory adds the following services to the
     * service manager:
     *  - ZendDeveloperTools\Profiler\Profiler
     *    (by default a ZendDeveloperTools\Profiler\Profiler instance)
     *
     * @param  ServiceLocatorInterface $serviceLocator
     * @return CollectorManager
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        if ($serviceLocator->has('ZendDeveloperTools\Profiler\ProfilerInterface')) {
            $profiler = $serviceLocator->get('ZendDeveloperTools\Profiler\ProfilerInterface');

            if (!$profiler instanceof ProfilerInterface) {
                throw new RuntimeException(
                    'The profiler must implement ZendDeveloperTools\Profiler\ProfilerInterface'
                );
            }
        } else {
            $profiler = new Profiler();
        }

        if ($serviceLocator->has('ZendDeveloperTools\Profiler\ReportInterface')) {
            $report = $serviceLocator->get('ZendDeveloperTools\Profiler\ReportInterface');

            if (!$report instanceof ReportInterface) {
                throw new RuntimeException(
                    'The profiler report must implement ZendDeveloperTools\Profiler\ReportInterface'
                );
            }
        } else {
            $report = new Report();
        }

        $options = $serviceLocator->get('ZendDeveloperTools\Options\ModuleOptions');

        $profiler->setReport($report);
        $profiler->setOptions($options);

        $serviceLocator->setService('ZendDeveloperTools\Profiler\Profiler', $profiler);

        return $profiler;
    }
}