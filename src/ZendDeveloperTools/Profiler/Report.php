<?php
/**
 * Zend Developer Tools for Zend Framework (http://framework.zend.com/)
 *
 * @link       http://github.com/zendframework/ZendDeveloperTools for the canonical source repository
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd New BSD License
 * @package    ZendDeveloperTools
 * @subpackage Profiler
 */

namespace ZendDeveloperTools\Profiler;

use DateTime;
use ZendDeveloperTools\Profiler\Collector\CollectorInterface;

/**
 * @category   Zend
 * @package    ZendDeveloperTools
 * @subpackage Profiler
 */
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
     * @var DateTime
     */
    protected $dateTime;

    /**
     * @var string
     */
    protected $token;

    /**
     * @var string
     */
    protected $method;

    /**
     * @var array
     */
    protected $collectors = array();

    /**
     * @inheritdoc
     */
    public function setIp($ip)
    {
        $this->ip = $ip;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * @inheritdoc
     */
    public function setUri($uri)
    {
        $this->uri = $uri;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getUri()
    {
        return $this->url;
    }

    /**
     * @inheritdoc
     */
    public function setDateTime(DateTime $dateTime)
    {
        $this->dateTime = $dateTime;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getDateTime()
    {
        return $this->time;
    }

    /**
     * @inheritdoc
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @inheritdoc
     */
    public function setMethod($method)
    {
        $this->method = $method;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @inheritdoc
     */
    public function getCollectors()
    {
        return $this->collectors;
    }

    /**
     * @inheritdoc
     */
    public function addCollector(CollectorInterface $collector)
    {
        $this->collectors[$collector->getName()] = $collector;
    }

    /**
     * @return array
     */
    public function __sleep()
    {
        return array('ip', 'uri', 'dateTime', 'token', 'method', 'collectors');
    }
}