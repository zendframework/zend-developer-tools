<?php

namespace ZendDeveloperTools\View;

use ArrayAccess,
    Zend\EventManager\EventCollection,
    Zend\EventManager\ListenerAggregate,
    Zend\EventManager\StaticEventCollection,
    Zend\Mvc\MvcEvent,
    Zend\View\Renderer;

class Listener implements ListenerAggregate
{
    protected $listeners = array();
    protected $staticListeners = array();
    protected $view;
    protected $layout;

    public function __construct(Renderer $renderer, $layout = 'developer-toolbar.phtml')
    {
        $this->view = $renderer;
        $this->layout = $layout;
    }

    public function detach(EventCollection $events)
    {
        foreach ($this->listeners as $key => $listener) {
            $events->detach($listener);
            unset($this->listeners[$key]);
            unset($listener);
        }
    }

    public function attach(EventCollection $events)
    {
        $this->listeners[] = $events->attach('dispatch', array($this, 'renderLayout'), -1000);
    }

    public function registerStaticListeners(StaticEventCollection $events)
    {
        $ident   = 'ZendDeveloperTools\Controller\DeveloperToolsController';
        $handler = $events->attach($ident, 'dispatch', array($this, 'renderView'), -40);
        $this->staticListeners[] = array($ident, $handler);
        $handler = $events->attach($ident, 'dispatch', array($this, 'renderLayout'), -1000);
        $this->staticListeners[] = array($ident, $handler);
    }

    public function detachStaticListeners(StaticEventCollection $events)
    {
        foreach ($this->staticListeners as $i => $info) {
            list($id, $handler) = $info;
            $events->detach($id, $handler);
            unset($this->staticListeners[$i]);
        }
    }

    public function renderView(MvcEvent $e)
    {
        $response = $e->getResponse();
        if (!$response->isSuccess()) {
            return;
        }

        $controller = 'developer-tools';
        $action     = 'index';
        $script     = $controller . '/' . $action . '.phtml';

        $vars       = $e->getResult();
        if (is_scalar($vars)) {
            $vars = array('content' => $vars);
        } elseif (is_object($vars) && !$vars instanceof ArrayAccess) {
            $vars = (array) $vars;
        }

        $content = $this->view->render($script, $vars);

        $e->setResult($content);
        return $e->getResult();
    }

    public function renderLayout(MvcEvent $e)
    {
        $response = $e->getResponse();
        if (!$response) {
            $response = new Response();
            $e->setResponse($response);
        }
        if ($response->isRedirect()) {
            return $response;
        }

        if (false !== ($contentParam = $e->getParam('content', false))) {
            $vars['content'] = $contentParam;
        } else {
            $vars['content'] = $e->getResult();
        }

        $layout   = $this->view->render($this->layout, $vars);
        $e->setResult($layout);
        return $response;
    }
}
