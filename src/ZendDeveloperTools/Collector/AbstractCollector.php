<?php
/**
 * Zend Developer Tools for Zend Framework (http://framework.zend.com/)
 *
 * @link       http://github.com/zendframework/ZendDeveloperTools for the canonical source repository
 * @copyright  Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendDeveloperTools\Collector;

/**
 * Serializable Collector base class.
 *
 */
abstract class AbstractCollector implements CollectorInterface, \Serializable
{
    /**
     * Collected Data
     *
     * @var array
     */
    protected $data;

    /**
     * This function will replace Closure instances with the string 'Closure' to prevent serialize problems.
     *
     * @param array $data
     * @return array
     */
    protected function _preSerialize($data)
    {
        if (!$data) {
            return $data;
        }

        foreach ($data as $key => $value) {
            if ($value instanceof \Closure) {
                $value = 'Closure';
            } elseif (is_array($value)) {
                $value = $this->_preSerialize($value);
            }
            $data[$key] = $value;
        }
        return $data;
    }

    /**
     * @see \Serializable
     */
    public function serialize()
    {
        return serialize($this->_preSerialize($this->data));
    }

    /**
     * @see \Serializable
     */
    public function unserialize($data)
    {
        $this->data = unserialize($data);
    }
}