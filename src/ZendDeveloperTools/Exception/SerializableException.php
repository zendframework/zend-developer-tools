<?php
/**
 * Zend Developer Tools for Zend Framework (http://framework.zend.com/)
 *
 * @link       http://github.com/zendframework/ZendDeveloperTools for the canonical source repository
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd New BSD License
 * @package    ZendDeveloperTools
 * @subpackage Exception
 */

namespace ZendDeveloperTools\Exception;

/**
 * @category   Zend
 * @package    ZendDeveloperTools
 * @subpackage Exception
 */
class SerializableException implements \Serializable
{
    /**
     * Exception Data
     *
     * @var array
     */
    protected $data;

    /**
     * Saves the exception data in an array.
     *
     * @param \Exception $exception
     */
    public function __construct(\Exception $exception)
    {
        $this->data = array(
            'code'     => $exception->getCode(),
            'file'     => $exception->getFile(),
            'line'     => $exception->getLine(),
            'class'    => get_class($exception),
            'message'  => $exception->getMessage(),
            'previous' => $exception->getPrevious() ? new self($exception->getPrevious()) : null,
            'trace'    => $this->filterTrace(
                $exception->getTrace(),
                $exception->getFile(),
                $exception->getLine()
            ),
        );
    }

    /**
     * @return integer|string
     */
    public function getCode()
    {
        return $this->data['code'];
    }

    /**
     * @return string
     */
    public function getFile()
    {
        return $this->data['file'];
    }

    /**
     * @return integer
     */
    public function getLine()
    {
        return $this->data['line'];
    }

    /**
     * @return array
     */
    public function getTrace()
    {
        return $this->data['trace'];
    }

    /**
     * @return string
     */
    public function getTraceAsString()
    {
        return implode("\n", $this->data['trace']);
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->data['message'];
    }

    /**
     * @return self|null
     */
    public function getPrevious()
    {
        return $this->data['previous'];
    }

    /**
     * This function uses code coming from Symfony 2.
     *
     * @copyright Copyright (c) Fabien Potencier <fabien@symfony.com> (http://symfony.com/)
     * @license   http://symfony.com/doc/current/contributing/code/license.html  MIT license
     *
     * @param  array   $trace
     * @param  string  $file
     * @param  integer $line
     * @return array
     */
    protected function filterTrace($trace, $file, $line)
    {
        $filteredTrace = array();

        $filteredTrace[] = array(
            'namespace'   => '',
            'short_class' => '',
            'class'       => '',
            'type'        => '',
            'function'    => '',
            'file'        => $file,
            'line'        => $line,
            'args'        => array(),
        );

        foreach ($trace as $entry) {
            $class = '';
            $namespace = '';

            if (isset($entry['class'])) {
                $parts = explode('\\', $entry['class']);
                $class = array_pop($parts);
                $namespace = implode('\\', $parts);
            }

            $filteredTrace[] = array(
                'namespace'   => $namespace,
                'short_class' => $class,
                'class'       => isset($entry['class']) ? $entry['class'] : '',
                'type'        => isset($entry['type']) ? $entry['type'] : '',
                'function'    => $entry['function'],
                'file'        => isset($entry['file']) ? $entry['file'] : null,
                'line'        => isset($entry['line']) ? $entry['line'] : null,
                'args'        => isset($entry['args']) ? $this->filterArgs($entry['args']) : array(),
            );
        }

        return $filteredTrace;
    }

    /**
     * This function uses code coming from Symfony 2.
     *
     * @copyright Copyright (c) Fabien Potencier <fabien@symfony.com> (http://symfony.com/)
     * @license   http://symfony.com/doc/current/contributing/code/license.html  MIT license
     *
     * @param  array   $args
     * @param  integer $level
     * @return array
     */
    protected function filterArgs($args, $level = 0)
    {
        $result = array();

        foreach ($args as $key => $value) {
            if (is_object($value)) {
                $result[$key] = array('object', get_class($value));
            } elseif (is_array($value)) {
                if ($level > 10) {
                    $result[$key] = array('array', '*DEEP NESTED ARRAY*');
                } else {
                    $result[$key] = array('array', $this->filterArgs($value, ++$level));
                }
            } elseif (null === $value) {
                $result[$key] = array('null', null);
            } elseif (is_bool($value)) {
                $result[$key] = array('boolean', $value);
            } elseif (is_resource($value)) {
                $result[$key] = array('resource', get_resource_type($value));
            } else {
                $result[$key] = array('string', (string) $value);
            }
        }

        return $result;
    }

    /**
     * @see \Serializable
     */
    public function serialize()
    {
        return serialize($this->data);
    }

    /**
     * @see \Serializable
     */
    public function unserialize($data)
    {
        $this->data = unserialize($data);
    }
}