<?php
/**
 * @see       https://github.com/zendframework/zend-developer-tools for the canonical source repository
 * @copyright Copyright (c) 2011-2018 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-developer-tools/blob/master/LICENSE.md New BSD License
 */

namespace ZendDeveloperTools\Collector;

use Zend\EventManager\EventInterface;

/**
 * Event Data Collector Interface.
 */
interface EventCollectorInterface
{
    /**
     * Collects event-level information
     *
     * @param string         $id
     * @param EventInterface $event
     */
    public function collectEvent($id, EventInterface $event);
}
