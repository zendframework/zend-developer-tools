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
use ZendDeveloperTools\Matcher\LateMatchingInterface;
use ZendDeveloperTools\Exception\MatcherException;

/**
 * @category   Zend
 * @package    ZendDeveloperTools
 * @subpackage Listener
 */
class MatcherListener implements ListenerAggregateInterface
{
    /**
     * @var boolean
     */
    protected $matchEarly;

    /**
     * @var \Zend\Stdlib\CallbackHandler[]
     */
    protected $listeners = array();

    /**
     * Constructor.
     *
     * Sets the matching mode, which determines to which event the matcher
     * subscribes.
     *
     * @param boolean $matchEarly
     */
    public function __construct($matchEarly = true)
    {
        $this->matchEarly = (boolean) $matchEarly;

    }

    /**
     * @inheritdoc
     */
    public function attach(EventManagerInterface $events)
    {
        if ($this->matchEarly) {
            $this->listeners[] = $events->attach(ProfilerEvent::EVENT_BOOTSTRAP, array($this, 'match'), 10000);
        } else {
            $this->listeners[] = $events->attach(ProfilerEvent::EVENT_COLLECT, array($this, 'match'), 9500);
        }

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
     * Starts the matching process
     *
     * Iterates over the defined matchers and tries to match the defined
     * pattern. If a matcher does not match, the profiler will be disabled
     * and further execution of the event and the iteration will be
     * interrupted.
     *
     * @param  ProfilerEvent $event
     * @return boolean|null
     */
    public function match(ProfilerEvent $event)
    {
        $profiler    = $event->getProfiler();
        $application = $event->getApplication();
        $mvcEvent    = $application->getEvent();
        $options     = $profiler->getOptions();
        $matcher     = $options->getMatcher();
        $matchers    = $this->createMatchStack($options->getMatchers(), $profiler->getServiceManager());

        foreach ($matcher as $matcherName => $pattern) {
            if (isset($matchers[$matcherName])) {
                $matcherObj = $matchers[$matcherName];
                $matches    = $matcherObject->match($pattern, $mvcEvent);

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
                if (!is_string($matcherName)) {
                    throw new MatcherException(sprintf(
                        '%s::getName must return a string, %s given.',
                        get_class($matcher),
                        gettype($priority)
                    ));
                }

                if ($this->matchEarly) {
                    if ($matcher instanceof LateMatchingInterface) {
                        throw new MatcherException(sprintf(
                            '%s does not support early matching.',
                            get_class($matcher)
                        ));
                    }
                }

                $stack[$matcherName] = $matcher;
            } else {
                throw new MatcherException(sprintf('Unable to fetch or create an instance for %s.', $serviceName));
            }
        }

        return $stack;
    }
}