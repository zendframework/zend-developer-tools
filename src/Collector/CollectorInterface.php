<?php
/**
 * @see       https://github.com/zendframework/ZendDeveloperTools for the canonical source repository
 * @copyright Copyright (c) 2011-2018 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/ZendDeveloperTools/blob/master/LICENSE.md New BSD License
 */

namespace ZendDeveloperTools\Collector;

use Zend\Mvc\MvcEvent;

/**
 * Collector Interface.
 */
interface CollectorInterface
{
    /**
     * Collector Name.
     *
     * @return string
     */
    public function getName();

    /**
     * Collector Priority.
     *
     * @return integer
     */
    public function getPriority();

    /**
     * Collects data.
     *
     * @param MvcEvent $mvcEvent
     */
    public function collect(MvcEvent $mvcEvent);
}
