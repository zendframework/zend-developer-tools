<?php
/**
 * Zend Developer Tools for Zend Framework (http://framework.zend.com/)
 *
 * @link       http://github.com/zendframework/ZendDeveloperTools for the canonical source repository
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendDeveloperTools\Exception;

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
     * @param \Exception|\Throwable $exception
     */
    public function __construct($exception)
    {
        $this->data = [
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
        ];
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
        $filteredTrace = [];

        $filteredTrace[] = [
            'namespace'   => '',
            'short_class' => '',
            'class'       => '',
            'type'        => '',
            'function'    => '',
            'file'        => $file,
            'line'        => $line,
            'args'        => [],
        ];

        foreach ($trace as $entry) {
            $class = '';
            $namespace = '';

            if (isset($entry['class'])) {
                $parts = explode('\\', $entry['class']);
                $class = array_pop($parts);
                $namespace = implode('\\', $parts);
            }

            $filteredTrace[] = [
                'namespace'   => $namespace,
                'short_class' => $class,
                'class'       => isset($entry['class']) ? $entry['class'] : '',
                'type'        => isset($entry['type']) ? $entry['type'] : '',
                'function'    => $entry['function'],
                'file'        => isset($entry['file']) ? $entry['file'] : null,
                'line'        => isset($entry['line']) ? $entry['line'] : null,
                'args'        => isset($entry['args']) ? $this->filterArgs($entry['args']) : [],
            ];
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
        $result = [];

        foreach ($args as $key => $value) {
            if (is_object($value)) {
                $result[$key] = ['object', get_class($value)];
                continue;
            }

            if (is_array($value)) {
                if ($level > 10) {
                    $result[$key] = ['array', '*DEEP NESTED ARRAY*'];
                    continue;
                }
                $result[$key] = ['array', $this->filterArgs($value, ++$level)];
                continue;
            }

            if (null === $value) {
                $result[$key] = ['null', null];
                continue;
            }

            if (is_bool($value)) {
                $result[$key] = ['boolean', $value];
                continue;
            }

            if (is_resource($value)) {
                $result[$key] = ['resource', get_resource_type($value)];
                continue;
            }

            $result[$key] = ['string', (string) $value];
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
