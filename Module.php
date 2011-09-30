<?php

namespace ZendDeveloperTools;

use Zend\Module\Manager,
    Zend\Config\Config,
    Zend\EventManager\StaticEventManager;

class Module
{
    public function init(Manager $moduleManager)
    {
        StaticEventManager::getInstance()->attach('Zend\Mvc\Application', 'finish', function($e) {
            $append = 'ZendDeveloperTools Module Loaded';
            return $e->getResponse()->setContent($e->getResponse()->getBody() . $append);
        });
        $this->initAutoloader();
    }

    protected function initAutoloader()
    {
        require __DIR__ . '/autoload_register.php';
    }

    public static function getConfig($env = null)
    {
        return new Config(include __DIR__ . '/configs/module.config.php');
    }
}
