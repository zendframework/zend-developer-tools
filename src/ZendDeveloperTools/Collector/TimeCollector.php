<?php
/**
 * Zend Developer Tools for Zend Framework (http://framework.zend.com/)
 *
 * @link       http://github.com/zendframework/ZendDeveloperTools for the canonical source repository
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ZendDeveloperTools\Collector;

use Zend\Mvc\MvcEvent;
use Zend\EventManager\Event;
use ZendDeveloperTools\EventLogging\EventContextProvider;

/**
 * Time Data Collector.
 */
class TimeCollector extends AbstractCollector implements EventCollectorInterface
{
    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'time';
    }

    /**
     * @inheritdoc
     */
    public function getPriority()
    {
        return PHP_INT_MAX;
    }

    /**
     * @inheritdoc
     */
    public function collect(MvcEvent $mvcEvent)
    {
        if (PHP_VERSION_ID >= 50400) {
            $start = $mvcEvent->getRequest()->getServer()->get('REQUEST_TIME_FLOAT');
        } elseif (defined('REQUEST_MICROTIME')) {
            $start = REQUEST_MICROTIME;
        } else {
            $start = $mvcEvent->getRequest()->getServer()->get('REQUEST_TIME');
        }

        if (!isset($this->data)) {
            $this->data = array();
        }

        $this->data['start'] = $start;
        $this->data['end']   = microtime(true);
    }

    /**
     * Saves the current time in microseconds for a specific event.
     *
     * @param string $id
     * @param Event  $event
     */
    public function collectEvent($id, Event $event)
    {
        $contextProvider   = new EventContextProvider($event);
        $context['time']   = microtime(true);
        $context['name']   = $contextProvider->getEvent()->getName();
        $context['target'] = $contextProvider->getEventTarget();
        $context['file']   = $contextProvider->getEventTriggerFile();
        $context['line']   = $contextProvider->getEventTriggerLine();

        if (!isset($this->data['event'][$id])) {
            $this->data['event'][$id] = array();
        }

        $this->data['event'][$id][] = $context;
    }

    /**
     * Returns the total execution time.
     *
     * @return float
     */
    public function getStartTime()
    {
        return $this->data['start'];
    }

    /**
     * Returns the total execution time.
     *
     * @return float
     */
    public function getExecutionTime()
    {
        return $this->data['end'] - $this->data['start'];
    }

    /**
     * Event times collected?
     *
     * @return bool
     */
    public function hasEventTimes()
    {
        return isset($this->data['event']);
    }

    /**
     * Returns the detailed application execution time.
     *
     * @return array
     */
    public function getApplicationEventTimes()
    {
        $result = array();

        if (!isset($this->data['event']['application'])) {
            return $result;
        }

        $app = $this->data['event']['application'];

        $previous = null;
        while (list($index, $context) = each($app)) {
            $result[$index] = $context;
            $result[$index]['elapsed'] = ($previous)
                ? ($context['time'] - $previous['time'])
                : ($context['time'] - $this->data['start']);
            $previous = prev($app);
            next($app);
        }

        return $result;
    }
}
