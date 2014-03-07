<?php
/**
 * Zend Developer Tools for Zend Framework (http://framework.zend.com/)
 *
 * @link http://github.com/zendframework/ZendDeveloperTools for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ZendDeveloperTools\Listener;

use ZendDeveloperTools\Options;
use ZendDeveloperTools\Profiler;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\EventManager\SharedListenerAggregateInterface;
use Zend\EventManager\Event;
use Zend\Debug\Debug;
use Zend\EventManager\SharedEventManagerInterface;

/**
 * Listens to defined events to allow event-level collection of statistics.
 *
 * @author Mark Garrett <mark@moderndeveloperllc.com>
 * @since 0.0.3
 */
class EventListener implements SharedListenerAggregateInterface
{

    /**
     *
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator;

    /**
     *
     * @var Options
     */
    protected $options;

    /**
     *
     * @var array
     */
    protected $listeners = array();

    /**
     * Constructor.
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @param Options $options
     */
    public function __construct(ServiceLocatorInterface $serviceLocator, Options $options)
    {
        $this->options = $options;
        $this->serviceLocator = $serviceLocator;
    }

    /**
     * @inheritdoc
     */
    public function attachShared(SharedEventManagerInterface $events)
    {
        $identifiers = array_values($this->options->getEventIdentifiers());
        $this->listeners[] = $events->attach(
            $identifiers,
            '*',
            array($this,'onCollectEvent'),
            Profiler::PRIORITY_EVENT_COLLECTOR
        );
    }

    /**
     * @inheritdoc
     */
    public function detachShared(SharedEventManagerInterface $events)
    {
        foreach ($this->listeners as $index => $listener) {
            if ($events->detach($listener)) {
                unset($this->listeners[$index]);
            }
        }
    }

    /**
     * Callback to process events
     *
     * @param Event $event
     * @throws ServiceNotFoundException
     */
    public function onCollectEvent(Event $event)
    {
        $eventContext = $this->provideEventContext($event);
        $strict = $this->options->isStrict();
        $collectors = $this->options->getEventCollectors();
        $report = $this->serviceLocator->get('ZendDeveloperTools\Report');

        foreach ($collectors as $name => $collector) {
            if ($this->serviceLocator->has($collector)) {
                $this->serviceLocator->get($collector)->collectEvent('application', $eventContext);
            } else {
                $error = sprintf('Unable to fetch or create an instance for %s.', $collector);
                if ($strict === true) {
                    throw new ServiceNotFoundException($error);
                } else {
                    $report->addError($error);
                }
            }
        }
    }

    /**
     * Build the event context array for use with event-level collectors.
     *
     * @param Event $event
     * @return string
     */
    private function provideEventContext(Event $event)
    {
        $context = array();
        $backtrace = debug_backtrace();
        $context['name'] = $event->getName();
        $context['file'] = basename(dirname($backtrace[4]['file'])) . '/' . basename($backtrace[4]['file']);
        $context['line'] = $backtrace[4]['line'];
        $context['target'] = (is_object($event->getTarget())) ? get_class($event->getTarget()) : $event->getTarget();

        return $context;
    }
}
