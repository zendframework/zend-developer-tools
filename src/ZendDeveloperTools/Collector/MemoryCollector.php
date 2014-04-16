<?php
/**
 * Zend Developer Tools for Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendDeveloperTools for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ZendDeveloperTools\Collector;

use Zend\Mvc\MvcEvent;
use Zend\EventManager\Event;
use ZendDeveloperTools\EventLogging\EventContextProvider;

/**
 * Memory Data Collector.
 */
class MemoryCollector extends AbstractCollector implements EventCollectorInterface
{
    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'memory';
    }

    /**
     * @inheritdoc
     */
    public function getPriority()
    {
        return PHP_INT_MAX - 1;
    }

    /**
     * @inheritdoc
     */
    public function collect(MvcEvent $mvcEvent)
    {
        if (!isset($this->data)) {
            $this->data = array();
        }

        $this->data['memory'] = memory_get_peak_usage(true);
        $this->data['end'] = memory_get_usage(true);
    }

    /**
     * Saves the current memory usage.
     *
     * @param string $id
     * @param Event  $event
     */
    public function collectEvent($id, Event $event)
    {
        $contextProvider   = new EventContextProvider($event);
        $context['name']   = $contextProvider->getEvent()->getName();
        $context['target'] = $contextProvider->getEventTarget();
        $context['file']   = $contextProvider->getEventTriggerFile();
        $context['line']   = $contextProvider->getEventTriggerLine();
        $context['memory'] = memory_get_usage(true);

        if (!isset($this->data['event'][$id])) {
            $this->data['event'][$id] = array();
        }

        $this->data['event'][$id][] = $context;
    }

    /**
     * Returns the used Memory (peak)
     *
     * @return integer Memory
     */
    public function getMemory()
    {
        return $this->data['memory'];
    }

    /**
     * Event memory collected?
     *
     * @return integer Memory
     */
    public function hasEventMemory()
    {
        return isset($this->data['event']);
    }

    /**
     * Returns the detailed application memory.
     *
     * @return array
     */
    public function getApplicationEventMemory()
    {
        $result = array();

        if (!isset($this->data['event']['application'])) {
            return $result;
        }

        $app = $this->data['event']['application'];

        $previous = null;
        while (list ($name, $context) = each($app)) {
            $result[$name] = $context;
            $result[$name]['difference'] = ($previous)
                ? ($context['memory'] - $previous['memory'])
                : ($context['memory']);
            $previous = prev($app);
            next($app);
        }

        return $result;
    }
}
