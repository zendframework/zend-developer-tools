<?php
/**
 * Zend Developer Tools for Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendDeveloperTools for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   ZendDeveloperTools
 */

namespace ZendDeveloperTools;

use Zend\EventManager\EventInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface as Config;
use Zend\ModuleManager\Feature\ServiceProviderInterface as Service;
use Zend\ModuleManager\Feature\BootstrapListenerInterface as BootstrapListener;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface as Autoloader;
use Zend\ModuleManager\Feature\ViewHelperProviderInterface as ViewHelper;
use BjyProfiler\Db\Adapter\ProfilingAdapter;

/**
 * @category   Zend
 * @package    ZendDeveloperTools
 */
class Module implements Config, Service, Autoloader, BootstrapListener, ViewHelper
{
    /**
     * Zend\Mvc\MvcEvent::EVENT_BOOTSTRAP event callback
     *
     * @param Event $event
     */
    public function onBootstrap(EventInterface $event)
    {
        $app = $event->getApplication();
        $em  = $app->getEventManager();
        $sem = $em->getSharedManager();
        $sm  = $app->getServiceManager();

        $options = $sm->get('ZendDeveloperTools\Config');

        if (!$options->isEnabled()) {
            return;
        }

        $report = $sm->get('ZendDeveloperTools\Report');

        if ($options->canFlushEarly()) {
            $em->attachAggregate($sm->get('ZendDeveloperTools\FlushListener'));
        }

        if ($options->isStrict() && $report->hasErrors()) {
            throw new Exception\InvalidOptionException(implode(' ', $report->getErrors()));
        }

        $em->attachAggregate($sm->get('ZendDeveloperTools\ProfilerListener'));

        if ($options->isToolbarEnabled()) {
            $sem->attach('profiler', $sm->get('ZendDeveloperTools\ToolbarListener'), null);
        }

        if ($options->isStrict() && $report->hasErrors()) {
            throw new Exception\ProfilerException(implode(' ', $report->getErrors()));
        }
    }

    /**
     * @inheritdoc
     */
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    /**
     * @inheritdoc
     */
    public function getConfig($env = null)
    {
        return include __DIR__ . '/config/module.config.php';
    }

   public function getViewHelperConfig()
    {
        return array(
            'invokables' => array(
                'ZDT_Time'        => 'ZendDeveloperTools\View\Helper\Time',
                'ZDT_Memory'      => 'ZendDeveloperTools\View\Helper\Memory',
                'ZDT_DetailArray' => 'ZendDeveloperTools\View\Helper\DetailArray',
            ),
        );
    }

    /**
     * @inheritdoc
     */
    public function getServiceConfig()
    {
        return array(
            'aliases' => array(
                'ZendDeveloperTools\ReportInterface' => 'ZendDeveloperTools\Report',
            ),
            'invokables' => array(
                'ZendDeveloperTools\Report'             => 'ZendDeveloperTools\Report',
                'ZendDeveloperTools\EventCollector'     => 'ZendDeveloperTools\Collector\EventCollector',
                'ZendDeveloperTools\ExceptionCollector' => 'ZendDeveloperTools\Collector\ExceptionCollector',
                'ZendDeveloperTools\RouteCollector'     => 'ZendDeveloperTools\Collector\RouteCollector',
                'ZendDeveloperTools\RequestCollector'   => 'ZendDeveloperTools\Collector\RequestCollector',
                'ZendDeveloperTools\MailCollector'      => 'ZendDeveloperTools\Collector\MailCollector',
                'ZendDeveloperTools\MemoryCollector'    => 'ZendDeveloperTools\Collector\MemoryCollector',
                'ZendDeveloperTools\TimeCollector'      => 'ZendDeveloperTools\Collector\TimeCollector',
                'ZendDeveloperTools\FlushListener'      => 'ZendDeveloperTools\Listener\FlushListener',
            ),
            'factories' => array(
                'Profiler' => function($sm) {
                    $a = new Profiler($sm->get('ZendDeveloperTools\Report'));
                    $a->setEvent($sm->get('ZendDeveloperTools\Event'));
                    return $a;
                },
                'ZendDeveloperTools\Config' => function ($sm) {
                    $config = $sm->get('Configuration');
                    $config = isset($config['zdt']) ? $config['zdt'] : null;

                    return new Options($config, $sm->get('ZendDeveloperTools\Report'));
                },
                'ZendDeveloperTools\Event' => function($sm) {
                    $event = new ProfilerEvent();
                    $event->setReport($sm->get('ZendDeveloperTools\Report'));
                    $event->setApplication($sm->get('Application'));

                    return $event;
                },
                'ZendDeveloperTools\StorageListener' => function($sm) {
                    return new Listener\StorageListener($sm);
                },
                'ZendDeveloperTools\ToolbarListener' => function($sm) {
                    return new Listener\ToolbarListener($sm->get('ViewRenderer'), $sm->get('ZendDeveloperTools\Config'));
                },
                'ZendDeveloperTools\ProfilerListener' => function($sm) {
                    return new Listener\ProfilerListener($sm, $sm->get('ZendDeveloperTools\Config'));
                },
                'ZendDeveloperTools\DbCollector' => function($sm) {
                    $p  = false;
                    $db = new Collector\DbCollector();

                    if ($sm->has('Zend\Db\Adapter\Adapter')) {
                        $adapter = $sm->get('Zend\Db\Adapter\Adapter');
                        if ($adapter instanceof ProfilingAdapter) {
                            $p = true;
                            $db->setProfiler($adapter->getProfiler());
                        }
                    } elseif (!$p && $sm->has('Zend\Db\Adapter\ProfilingAdapter')) {
                        $adapter = $sm->get('Zend\Db\Adapter\ProfilingAdapter');
                        if ($adapter instanceof ProfilingAdapter) {
                            $db->setProfiler($adapter->getProfiler());
                        }
                    }

                    return $db;
                },
            ),
        );
    }
}
