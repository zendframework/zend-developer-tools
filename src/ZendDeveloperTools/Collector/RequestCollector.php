<?php
/**
 * Zend Developer Tools for Zend Framework (http://framework.zend.com/)
 *
 * @link       http://github.com/zendframework/ZendDeveloperTools for the canonical source repository
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ZendDeveloperTools\Collector;

use Zend\Mvc\MvcEvent;
use Zend\View\Variables;

/**
 * Request Data Collector.
 */
class RequestCollector extends AbstractCollector
{

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'request';
    }

    /**
     * @inheritdoc
     */
    public function getPriority()
    {
        return 100;
    }

    /**
     * @inheritdoc
     */
    public function collect(MvcEvent $mvcEvent)
    {
        $views = array();
        $match = $mvcEvent->getRouteMatch();
        
        $vars = $mvcEvent->getViewModel()->getVariables();
        if($vars instanceof Variables){
            $vars = $vars->getArrayCopy();
        }
        $views[] = array(
            'template' => $mvcEvent->getViewModel()->getTemplate(),
            'vars' => $vars
        );
        
        if ($mvcEvent->getViewModel()->hasChildren()) {
            foreach ($mvcEvent->getViewModel()->getChildren() as $child) {
                $vars = $child->getVariables();
                if($vars instanceof Variables){
                    $vars = $vars->getArrayCopy();
                }
                
                $views[] = array(
                    'template' => $child->getTemplate(),
                    'vars' => $vars
                );
            }
        }
        
        if (empty($views)) {
            $views[] = array(
                'template' => 'N/A',
                'vars' => array()
            );
        }
        
        $this->data = array(
            'views' => $views,
            'method' => $mvcEvent->getRequest()->getMethod(),
            'status' => $mvcEvent->getResponse()->getStatusCode(),
            'route' => ($match === null) ? 'N/A' : $match->getMatchedRouteName(),
            'action' => ($match === null) ? 'N/A' : $match->getParam('action', 'N/A'),
            'controller' => ($match === null) ? 'N/A' : $match->getParam('controller', 'N/A')
        );
    }

    /**
     * Returns the response status code.
     *
     * @return string
     */
    public function getStatusCode()
    {
        return $this->data['status'];
    }

    /**
     * Returns the request method
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->data['method'];
    }

    /**
     * Returns the matched route name if possible, otherwise N/A.
     *
     * @return string
     */
    public function getRouteName()
    {
        return $this->data['route'];
    }

    /**
     * Returns the action name if possible, otherwise N/A.
     *
     * @return string
     */
    public function getActionName()
    {
        return $this->data['action'];
    }

    /**
     * Returns the controller name if possible, otherwise N/A.
     *
     * @return string
     */
    public function getControllerName()
    {
        return $this->data['controller'];
    }

    /**
     * Returns the controller and action name if possible, otherwise N/A.
     *
     * @param bool $short
     *            Removes the namespace.
     * @return string
     */
    public function getFullControllerName($short = true)
    {
        if ($short) {
            $controller = explode('\\', $this->data['controller']);
            $controller = array_pop($controller);
        } else {
            $controller = $this->data['controller'];
        }
        
        $return = sprintf('%s::%s', $controller, $this->data['action']);
        
        if ($return === 'N/A::N/A') {
            return 'N/A';
        } else {
            return $return;
        }
    }

    /**
     * Returns the template name if possible, otherwise N/A.
     *
     * @return string
     */
    public function getViews()
    {
        return $this->data['views'];
    }
}
