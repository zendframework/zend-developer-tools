<?php
/**
 * Zend Developer Tools for Zend Framework (http://framework.zend.com/)
 *
 * @link       http://github.com/zendframework/ZendDeveloperTools for the canonical source repository
 * @copyright  Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendDeveloperTools\Collector;

use Zend\Mvc\MvcEvent;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Version\Version;
use ZendDeveloperTools\Options;

/**
 * Zend Framework Collector
 */
class ZfCollector extends AbstractCollector implements ServiceLocatorAwareInterface
{
    /**
     * Dev documentation URI pattern.
     *
     * @var string
     */
    const DEV_DOC_URI_PATTERN = 'http://zf2.readthedocs.org/en/%s/index.html';

    /**
     * Documentation URI pattern.
     *
     * @var string
     */
    const DOC_URI_PATTERN = 'http://framework.zend.com/manual/%s/en/index.html';

    /**
     * Module Gallery URI.
     *
     * @var string
     */
    const MODULE_GALLERY_URI = 'http://modules.zendframework.com/';

    /**
     * Time to live for the version cache in seconds.
     *
     * @var integer
     */
    const VERSION_CACHE_TTL = 3600;

    /**
     * @var Options
     */
    protected $options = null;

    /**
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator = null;

    /**
     * Get the options
     *
     * @return \ZendDeveloperTools\Options $options
     */
    public function getOptions()
    {
        if (null === $this->options) {
            $this->setOptions($this->getServiceLocator()->get('ZendDeveloperTools\Config'));
        }
        return $this->options;
    }

    /**
     * Set the options
     *
     * @param \ZendDeveloperTools\Options $options
     * @return this
     */
    public function setOptions(Options $options)
    {
        $this->options = $options;
        return $this;
    }

    /**
     * Set service locator
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;

        return $this;
    }

    /**
     * Get service locator
     *
     * @return ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'zf2';
    }

    /**
     * @inheritdoc
     */
    public function getPriority()
    {
        return PHP_INT_MAX;
    }

    /**
     * @inheritdoc
     */
    public function collect(MvcEvent $mvcEvent)
    {
        $serviceManager = $mvcEvent->getApplication()->getServiceManager();

        $this->setServiceLocator($serviceManager);
        $this->setOptions($serviceManager->get('ZendDeveloperTools\Config'));
    }

    /**
     * Wrapper for Zend\Version::VERSION
     *
     * @return string
     */
    public function getCurrentVersion()
    {
        return Version::VERSION;
    }

    /**
     * Wrapper for Zend\Version::getLatest with caching functionality, so that
     * ZendDeveloperTools won't act as a "DDoS bot-network".
     *
     * @param  null|string $currentVersion if null, than is getCurrentVerion() used
     * @return array
     */
    public function getLatestVersion($currentVersion = null)
    {
        if (null === $currentVersion) {
        	$currentVersion = $this->getCurrentVersion();
        }

        $options = $this->getOptions();

        if (!$options->isVersionCheckEnabled()) {
            return array(true, '');
        }

        $cacheDir = $options->getCacheDir();

        // exit early if the cache dir doesn't exist,
        // to prevent hitting the GitHub API for every request.
        if (!is_dir($cacheDir)) {
            return array(true, '');
        }

        if (file_exists($cacheDir . '/ZDT_ZF_Version.cache')) {
            $cache = file_get_contents($cacheDir . '/ZDT_ZF_Version.cache');
            $cache = explode('|', $cache);

            if ($cache[0] + self::VERSION_CACHE_TTL > time()) {
                // the cache file was written before the version was upgraded.
                if ($currentVersion === $cache[2] || $cache[2] === 'N/A') {
                    return array(true, '');
                }

                return array(
                    ($cache[1] === 'yes') ? true : false,
                    $cache[2]
                );
            }
        }

        $isLatest = Version::isLatest();
        $latest   = Version::getLatest();

        file_put_contents(
            $cacheDir . '/ZDT_ZF_Version.cache',
            sprintf(
                '%d|%s|%s',
                time(),
                ($isLatest) ? 'yes' : 'no',
                ($latest === null) ? 'N/A' : $latest
            )
        );

        return array($isLatest, $latest);
    }

    /**
     * Get documentation URI for current version
     *
     * @param null|string $latest
     * @return string
     */
    public function getDocUriForVersionForCurrentVersion($latest = null)
    {
        return $this->getDocUriForVersion($this->getCurrentVersion(), $latest);
    }

    /**
     * Get documentation URI for given version
     *
     * @param string $version
     * @param string $latest
     * @return string
     */
    public function getDocUriForVersion($version, $latest = null)
    {
        if (false === ($pos = strpos($version, 'dev'))) {
            $docUri = sprintf(self::DOC_URI_PATTERN, substr($version, 0, 3));
        } else { // unreleased dev branch - compare minor part of versions
            if ($latest === null) {
                $latest = $version;
            }
            $partsCurrent       = explode('.', substr($version, 0, $pos));
            $partsLatestRelease = explode('.', $latest);
            $docUri             = sprintf(
                self::DEV_DOC_URI_PATTERN,
                current($partsLatestRelease) == $partsCurrent[1] ? 'latest' : 'develop'
            );
        }

        return $docUri;
    }

    /**
     * Get the module gallery URI
     *
     * @return string
     */
    public function getModuleGalleryUri()
    {
        return static::MODULE_GALLERY_URI;
    }

    /**
     * Get a list of loaded modules
     *
     * @return array
     */
    public function getLoadedModules()
    {
        $serviceLocator = $this->getServiceLocator();
        $moduleManager  = $serviceLocator->get('ModuleManager');

        return array_keys($moduleManager->getLoadedModules());
    }
}