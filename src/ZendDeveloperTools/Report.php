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
class Report implements ReportInterface
{
    /**
     * @var string
     */
    protected $ip;

    /**
     * @var string
     */
    protected $url;

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
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @inheritdoc
     */
    public function setTime($time)
    {
        $this->time = $time;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getTime()
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
    public function setErrors($errors)
    {
        if (!isset($this->errors)) {
            $this->errors = array();
        }

        foreach ($errors as $collector => $error) {
            if (isset($this->errors[$collector])) {
                $this->errors[$collector][] = $error;
            } else {
                $this->errors[$collector] = array($error);
            }
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getErrors()
    {
        return $this->errors;
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
    public function hasCollector($name)
    {
        return isset($this->collectors[$name]);
    }

    /**
     * @inheritdoc
     */
    public function getCollector($name)
    {
        if (isset($this->collectors[$name])) {
            return $this->collectors[$name];
        }

        return null;
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
    public function setCollectors(array $collectors)
    {
        foreach ($collectors as $collector) {
            $this->addCollector($collector);
        }

        return $this;
    }

    /**
     * @inheritdoc
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
        return array('ip', 'url', 'time', 'token', 'errors', 'method', 'collectors');
    }
}