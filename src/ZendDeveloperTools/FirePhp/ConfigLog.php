<?php
/**
 * Zend Developer Tools for Zend Framework (http://framework.zend.com/)
 *
 * @link       http://github.com/zendframework/ZendDeveloperTools for the canonical source repository
 * @copyright  Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ZendDeveloperTools\FirePhp;

/**
 * The FirePHP config log class
 */
class ConfigLog extends AbstractLabeledLog
{
    /**
     * Return the config collector
     *
     * @return \ZendDeveloperTools\Collector\ConfigCollector $collector
     */
    public function getCollector()
    {
        if (null === $this->collector) {
            $collector = $this->getServiceLocator()->get('ZendDeveloperTools\ConfigCollector');
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
            $this->groupLabel = 'Config';
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

        $firePhp->log($collector->getConfig(), null, array('maxArrayDepth' => 100));

        return $this;
    }
}