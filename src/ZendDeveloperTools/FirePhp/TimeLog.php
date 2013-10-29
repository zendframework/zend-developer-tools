<?php
/**
 * Zend Developer Tools for Zend Framework (http://framework.zend.com/)
 *
 * @link       http://github.com/zendframework/ZendDeveloperTools for the canonical source repository
 * @copyright  Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ZendDeveloperTools\FirePhp;

use ZendDeveloperTools\Collector\TimeCollector;

/**
 * The FirePHP time data provider class
 */
class TimeLog extends AbstractLabeledLog
{
    /**
     * Get the time collector
     *
     * @return \ZendDeveloperTools\Collector\TimeCollector $collector
     */
    public function getCollector()
    {
        if (null === $this->collector) {
            $collector = $this->getServiceLocator()->get('ZendDeveloperTools\TimeCollector');
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
                'Execution Time (%s)',
                $this->formatTime($collector->getExecutionTime())
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

        $firePhp->log($this->formatTime($collector->getExecutionTime()), 'Execution Time');

        if ($collector->hasEventTimes()) {

            $firePhp->log('Event Times');

            foreach ($collector->getApplicationEventTimes() as $key => $value) {
                $firePhp->log($this->formatTime($value), $key);
            }
        }

        return $this;
    }
}