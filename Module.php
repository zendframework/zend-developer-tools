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
 * @subpackage Module
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendDeveloperTools;

use Zend\EventManager\Event;
use Zend\ModuleManager\Feature\ConfigProviderInterface as ConfigProvider;
use Zend\ModuleManager\Feature\ServiceProviderInterface as ServiceProvider;
use Zend\ModuleManager\Feature\BootstrapListenerInterface as BootstrapListener;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface as AutoloaderProvider;

/**
 * @category   Zend
 * @package    ZendDeveloperTools
 * @subpackage Module
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Module implements ConfigProvider, ServiceProvider, AutoloaderProvider, BootstrapListener
{
    /**
     * Zend\Mvc\MvcEvent::EVENT_BOOTSTRAP event callback
     *
     * @param Event $event
     */
    public function onBootstrap(Event $event)
    {
        $sm      = $event->getApplication()->getServiceManager();
        $manager = $sm->get('ZDT_Manager');
        $manager->register();
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

    /**
     * @inheritdoc
     */
    public function getServiceConfiguration()
    {
        return array(
            'invokables' => array(
                'ZDT_Report'              => 'ZendDeveloperTools\Report',
                'ZDT_Collector_Db'        => 'ZendDeveloperTools\Collector\DbCollector',
                'ZDT_Collector_Event'     => 'ZendDeveloperTools\Collector\EventCollector',
                'ZDT_Collector_Exception' => 'ZendDeveloperTools\Collector\ExceptionCollector',
                'ZDT_Collector_Request'   => 'ZendDeveloperTools\Collector\RequestCollector',
                'ZDT_Collector_Memory'    => 'ZendDeveloperTools\Collector\MemoryCollector',
                'ZDT_Collector_Time'      => 'ZendDeveloperTools\Collector\TimeCollector',
            ),
            'factories' => array(
                'ZDT_Options' => function ($sm) {
                    $config = $sm->get('Configuration');
                    $config = isset($config['zdt']) ? $config['zdt'] : null;

                    return new Options($config, $sm->get('ZDT_Report'));
                },
                'ZDT_Manager' => function($sm) {
                    $opt = $sm->get('ZDT_Options');
                    $em  = $sm->get('Application')->getEventManager();

                    return new Manager($sm, $em, $opt);
                },
                'ZDT_Profiler' => function($sm) {
                    return new Profiler($sm->get('ZDT_ProfilerEvent'), $sm->get('ZDT_Report'));
                },
                'ZDT_ProfilerEvent' => function($sm) {
                    $event = new ProfilerEvent();
                    $event->setApplication($sm->get('Application'));

                    return $event;
                },
                'ZDT_StorageListener' => function($sm) {
                    return new Listener\StorageListener($sm);
                },
                'ZDT_ToolbarListener' => function($sm) {
                    return new Listener\ToolbarListener($sm, $sm->get('ZDT_Options'));
                },
                'ZDT_ProfileListener' => function($sm) {
                    return new Listener\ProfilerListener($sm, $sm->get('ZDT_Options'));
                },
                'ZDT_TimeCollectorListener' => function($sm) {
                    return new Listener\EventCollectorListener($sm->get('ZDT_Collector_Time'));
                },
                'ZDT_MemoryCollectorListener' => function($sm) {
                    return new Listener\EventCollectorListener($sm->get('ZDT_Collector_Memory'));
                },
            ),
            'aliases' => array(
                'Profiler' => 'ZDT_Profiler',
            ),
        );
    }
}
