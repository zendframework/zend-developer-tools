<?php
namespace ZendDeveloperTools\EventLogging;

use Zend\EventManager\EventInterface;

/**
 * Class to provide context information for a passed event.
 *
 * @author Mark Garrett <mark.garrett@allcarepharmacy.com>
 */
class EventContextProvider implements EventContextInterface
{
    /**
     * @var EventInterface
     */
    protected $event;

    /**
     *
     * @var array
     */
    private $debugBacktrace = array();

    /**
     * @param EventInterface $event (Optional) The event to provide context to. The event must be set either here or
     * with {@see setEvent()} before any other methods can be used.
     */
    public function __construct(EventInterface $event = null)
    {
        if ($event) {
            $this->setEvent($event);
        }
    }

    /**
     * @see \ZendDeveloperTools\EventLogging\EventContextInterface::setEvent()
     * @param  Event $event The event to add context to.
     * @return null
     */
    public function setEvent(EventInterface $event)
    {
        $this->event = $event;
    }

    /**
     * @see \ZendDeveloperTools\EventLogging\EventContextInterface::getEvent()
     * @return Event
     */
    public function getEvent()
    {
        if (!$this->event) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects an event to have been set.',
                __METHOD__
            ));
        }

        return $this->event;
    }

    /**
     * Returns either the class name of the target, or the target string
     *
     * @return string
     */
    public function getEventTarget()
    {
        $event = $this->getEvent();
        return (is_object($event->getTarget())) ? get_class($event->getTarget()) : (string) $event->getTarget();
    }

    /**
     * Returns the debug_backtrace() for this object with two levels removed so that array starts where this
     * class method was called.
     *
     * @return string
     */
    private function getDebugBacktrace()
    {
        if (!$this->debugBacktrace) {
            //Remove the levels this method introduces
            $trace = debug_backtrace();
            $this->debugBacktrace = array_splice($trace, 2);
        }

        return $this->debugBacktrace;
    }

    /**
     * Returns the filename and parent directory of the file from which the event was triggered.
     *
     * @return string
     */
    public function getEventTriggerFile()
    {
        $backtrace = $this->getDebugBacktrace();
        if (file_exists($backtrace[4]['file'])) {
            return basename(dirname($backtrace[4]['file'])) . '/' . basename($backtrace[4]['file']);
        }

        return '';
    }

    /**
     * Returns the line number of the file from which the event was triggered.
     *
     * @return integer
     */
    public function getEventTriggerLine()
    {
        $backtrace = $this->getDebugBacktrace();
        if (isset($backtrace[4]['line'])) {
            return $backtrace[4]['line'];
        }

        return '';
    }
}
