<?php
namespace ZendDeveloperTools\FirePhp;

use FirePHP;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use ZendDeveloperTools\Collector\CollectorInterface;
use ZendDeveloperTools\Options;

/**
 * Abstract FirePHP log class
 */
abstract class AbstractLog implements LogWriterInterface, ServiceLocatorAwareInterface
{
    /**
     * @var \ZendDeveloperTools\Collector\CollectorInterface
     */
    protected $collector;

    /**
     * @var FirePHP
     */
    protected $firePhp;

    /**
     * @var Options
     */
    protected $options = null;

    /**
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator = null;

    /**
     * Get the collector instance
     *
     * @return \ZendDeveloperTools\Collector\CollectorInterface $collector
     */
    public function getCollector()
    {
        return $this->collector;
    }

    /**
     * Set the collector instance
     *
     * @param \ZendDeveloperTools\Collector\CollectorInterface $collector
     * @return this
     */
    public function setCollector(CollectorInterface $collector)
    {
        $this->collector = $collector;
        return $this;
    }

    /**
     * Get the $this->firePhp
     *
     * @return FirePHP $firePhp
     */
    public function getFirePhp()
    {
        if (null === $this->firePhp) {
            $this->setFirePhp($this->getServiceLocator()->get('ZendDeveloperTools\FirePhp'));
        }

        return $this->firePhp;
    }

    /**
     * Set the $this->firePhp
     *
     * @param FirePHP $firePhp
     * @return this
     */
    public function setFirePhp(FirePHP $firePhp)
    {
        $this->firePhp = $firePhp;
        return $this;
    }

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
     * Returns the formatted memory size.
     *
     * @param  integer $size
     * @param  integer $precision Only used for MegaBytes
     * @return string
     */
    public function formatMemory($size, $precision = 2)
    {
        if ($size < 1024) {
            return sprintf('%d B', $size);
        } elseif (($size / 1024) < 1024) {
            return sprintf('%.0f Kb', $size / 1024);
        } else {
            return sprintf('%.' . $precision . 'f Mb', $size / 1024 / 1024);
        }
    }

    /**
     * Returns the formatted time.
     *
     * @param  integer|float $time
     * @param  integer       $precision Will only be used for seconds.
     * @return string
     */
    public function formatTime($time, $precision = 2)
    {
        if ($time === 0) {
            return '0 s';
        }

        if ($time >= 1) {
            return sprintf('%.' . $precision . 'f s', $time);
        } elseif ($time * 1000 >= 1) {
            return sprintf('%.' . $precision . 'f ms', $time * 1000);
        } else {
            return sprintf('%.' . $precision . 'f µs', $time * 1000000);
        }
    }

    /**
     * Writes the collected data to the browser console
     *
     * @return AbstractLog
     */
    public function writeLog()
    {
        return $this->internalWriteLog();

    }

    /**
     * Writes the collected data to the browser console
     *
     * @return AbstractLog
     */
    abstract protected function internalWriteLog();
}