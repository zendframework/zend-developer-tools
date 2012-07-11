<?php
/**
 * Zend Developer Tools for Zend Framework (http://framework.zend.com/)
 *
 * @link       http://github.com/zendframework/ZendDeveloperTools for the canonical source repository
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd New BSD License
 * @package    ZendDeveloperTools
 * @subpackage Collector
 */

namespace ZendDeveloperTools\Collector;

use Zend\Mvc\MvcEvent;

/**
 * Request Data Collector.
 *
 * @category   Zend
 * @package    ZendDeveloperTools
 * @subpackage Collector
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
        $templates   = array();
        $match       = $mvcEvent->getRouteMatch();

        $templates[] = $mvcEvent->getViewModel()->getTemplate();
        if ($mvcEvent->getViewModel()->hasChildren()) {
            foreach ($mvcEvent->getViewModel()->getChildren() as $i => $child) {
                $templates[] = $child->getTemplate();
            }
        }

        if (empty($templates)) {
            $templates[] = 'N/A';
        }

        $this->data = array(
            'templates'  => $templates,
            'method'     => $mvcEvent->getRequest()->getMethod(),
            'status'     => $mvcEvent->getResponse()->getStatusCode(),
            'route'      => ($match === null) ? 'N/A' : $match->getMatchedRouteName(),
            'action'     => ($match === null) ? 'N/A' : $match->getParam('action', 'N/A'),
            'controller' => ($match === null) ? 'N/A' : $match->getParam('controller', 'N/A'),
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
     * @param  boolean $short Removes the namespace.
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
    public function getTemplateNames()
    {
        return $this->data['templates'];
    }
}
