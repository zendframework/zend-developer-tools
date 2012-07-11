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

use Zend\Mvc\MvcEvent;

/**
 * Database Data Collector.
 *
 * @category   Zend
 * @package    ZendDeveloperTools
 * @subpackage Collector
 */
class DbCollector extends AbstractCollector implements AutoHideInterface
{
    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'db';
    }

    /**
     * @inheritdoc
     */
    public function getPriority()
    {
        return 100;
    }

    /**
     * @inheritdoc
     */
    public function collect(MvcEvent $mvcEvent)
    {
        // todo
    }

    /**
     * @inheritdoc
     */
    public function canHide()
    {
        return true;
    }

    public function getQueries()
    {
        return 0;
    }

    public function getCreateQueries()
    {
        return 0;
    }

    public function getReadQueries()
    {
        return 0;
    }

    public function getUpdateQueries()
    {
        return 0;
    }

    public function getDeleteQueries()
    {
        return 0;
    }

    public function getTime()
    {
        return 0;
    }

    public function getCreateTime()
    {
        return 0;
    }

    public function getReadTime()
    {
        return 0;
    }

    public function getUpdateTime()
    {
        return 0;
    }

    public function getDeleteTime()
    {
        return 0;
    }
}