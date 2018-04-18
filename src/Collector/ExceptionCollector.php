<?php
/**
 * @see       https://github.com/zendframework/zend-developer-tools for the canonical source repository
 * @copyright Copyright (c) 2011-2018 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-developer-tools/blob/master/LICENSE.md New BSD License
 */

namespace ZendDeveloperTools\Collector;

use ZendDeveloperTools\Exception\SerializableException;
use Zend\Mvc\Application;
use Zend\Mvc\MvcEvent;

/**
 * Exception Data Collector.
 */
class ExceptionCollector extends AbstractCollector
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'exception';
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        return 100;
    }

    /**
     * {@inheritdoc}
     */
    public function collect(MvcEvent $mvcEvent)
    {
        if ($mvcEvent->getError() === Application::ERROR_EXCEPTION) {
            $this->data = [
                'exception' => new SerializableException($mvcEvent->getParam('exception'))
            ];
        }
    }

    /**
     * Checks if an exception was thrown during the runtime.
     *
     * @return bool
     */
    public function hasException()
    {
        return isset($this->data['exception']);
    }

    /**
     * Checks if an exception was re-thrown during the runtime.
     *
     * @return bool
     */
    public function hasPreviousException()
    {
        return (isset($this->data['exception']) && $this->data['exception']->getPrevious() !== null);
    }

    /**
     * Returns the exception.
     *
     * @return SerializableException
     */
    public function getException()
    {
        return $this->data['exception'];
    }

    /**
     * Returns the previous exception.
     *
     * @return SerializableException
     */
    public function getPreviousException()
    {
        return $this->data['exception']->getPrevious();
    }

    /**
     * Returns the exception message.
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->data['exception']->getMessage();
    }

    /**
     * Returns the previous exception message.
     *
     * @return string
     */
    public function getPreviousMessage()
    {
        return $this->data['exception']->getPrevious()->getMessage();
    }

    /**
     * Returns the file in which the exception occurred.
     *
     * @return string
     */
    public function getFile()
    {
        return $this->data['exception']->getFile();
    }

    /**
     * Returns the file in which the previous exception occurred.
     *
     * @return string
     */
    public function getPreviousFile()
    {
        return $this->data['exception']->getPrevious()->getFile();
    }

    /**
     * Returns the exception code.
     *
     * @return integer
     */
    public function getCode()
    {
        return $this->data['exception']->getCode();
    }

    /**
     * Returns the previous exception code.
     *
     * @return integer
     */
    public function getPreviousCode()
    {
        return $this->data['exception']->getPrevious()->getCode();
    }

    /**
     * Returns the exception trace.
     *
     * @return array
     */
    public function getTrace()
    {
        return $this->data['exception']->getTrace();
    }

    /**
     * Returns the previous exception trace.
     *
     * @return array
     */
    public function getPreviousTrace()
    {
        return $this->data['exception']->getPrevious()->getTrace();
    }
}
