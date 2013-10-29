<?php
/**
 * Zend Developer Tools for Zend Framework (http://framework.zend.com/)
 *
 * @link       http://github.com/zendframework/ZendDeveloperTools for the canonical source repository
 * @copyright  Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendDeveloperTools\FirePhp;

use ZendDeveloperTools\Collector\RequestCollector;

/**
 * The FirePHP request log class
 */
class RequestLog extends AbstractLabeledLog
{
    /**
     * @var RequestCollector
     */
    protected $collector;

    /**
     * Get the $this->collector
     *
     * @return \ZendDeveloperTools\Collector\RequestCollector $collector
     */
    public function getCollector()
    {
        if (null === $this->collector) {
            $collector = $this->getServiceLocator()->get('ZendDeveloperTools\RequestCollector');
            $this->setCollector($collector);
        }
        return $this->collector;
    }

    /**
     * @inheritdoc
     */
    public function getGroupLabel()
    {
        if (null === $this->groupLabel) {
            $collector = $this->getCollector();
            $this->groupLabel = sprintf(
                'Request (%d %s::%s on %s)',
                $collector->getStatusCode(),
                $collector->getFullControllerName(true),
                $collector->getActionName(),
                $collector->getRouteName()
            );
        }
        return $this->groupLabel;
    }

    /**
     * @inheritdoc
     */
    protected function internalWriteLog()
    {
        $firePhp   = $this->getFirePhp();
        $collector = $this->getCollector();

        $firePhp->log($collector->getStatusCode(), 'Status Code');
        $firePhp->log($collector->getMethod(), 'Method');
        $firePhp->log($collector->getControllerName(), 'Controller');
        $firePhp->log($collector->getActionName(), 'Action');
        $firePhp->log($collector->getRouteName(), 'Route');
        $firePhp->log($collector->getTemplateNames(), 'Templates');

        return $this;
    }
}