<?php
/**
 * Zend Developer Tools for Zend Framework (http://framework.zend.com/)
 *
 * @link       http://github.com/zendframework/ZendDeveloperTools for the canonical source repository
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendDeveloperTools\Collector;

use Zend\EventManager\Event;

/**
 * Event Data Collector Interface.
 *
 */
interface EventCollectorInterface
{
    /**
     * Collects event-level information
     *
     * @param string $id
     * @param Event  $event
     */
    public function collectEvent($id, Event $event);
}
