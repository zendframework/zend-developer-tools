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
 * Event Data Collector Interface.
 *
 */
interface EventCollectorInterface
{
    /**
     * Saves the current time in microseconds for an specific event.
     *
     * @param string                          $id
     * @param \Zend\EventManager\Event|string $event
     */
    public function collectEvent($id, $event);
}