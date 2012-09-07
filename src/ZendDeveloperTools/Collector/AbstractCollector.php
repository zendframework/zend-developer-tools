<?php
/**
 * Zend Developer Tools for Zend Framework (http://framework.zend.com/)
 *
 * @link       http://github.com/zendframework/ZendDeveloperTools for the canonical source repository
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd New BSD License
 * @package    ZendDeveloperTools
 * @subpackage Collector
 */

namespace ZendDeveloperTools\Collector;

use Serializable;
use ZendDeveloperTools\Collector\Feature\PriorityProviderInterface;

/**
 * Serializable collector base class.
 *
 * @category   Zend
 * @package    ZendDeveloperTools
 * @subpackage Collector
 */
abstract class AbstractCollector implements
    Serializable,
    CollectorInterface,
    PriorityProviderInterface
{
    /**
     * @var array
     */
    protected $data;

    /**
     * @var string
     */
    protected $name = null;

    /**
     * @var integer
     */
    protected $priority = 0;

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @inheritdoc
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * @see Serializable
     */
    public function serialize()
    {
        return serialize($this->data);
    }

    /**
     * @param string $data
     * @see   Serializable
     */
    public function unserialize($data)
    {
        $this->data = unserialize($data);
    }
}