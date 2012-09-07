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
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\ServiceProviderInterface;
use Zend\ModuleManager\Feature\BootstrapListenerInterface;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ViewHelperProviderInterface;
use ZendDeveloperTools\Profiler\ProfilerInterface;
use ZendDeveloperTools\Exception\RuntimeException;

/**
 * @category Zend
 * @package  ZendDeveloperTools
 */
class Module implements
    ConfigProviderInterface,
    ServiceProviderInterface,
    AutoloaderProviderInterface,
    BootstrapListenerInterface,
    ViewHelperProviderInterface
{
    /**
     * @inheritdoc
     */
    public function onBootstrap(EventInterface $event)
    {
        $application    = $event->getApplication();
        $serviceManager = $application->getServiceManager();
        $profiler       = $serviceManager->get('ZendDeveloperTools\Profiler\Profiler');

        $profiler->bootstrap($event);
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
        return array();
    }

    /**
     * @inheritdoc
     */
    public function getServiceConfig()
    {
        return array(
            'aliases' => array(
            ),
            'invokables' => array(
            ),
            'factories' => array(
                'ZendDeveloperTools\Profiler\Profiler' => 'ZendDeveloperTools\Service\ProfilerFactory',
            ),
        );
    }
}
