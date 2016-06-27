<?php
/**
 * Zend Developer Tools for Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendDeveloperTools for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendDeveloperTools;

class Report implements ReportInterface
{
    /**
     * @var string
     */
    protected $ip;

    /**
     * @var string
     */
    protected $uri;

    /**
     * @var integer
     */
    protected $time;

    /**
     * @var string
     */
    protected $token;

    /**
     * @var array
     */
    protected $errors;

    /**
     * @var string
     */
    protected $method;

    /**
     * @var array
     */
    protected $collectors = [];

    /**
     * {@inheritdoc}
     */
    public function setIp($ip)
    {
        $this->ip = $ip;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * {@inheritdoc}
     */
    public function setUri($uri)
    {
        $this->uri = $uri;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * {@inheritdoc}
     */
    public function setTime($time)
    {
        $this->time = $time;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * {@inheritdoc}
     */
    public function setToken($token)
    {
        $this->token = $token;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * {@inheritdoc}
     */
    public function addError($error)
    {
        if (! isset($this->errors)) {
            $this->errors = [];
        }

        $this->errors[] = $error;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * {@inheritdoc}
     */
    public function hasErrors()
    {
        return !empty($this->errors);
    }

    /**
     * {@inheritdoc}
     */
    public function setMethod($method)
    {
        $this->method = $method;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * {@inheritdoc}
     */
    public function hasCollector($name)
    {
        return isset($this->collectors[$name]);
    }

    /**
     * {@inheritdoc}
     */
    public function getCollector($name)
    {
        if (isset($this->collectors[$name])) {
            return $this->collectors[$name];
        }
        return;
    }

    /**
     * {@inheritdoc}
     */
    public function getCollectors()
    {
        return $this->collectors;
    }

    /**
     * {@inheritdoc}
     */
    public function getCollectorNames()
    {
        return array_keys($this->collectors);
    }

    /**
     * {@inheritdoc}
     */
    public function setCollectors(array $collectors)
    {
        foreach ($collectors as $collector) {
            $this->addCollector($collector);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addCollector(Collector\CollectorInterface $collector)
    {
        $this->collectors[$collector->getName()] = $collector;
    }

    /**
     * @return array
     */
    public function __sleep()
    {
        return ['ip', 'uri', 'time', 'token', 'errors', 'method', 'collectors'];
    }
}
