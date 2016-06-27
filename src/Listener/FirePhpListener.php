<?php
/**
 * Zend Developer Tools for Zend Framework (http://framework.zend.com/)
 *
 * @link       http://github.com/zendframework/ZendDeveloperTools for the canonical source repository
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendDeveloperTools\Listener;

use ZendDeveloperTools\Profiler;
use ZendDeveloperTools\ProfilerEvent;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\EventManager\ListenerAggregateTrait;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * FirePHP Listener
 *
 */
class FirePhpListener implements ListenerAggregateInterface
{
    use ListenerAggregateTrait;

    /**
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator;

    /**
     * Constructor.
     *
     * @param ServiceLocatorInterface $serviceLocator
     */
    public function __construct(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    /**
     * {@inheritdoc}
     */
    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $this->listeners[] = $events->attach(
            ProfilerEvent::EVENT_COLLECTED,
            [$this, 'onCollected'],
            Profiler::PRIORITY_FIREPHP
        );
    }

    /**
     * ProfilerEvent::EVENT_COLLECTED event callback.
     *
     * @param ProfilerEvent $event
     */
    public function onCollected(ProfilerEvent $event)
    {
    }
}
