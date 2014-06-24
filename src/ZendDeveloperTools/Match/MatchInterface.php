<?php
/**
 * Zend Developer Tools for Zend Framework (http://framework.zend.com/)
 *
 * @link       http://github.com/zendframework/ZendDeveloperTools for the canonical source repository
 * @copyright  Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendDeveloperTools\Match;

use ZendDeveloperTools\ProfilerEvent;

interface MatchInterface
{
    /**
     * The (case-insensitive) name of the matcher.
     *
     * @return string
     */
    public function getName();
    
    /**
     * Compose an Event
     *
     * @param  ProfilerEvent $event
     * @return void
     */
    public function setEvent(ProfilerEvent $event);
    
    /**
     * Retrieve the composed event
     *
     * @return ProfilerEvent
     */
    public function getEvent();

    /**
     * Matches the pattern against data.
     *
     * @param  mixed   $pattern
     * @return boolean
     */
    public function matches($pattern);
}
