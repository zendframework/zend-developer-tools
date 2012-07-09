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
 * @subpackage Collector
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendDeveloperTools\Collector;

use Zend\Mvc\MvcEvent;

/**
 * Database Data Collector.
 *
 * @category   Zend
 * @package    ZendDeveloperTools
 * @subpackage Collector
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class DbCollector extends CollectorAbstract
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