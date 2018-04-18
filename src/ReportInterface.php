<?php
/**
 * @see       https://github.com/zendframework/zend-developer-tools for the canonical source repository
 * @copyright Copyright (c) 2011-2018 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-developer-tools/blob/master/LICENSE.md New BSD License
 */

namespace ZendDeveloperTools;

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
     * @return bool
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
     * @return bool
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
