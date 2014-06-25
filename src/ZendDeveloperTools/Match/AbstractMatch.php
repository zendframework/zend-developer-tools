<?php
/**
 * Zend Developer Tools for Zend Framework (http://framework.zend.com/)
 *
 * @link       http://github.com/zendframework/ZendDeveloperTools for the canonical source repository
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendDeveloperTools\Match;

use ZendDeveloperTools\ProfilerEvent;

abstract class AbstractMatch implements MatchInterface
{
    /**
     * @var ProfilerEvent
     */
    protected $event;
     
    /**
     * Compose an Event
     *
     * @param  ProfilerEvent $event
     * @return void
     */
    public function setEvent(ProfilerEvent $event)
    {
        $this->event = $event;
    }
    
    /**
     * Retrieve the composed event
     *
     * @return ProfilerEvent
    */
    public function getEvent()
    {
        return $this->event;
    }
}
