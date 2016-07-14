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
use Zend\View\Model\ModelInterface;
use Zend\View\Variables;

/**
 * Request Data Collector.
 */
class RequestCollector extends AbstractCollector
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'request';
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        return 100;
    }

    /**
     * {@inheritdoc}
     */
    public function collect(MvcEvent $mvcEvent)
    {
        $views = [];
        $match = $mvcEvent->getRouteMatch();
        $viewModel = $mvcEvent->getViewModel();

        $addToViewFromModel = function (ModelInterface $child) use (&$views) {
            $vars = $child->getVariables();

            if ($vars instanceof Variables) {
                $vars = $vars->getArrayCopy();
            }
            $vars = (array) $vars;

            foreach ($vars as $key => &$var) {
                $var = $key . ': ' . (is_object($var) ? get_class($var) : gettype($var));
            }
            sort($vars);

            $views[] = [
                'template' => $child->getTemplate(),
                'vars' => $vars,
            ];
        };

        $addToViewFromModel($viewModel);
        $this->addChildrenToView($viewModel, $addToViewFromModel);

        $this->data = [
            'views'      => $views,
            'method'     => $mvcEvent->getRequest()->getMethod(),
            'status'     => $mvcEvent->getResponse()->getStatusCode(),
            'route'      => ($match === null) ? 'N/A' : $match->getMatchedRouteName(),
            'action'     => ($match === null) ? 'N/A' : $match->getParam('action', 'N/A'),
            'controller' => ($match === null) ? 'N/A' : $match->getParam('controller', 'N/A'),
            'other_route_parameters' => ($match === null) ? 'N/A' : array_filter($match->getParams(), function ($key) {
                return ! in_array($key, ['action', 'controller']);
            }, ARRAY_FILTER_USE_KEY),
        ];
    }

    /**
     * @param ModelInterface $viewModel
     * @param callable $addToViewFromModel
     */
    protected function addChildrenToView(ModelInterface $viewModel, $addToViewFromModel)
    {
        if (! $viewModel->hasChildren()) {
            return;
        }

        foreach ($viewModel->getChildren() as $child) {
            $addToViewFromModel($child);
            $this->addChildrenToView($child, $addToViewFromModel);
        }
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
     * Returns parameters except controller and actions
     * @return array
     */
    public function getOtherParameters()
    {
        return $this->data['other_route_parameters'];
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
        $controller = $this->data['controller'];

        if ($short) {
            $controller = explode('\\', $controller);
            $controller = array_pop($controller);
        }

        $return = sprintf('%s::%s', $controller, $this->data['action']);

        if ($return === 'N/A::N/A') {
            return 'N/A';
        }

        return $return;
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
