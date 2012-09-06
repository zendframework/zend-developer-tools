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
 * Database (Zend\Db) Data Collector.
 *
 * @category   Zend
 * @package    ZendDeveloperTools
 * @subpackage Collector
 */
class PhpCollector extends AbstractCollector implements AutoHideInterface
{
    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'php';
    }

    /**
     * @inheritdoc
     */
    public function getPriority()
    {
        return 20;
    }

    /**
     * @inheritdoc
     */
    public function collect(MvcEvent $mvcEvent)
    {
        return;
    }

    /**
     * @inheritdoc
     */
    public function canHide()
    {
        return true;
    }
}