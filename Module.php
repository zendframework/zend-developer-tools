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

use Zend\Module\Manager,
    Zend\Module\Consumer\AutoloaderProvider,
    Zend\EventManager\StaticEventManager;

class Module implements AutoloaderProvider
{
    protected $viewListener;
    protected $view;

    public function init(Manager $moduleManager)
    {
        Service\DeveloperTools::$startTime = microtime(true);
        $events = StaticEventManager::getInstance();
        $events->attach('bootstrap', 'bootstrap', array($this, 'initializeView'), 1000);
        $events->attach('Zend\Mvc\Application', 'finish', function($e) {
            Service\DeveloperTools::$stopTime = microtime(true);
            $devToolService = new Service\DeveloperTools;
            return $devToolService->appendResponse($e);
        });
    }

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

    public function getConfig($env = null)
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function initializeView($e)
    {
        $app          = $e->getParam('application');
        $locator      = $app->getLocator();
        $config       = $e->getParam('config');
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
