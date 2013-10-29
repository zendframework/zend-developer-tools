<?php
/**
 * Zend Developer Tools for Zend Framework (http://framework.zend.com/)
 *
 * @link       http://github.com/zendframework/ZendDeveloperTools for the canonical source repository
 * @copyright  Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendDeveloperTools\FirePhp;

use BjyProfiler\Db\Profiler\Profiler;
use FirePHP;

/**
 * The FirePHP db log class
 */
class DbLog extends AbstractLabeledLog
{
    /**
     * Return the db collector
     *
     * @return \ZendDeveloperTools\Collector\DbCollector $collector
     */
    public function getCollector()
    {
        if (null === $this->collector) {
            $collector = $this->getServiceLocator()->get('ZendDeveloperTools\DbCollector');
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
            if ($collector->hasProfiler()) {
                $this->groupLabel = sprintf('Database Profiler (%d queries in %s)', $collector->getQueryCount(), $this->formatTime($collector->getQueryTime()));
            } else {
                $this->groupLabel = 'Database Profiler (N/A)';
            }
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

        if ($collector->hasProfiler()) {
            //Quantity
            $this->writeQuantityGroup();

            //Time
            $this->writeTimeGroup();

            //Query Profiles
            $this->writeQueryProfiles();
        } else {
            $firePhp->log(
                'You have to install or enable @bjyoungblood\'s Zend\Db Profiler (https://github.com/bjyoungblood/BjyProfiler) to use this feature.',
                'Error',
                array('type' => FirePHP::ERROR)
            );
        }

        return $this;
    }

    /**
     * Write query quantities to the browser console
     *
     * @return \ZendDeveloperTools\FirePhp\DbLog
     */
    protected function writeQuantityGroup()
    {
        $firePhp   = $this->getFirePhp();
        $collector = $this->getCollector();

        $firePhp->group('Quantity', $this->getGroupOptions());

        $firePhp->log($collector->getQueryCount(Profiler::INSERT), 'create');
        $firePhp->log($collector->getQueryCount(Profiler::SELECT), 'read');
        $firePhp->log($collector->getQueryCount(Profiler::UPDATE), 'update');
        $firePhp->log($collector->getQueryCount(Profiler::DELETE), 'delete');

        $firePhp->groupEnd();

        return $this;
    }

    /**
     * Write query times to the browser console
     *
     * @return \ZendDeveloperTools\FirePhp\DbLog
     */
    protected function writeTimeGroup()
    {
        $firePhp   = $this->getFirePhp();
        $collector = $this->getCollector();

        $firePhp->group('Time', $this->getGroupOptions());

        $firePhp->log($this->formatTime($collector->getQueryTime(Profiler::INSERT)), 'create');
        $firePhp->log($this->formatTime($collector->getQueryTime(Profiler::SELECT)), 'read');
        $firePhp->log($this->formatTime($collector->getQueryTime(Profiler::UPDATE)), 'update');
        $firePhp->log($this->formatTime($collector->getQueryTime(Profiler::DELETE)), 'delete');

        $firePhp->groupEnd();

        return $this;
    }

    /**
     * Write query profiles to the browser console
     *
     * @return \ZendDeveloperTools\FirePhp\DbLog
     */
    protected function writeQueryProfiles()
    {
        $firePhp           = $this->getFirePhp();
        $allRequestHeaders = $firePhp->getAllRequestHeaders();
        $isFireFox         = (bool) strpos($allRequestHeaders['user-agent'], 'Firefox');

        if ($isFireFox) {
            $firePhp->table('Query Profiles', $this->getQueryProfilesTable());
        } else {
            $firePhp->group('Query Profiles', $this->getGroupOptions());

            $firePhp->table('', $this->getQueryProfilesTable());

            $firePhp->groupEnd();
        }

        return $this;
    }

    /**
     * Get the query profiles table data array
     *
     * @return array
     */
    protected function getQueryProfilesTable()
    {
        $profiler = $this->getCollector()->getProfiler();

        $table   = array();
        $table[] = array('SQL', 'Params', 'Time');

        foreach ($profiler->getQueryProfiles() as $profile) {
            $query               = $profile->toArray();
            $query['parameters'] = isset($query['parameters']) ? $query['parameters'] : array();
            $paramString         = '';

            foreach($query['parameters'] as $key => $value) {
                if (strlen($paramString) > 0) {
                    $paramString .= ', ';
                }
                $paramString .= $key . ' => ' .  var_export($value, true);
            }

            $table[] = array($query['sql'], $paramString, $this->formatTime($query['elapsed']));
        }

        return $table;
    }
}