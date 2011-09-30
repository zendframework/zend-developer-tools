<?php

namespace ZendDeveloperTools;

use Zend\Module\Manager,
    Zend\Config\Config,
    Zend\EventManager\StaticEventManager,
    Zend\Loader\AutoloaderFactory;

class Module
{
    public function init(Manager $moduleManager)
    {
        $this->initAutoloader();
        StaticEventManager::getInstance()->attach('Zend\Mvc\Application', 'finish', function($e) {
            $devToolService = new Service\DeveloperTools;
            return $devToolService->appendResponse($e->getResponse());
        });
    }

    protected function initAutoloader()
    {
        AutoloaderFactory::factory(array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        ));
    }

    public static function getConfig($env = null)
    {
        return new Config(include __DIR__ . '/configs/module.config.php');
    }
}
