<?php
namespace ZendDeveloperTools\EventLogging;

use Zend\EventManager\Event;

/**
 * Interface for classes that want to provide event context in the event-level collectors.
 *
 * @author Mark Garrett <mark.garrett@allcarepharmacy.com>
 */
interface EventContextInterface
{
    /**
     * Sets the event.
     *
     * @return null
     */
    public function setEvent(Event $event);

    /**
     * Collector Priority.
     *
     * @return Event
     */
    public function getEvent();
}
