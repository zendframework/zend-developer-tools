<?php
/**
 * @see       https://github.com/zendframework/zend-developer-tools for the canonical source repository
 * @copyright Copyright (c) 2011-2018 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-developer-tools/blob/master/LICENSE.md New BSD License
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
     * @see \Serializable
     */
    public function serialize()
    {
        return serialize($this->data);
    }

    /**
     * @see \Serializable
     */
    public function unserialize($data)
    {
        $this->data = unserialize($data);
    }
}
