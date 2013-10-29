<?php
/**
 * Zend Developer Tools for Zend Framework (http://framework.zend.com/)
 *
 * @link       http://github.com/zendframework/ZendDeveloperTools for the canonical source repository
 * @copyright  Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendDeveloperTools\FirePhp;

use FirePHP;

/**
 * The FirePHP zf log class
 */
class ZfLog extends AbstractLabeledLog
{
    /**
     * @var string
     */
    protected $groupLabel = 'ZF 2';

    /**
     * Get the memory collector
     *
     * @return \ZendDeveloperTools\Collector\ZfCollector $collector
     */
    public function getCollector()
    {
        if (null === $this->collector) {
            $collector = $this->getServiceLocator()->get('ZendDeveloperTools\ZfCollector');
            $this->setCollector($collector);
        }
        return $this->collector;
    }

    /**
     * Writes the collected data to the browser console
     *
     * @return AbstractLog
     */
    protected function internalWriteLog()
    {
        $firePhp   = $this->getFirePhp();
        $collector = $this->getCollector();

        $currentVersion          = $collector->getCurrentVersion();
        list($isLatest, $latest) = $collector->getLatestVersion($currentVersion);

        $firePhp->log($collector->getDocUriForVersionForCurrentVersion($latest), 'Documentation');
        $firePhp->log($collector->getModuleGalleryUri(), 'Module Gallery');

        if ($isLatest) {
            $firePhp->log($currentVersion, 'Zend Framework Version');
        } else {
            $firePhp->log($currentVersion, 'Zend Framework Version', array('type' => FirePHP::WARN));
            $firePhp->log($latest, 'New Zend Framework Version', array('type' => FirePHP::INFO));
        }

        $firePhp->log(phpversion(), 'PHP Version', array('type' => FirePHP::INFO));
        $firePhp->log(get_loaded_extensions(), 'PHP Extensions', array('type' => FirePHP::INFO));

        //Loaded Modules
        $firePhp->group('Modules', $this->getGroupOptions());

        $modules = $collector->getLoadedModules();
        foreach ($modules as $module) {
            $firePhp->log($module);
        }

        $firePhp->groupEnd();

        return $this;
    }
}