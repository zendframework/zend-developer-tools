<?php
/**
 * ZendDeveloperTools
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    ZendDeveloperTools
 * @subpackage Report
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendDeveloperTools;

/**
 * @category   Zend
 * @package    ZendDeveloperTools
 * @subpackage Report
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
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
     * @param  string $url
     * @return self
     */
    public function setUrl($url);

    /**
     * @return string
     */
    public function getUrl();

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
     * @param  array $error
     * @return self
     */
    public function setErrors($errors);

    /**
     * @return array|null
     */
    public function getErrors();

    /**
     * @param  string $ip
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