<?php
/**
 * @see       https://github.com/zendframework/zend-developer-tools for the canonical source repository
 * @copyright Copyright (c) 2011-2018 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-developer-tools/blob/master/LICENSE.md New BSD License
 */

namespace ZendDeveloperTools\Collector;

use Closure;
use Serializable;
use Traversable;
use ZendDeveloperTools\Stub\ClosureStub;
use Zend\Mvc\MvcEvent;
use Zend\Stdlib\ArrayUtils;

/**
 * Config data collector - dumps the contents of the `Config` and `ApplicationConfig` services
 */
class ConfigCollector implements CollectorInterface, Serializable
{
    const NAME     = 'config';
    const PRIORITY = 100;

    /**
     * @var array|null
     */
    protected $config;

    /**
     * @var array|null
     */
    protected $applicationConfig;

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

        if ($serviceLocator->has('config')) {
            $this->config = $this->makeArraySerializable($serviceLocator->get('config'));
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
        return isset($this->config) ? $this->unserializeArray($this->config) : null;
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
        return serialize(['config' => $this->config, 'applicationConfig' => $this->applicationConfig]);
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
     * Replaces the un-serializable items in an array with stubs
     *
     * @param array|\Traversable $data
     * @return array
     */
    private function makeArraySerializable($data)
    {
        $serializable = [];

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
     * @return array
     */
    private function unserializeArray(array $data)
    {
        $unserialized = [];

        foreach (ArrayUtils::iteratorToArray($data) as $key => $value) {
            if ($value instanceof Traversable || is_array($value)) {
                $unserialized[$key] = $this->unserializeArray($value);
                continue;
            }

            if ($value instanceof ClosureStub) {
                $unserialized[$key] = function () {
                };
                continue;
            }

            $unserialized[$key] = $value;
        }

        return $unserialized;
    }
}
