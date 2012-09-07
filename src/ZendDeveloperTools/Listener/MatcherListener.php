<?php
/**
 * Zend Developer Tools for Zend Framework (http://framework.zend.com/)
 *
 * @link       http://github.com/zendframework/ZendDeveloperTools for the canonical source repository
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd New BSD License
 * @package    ZendDeveloperTools
 * @subpackage Listener
 */

namespace ZendDeveloperTools\Listener;

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use ZendDeveloperTools\Profiler\ProfilerEvent;
use ZendDeveloperTools\Matcher\MatcherInterface;
use ZendDeveloperTools\Exception\MatcherException;

/**
 * @category   Zend
 * @package    ZendDeveloperTools
 * @subpackage Listener
 */
class MatcherListener implements ListenerAggregateInterface
{
    /**
     * @var \Zend\Stdlib\CallbackHandler[]
     */
    protected $listeners = array();

    /**
     * @inheritdoc
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(ProfilerEvent::EVENT_BOOTSTRAP, array($this, 'onBootstrap'), 10000);
    }

    /**
     * @inheritdoc
     */
    public function detach(EventManagerInterface $events)
    {
        foreach ($this->listeners as $index => $listener) {
            if ($events->detach($listener)) {
                unset($this->listeners[$index]);
            }
        }
    }

    /**
     * ProfilerEvent::EVENT_BOOTSTRAP callback
     *
     * @param  ProfilerEvent $event
     * @return boolean|null
     */
    public function onBootstrap(ProfilerEvent $event)
    {
        $profiler = $event->getProfiler();
        $options  = $profiler->getOptions();
        $matcher  = $options->getMatcher();
        $matchers = $this->createMatchStack($options->getMatchers(), $profiler->getServiceManager());

        foreach ($matcher as $matcherName => $match) {
            if (isset($matchers[$matcherName])) {
                $matcherObj = $matchers[$matcherName];
                $matches    = $matcherObject->match($match);

                if ($matches === false) {
                    $profiler->disable();
                    $event->stopPropagation();

                    return;
                }
            } else {
                throw new MatcherException(sprintf('Unregistered matcher called (%s).', $matcherName));
            }
        }
    }

    /**
     * Creates a stack based on the defined match options
     *
     * @param  array          $matchers
     * @param  ServiceManager $serviceManager
     * @return PriorityQueue
     */
    protected function createMatchStack(array $matchers, ServiceManager $serviceManager)
    {
        $stack = array();

        foreach ($matchers as $serviceName) {
            if ($serviceManager->has($serviceName)) {
                $matcher = $serviceManager->get($serviceName);

                if (!$matcher instanceof MatcherInterface) {
                    throw new MatcherException(sprintf(
                        'A matcher (%s) must implement ZendDeveloperTools\Matcher\MatcherInterface.',
                        $serviceName
                    ));
                }

                $matcherName = $matcher->getName();
                if (is_string($matcherName)) {
                    throw new MatcherException(sprintf(
                        '%s::getName must return a string, %s given.',
                        get_class($collector),
                        gettype($priority)
                    ));
                }

                $stack[$matcherName] = $matcher;
            } else {
                throw new MatcherException(sprintf('Unable to fetch or create an instance for %s.', $serviceName));
            }
        }

        return $stack;
    }
}