<?php
/**
 * Zend Developer Tools for Zend Framework (http://framework.zend.com/)
 *
 * @link       http://github.com/zendframework/ZendDeveloperTools for the canonical source repository
 * @copyright  Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendDeveloperTools\FirePhp;

use ZendDeveloperTools\Collector\MemoryCollector;

/**
 * The FirePHP memory log class
 */
class MemoryLog extends AbstractLabeledLog
{
    /**
     * Get the memory collector
     *
     * @return \ZendDeveloperTools\Collector\MemoryCollector $collector
     */
    public function getCollector()
    {
        if (null === $this->collector) {
            $collector = $this->getServiceLocator()->get('ZendDeveloperTools\MemoryCollector');
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
                'Memory (%s)',
                $this->formatMemory($collector->getMemory())
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

        $firePhp->log($this->formatMemory($collector->getMemory()), 'Memory');

        if ($collector->hasEventMemory()) {

            $firePhp->log('Event Memory');

            foreach ($collector->getApplicationEventMemory() as $key => $value) {
                $firePhp->log($this->formatTime($value), $key);
            }
        }

        return $this;
    }
}