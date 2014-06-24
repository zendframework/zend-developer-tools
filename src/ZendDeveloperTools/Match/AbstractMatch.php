<?php

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
