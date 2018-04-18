<?php
/**
 * @see       https://github.com/zendframework/zend-developer-tools for the canonical source repository
 * @copyright Copyright (c) 2011-2018 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-developer-tools/blob/master/LICENSE.md New BSD License
 */

namespace ZendDeveloperTools\Listener;

use ZendDeveloperTools\Options;
use ZendDeveloperTools\Profiler;
use ZendDeveloperTools\Report;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\EventManager\ListenerAggregateTrait;
use Zend\Mvc\MvcEvent;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Profiler Listener
 *
 * Listens to the MvcEvent::EVENT_FINISH event and starts collecting data.
 */
class ProfilerListener implements ListenerAggregateInterface
{
    use ListenerAggregateTrait;

    /**
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator;

    /**
     * @var Options
     */
    protected $options;

    /**
     * Constructor.
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @param Options                 $options
     */
    public function __construct(ServiceLocatorInterface $serviceLocator, Options $options)
    {
        $this->options        = $options;
        $this->serviceLocator = $serviceLocator;
    }

    /**
     * {@inheritdoc}
     */
    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $this->listeners[] = $events->attach(
            MvcEvent::EVENT_FINISH,
            [$this, 'onFinish'],
            Profiler::PRIORITY_PROFILER
        );
    }

    /**
     * MvcEvent::EVENT_FINISH event callback
     *
     * @param  MvcEvent $event
     * @throws ServiceNotFoundException
     */
    public function onFinish(MvcEvent $event)
    {
        $strict     = $this->options->isStrict();
        $collectors = $this->options->getCollectors();
        $report     = $this->serviceLocator->get(Report::class);
        $profiler   = $this->serviceLocator->get(Profiler::class);

        $profiler->setErrorMode($strict);

        foreach ($collectors as $name => $collector) {
            if ($this->serviceLocator->has($collector)) {
                $profiler->addCollector($this->serviceLocator->get($collector));
                continue;
            }

            $error = sprintf('Unable to fetch or create an instance for %s.', $collector);
            if ($strict === true) {
                throw new ServiceNotFoundException($error);
            }
            $report->addError($error);
        }

        $profiler->collect($event);
    }
}
