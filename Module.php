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
        $eventManager       = $event->getApplication()->getEventManager();
        $sharedEventManager = $eventManager->getSharedManager();
        $serviceManager     = $event->getApplication()->getServiceManager();

        $eventManager->attachAggregate($serviceManager->get('ProfileListener'));

        // todo: check if the toolbar is enabled.
        $sharedEventManager->attach('profiler', $serviceManager->get('ToolbarListener'), 2500);

        // todo: save stuff in db/file.
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
                'ProfilerReport' => 'ZendDeveloperTools\Report',
            ),
            'factories' => array(
                'Profiler' => function($sm) {
                    return new Profiler($sm->get('ProfilerEvent'), $sm->get('ProfilerReport'));
                },
                'ProfilerEvent' => function($sm) {
                    $event = new ProfilerEvent();
                    $event->setApplication($sm->get('Application'));

                    return $event;
                },
                'StorageListener' => function($sm) {
                    return new Listener\StorageListener($sm);
                },
                'ToolbarListener' => function($sm) {
                    return new Listener\ToolbarListener($sm);
                },
                'ProfileListener' => function($sm) {
                    return new Listener\ProfilerListener($sm);
                },
            ),
        );
    }
}
