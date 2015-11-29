<?php
/**
 * Zend Developer Tools for Zend Framework (http://framework.zend.com/)
 *
 * @link       http://github.com/zendframework/ZendDeveloperTools for the canonical source repository
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendDeveloperTools\Collector;

use Serializable;
use Traversable;
use Closure;
use Zend\Mvc\MvcEvent;
use Zend\Stdlib\ArrayObject;
use Zend\Stdlib\ArrayUtils;
use ZendDeveloperTools\Stub\ClosureStub;

/**
 * Config data collector - dumps the contents of the `Config` and `ApplicationConfig` services
 */
class ConfigCollector implements CollectorInterface, Serializable
{
    const NAME     = 'config';
    const PRIORITY = 100;
    const OBFUSCATE_STRING = '******';

    /**
     * @var array|null
     */
    protected $config;

    /**
     * @var array|null
     */
    protected $applicationConfig;

    /**
     * @var array
     */
    protected $riskyConfigKeys = array(
        'api_key',
        'dsn',
        'database',
        'key',
        'license',
        'license_key',
        'password',
        'pwd',
        'pass',
        'username',
        'usr',
    );

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return static::NAME;
    }

    /**
     * {@inheritDoc}
     */
    public function getPriority()
    {
        return static::PRIORITY;
    }

    /**
     * {@inheritDoc}
     */
    public function collect(MvcEvent $mvcEvent)
    {
        if (! $application = $mvcEvent->getApplication()) {
            return;
        }

        $serviceLocator = $application->getServiceManager();

        if ($serviceLocator->has('Config')) {
            $this->config = $this->makeArraySerializable($serviceLocator->get('Config'));
        }

        if ($serviceLocator->has('ApplicationConfig')) {
            $this->applicationConfig = $this->makeArraySerializable($serviceLocator->get('ApplicationConfig'));
        }
    }

    /**
     * @return array|null
     */
    public function getConfig()
    {
        return isset($this->config) ? $this->obfuscateConfigValues(
            $this->unserializeArray($this->config)
        ) : null;
    }

    /**
     * @return array|null
     */
    public function getApplicationConfig()
    {
        return isset($this->applicationConfig) ? $this->unserializeArray($this->applicationConfig) : null;
    }

    /**
     * {@inheritDoc}
     */
    public function serialize()
    {
        return serialize(array('config' => $this->config, 'applicationConfig' => $this->applicationConfig));
    }

    /**
     * {@inheritDoc}
     */
    public function unserialize($serialized)
    {
        $data                    = unserialize($serialized);
        $this->config            = $data['config'];
        $this->applicationConfig = $data['applicationConfig'];
    }

    /**
     * Replaces Risky Config Values with a obfuscate string
     *
     * @param array $data
     * @return array
     */
    private function obfuscateConfigValues(array $data)
    {
        $obfuscateData = new ArrayObject($data);

        foreach (ArrayUtils::iteratorToArray($data) as $key => $value) {
            if ($value instanceof Traversable || is_array($value)) {
                $obfuscateData[$key] = $this->obfuscateConfigValues($value);

                continue;
            }

            if (is_string($key) && in_array($key, $this->riskyConfigKeys)) {
                $obfuscateData[$key] = self::OBFUSCATE_STRING;
            }
        }

        return $obfuscateData->getArrayCopy();
    }

    /**
     * Replaces the un-serializable items in an array with stubs
     *
     * @param array|\Traversable $data
     *
     * @return array
     */
    private function makeArraySerializable($data)
    {
        $serializable = array();

        foreach (ArrayUtils::iteratorToArray($data) as $key => $value) {
            if ($value instanceof Traversable || is_array($value)) {
                $serializable[$key] = $this->makeArraySerializable($value);

                continue;
            }

            if ($value instanceof Closure) {
                $serializable[$key] = new ClosureStub();

                continue;
            }

            $serializable[$key] = $value;
        }

        return $serializable;
    }

    /**
     * Opposite of {@see makeArraySerializable} - replaces stubs in an array with actual un-serializable objects
     *
     * @param array $data
     *
     * @return array
     */
    private function unserializeArray(array $data)
    {
        $unserialized = array();

        foreach (ArrayUtils::iteratorToArray($data) as $key => $value) {
            if ($value instanceof Traversable || is_array($value)) {
                $unserialized[$key] = $this->unserializeArray($value);

                continue;
            }

            if ($value instanceof ClosureStub) {
                $unserialized[$key] = function () {};

                continue;
            }

            $unserialized[$key] = $value;
        }

        return $unserialized;
    }
}
