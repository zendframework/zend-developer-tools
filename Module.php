<?php

namespace ZendDeveloperTools;

use Zend\Module\Manager,
    Zend\EventManager\StaticEventManager,
    Zend\Loader\AutoloaderFactory;

class Module
{
    protected $viewListener;
    protected $view;

    public function init(Manager $moduleManager)
    {
        $this->initAutoloader();
        Service\DeveloperTools::$startTime = microtime(true);
        $events = StaticEventManager::getInstance();
        $events->attach('bootstrap', 'bootstrap', array($this, 'initializeView'), 1000);
        $events->attach('Zend\Mvc\Application', 'finish', function($e) {
            Service\DeveloperTools::$stopTime = microtime(true);
            $devToolService = new Service\DeveloperTools;
            return $devToolService->appendResponse($e);
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

    public function getConfig($env = null)
    {
        return include __DIR__ . '/configs/module.config.php';
    }

    public function initializeView($e)
    {
        $app          = $e->getParam('application');
        $locator      = $app->getLocator();
        $config       = $e->getParam('modules')->getMergedConfig();
        $view         = $this->getView($app);
        $viewListener = $this->getViewListener($view, $config);
        $events       = StaticEventManager::getInstance();
        $viewListener->registerStaticListeners($events, $locator);
    }

    protected function getViewListener($view, $config)
    {
        if ($this->viewListener instanceof View\Listener) {
            return $this->viewListener;
        }
        $viewListener       = new View\Listener($view, $config->zend_developer_tools->layout);
        $this->viewListener = $viewListener;
        return $viewListener;
    }

    protected function getView($app)
    {
        if ($this->view === null) {
            $this->view = $app->getLocator()->get('view');
        }
        return $this->view;
    }
}
