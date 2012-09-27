<?php
/**
 * Zend Developer Tools for Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendDeveloperTools for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   ZendDeveloperTools
 */

namespace ZendDeveloperTools;

/**
 * @category   Zend
 * @package    ZendDeveloperTools
 */
interface ReportInterface
{
    /**
     * @param  string $ip
     * @return self
     */
    public function setIp($ip);

    /**
     * @return string
     */
    public function getIp();

    /**
     * @param  string $uri
     * @return self
     */
    public function setUri($uri);

    /**
     * @return string
     */
    public function getUri();

    /**
     * @param  \DateTime $time
     * @return self
     */
    public function setTime($time);

    /**
     * @return \DateTime
     */
    public function getTime();

    /**
     * @param  string $token
     * @return self
     */
    public function setToken($token);

    /**
     * @return string
     */
    public function getToken();

    /**
     * @param  string $error
     * @return self
     */
    public function addError($error);

    /**
     * @return array|null
     */
    public function getErrors();

    /**
     * @return boolean
     */
    public function hasErrors();

    /**
     * @param  string $method
     * @return self
     */
    public function setMethod($method);

    /**
     * @return string
     */
    public function getMethod();

    /**
     * @param  string $name
     * @return boolean
     */
    public function hasCollector($name);

    /**
     * @param  string $name
     * @return Collector\CollectorInterface|null
     */
    public function getCollector($name);

    /**
     * @return array
     */
    public function getCollectors();

    /**
     * @return array
     */
    public function getCollectorNames();

    /**
     * @param  array $collectors
     * @return self
     */
    public function setCollectors(array $collectors);

    /**
     * @param  Collector\CollectorInterface $collector
     * @return self
     */
    public function addCollector(Collector\CollectorInterface $collector);
}
