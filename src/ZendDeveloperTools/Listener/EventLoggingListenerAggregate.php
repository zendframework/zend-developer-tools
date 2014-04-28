<?php
/**
 * Zend Developer Tools for Zend Framework (http://framework.zend.com/)
 *
 * @link http://github.com/zendframework/ZendDeveloperTools for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendDeveloperTools\Listener;

use Zend\EventManager\Event;
use Zend\EventManager\SharedEventManagerInterface;
use Zend\EventManager\SharedListenerAggregateInterface;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\ServiceManager\ServiceLocatorInterface;
use ZendDeveloperTools\Options;
use ZendDeveloperTools\Profiler;
use ZendDeveloperTools\ReportInterface;

/**
 * Listens to defined events to allow event-level collection of statistics.
 *
 * @author Mark Garrett <mark@moderndeveloperllc.com>
 * @since 0.0.3
 */
class EventLoggingListenerAggregate implements SharedListenerAggregateInterface
{
    /**
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator;

    /**
     *
     * @var Options
     */
    protected $options;

    /**
     * @var ReportInterface
     */
    protected $report;

    /**
     * Constructor.
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @param Options $options
     * @param ReportInterface $report
     */
    public function __construct(ServiceLocatorInterface $serviceLocator, Options $options, ReportInterface $report)
    {
        $this->options        = $options;
        $this->serviceLocator = $serviceLocator;
        $this->report         = $report;
    }

    /**
     * @inheritdoc
     */
    public function attachShared(SharedEventManagerInterface $events)
    {
        $events->attach(
            array_values($this->options->getEventIdentifiers()),
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
        // can't be detached
    }

    /**
     * Callback to process events
     *
     * @param Event $event
     *
     * @return bool
     *
     * @throws ServiceNotFoundException
     */
    public function onCollectEvent(Event $event)
    {
        $strict     = $this->options->isStrict();
        $collectors = $this->options->getEventCollectors();

        foreach ($collectors as $collector) {
            if ($this->serviceLocator->has($collector)) {
                /* @var $currentCollector \ZendDeveloperTools\Collector\EventCollectorInterface */
                $currentCollector = $this->serviceLocator->get($collector);

                $currentCollector->collectEvent('application', $event);
            } else {
                $error = sprintf('Unable to fetch or create an instance for %s.', $collector);

                if ($strict === true) {
                    throw new ServiceNotFoundException($error);
                } else {
                    $this->report->addError($error);
                }
            }
        }

        return true; // @TODO workaround, to be removed when https://github.com/zendframework/zf2/pull/6147 is fixed
    }
}
