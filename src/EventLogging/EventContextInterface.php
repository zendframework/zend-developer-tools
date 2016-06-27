<?php
/**
 * @link      http://github.com/zendframework/ZendDeveloperTools for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendDeveloperTools\EventLogging;

use Zend\EventManager\EventInterface;

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
    public function setEvent(EventInterface $event);

    /**
     * Collector Priority.
     *
     * @return Event
     */
    public function getEvent();
}
