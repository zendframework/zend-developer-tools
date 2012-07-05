<?php
/**
 * ZendDeveloperTools
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    ZendDeveloperTools
 * @subpackage Exception
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendDeveloperTools\Exception;

/**
 * @category   Zend
 * @package    ZendDeveloperTools
 * @subpackage Exception
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
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
        // todo: trace filtering
        // see:  http://fabien.potencier.org/article/9/php-serialization-stack-traces-and-exceptions

        $this->data = array(
            'code'     => $exception->getCode(),
            'file'     => $exception->getFile(),
            'line'     => $exception->getLine(),
            'trace'    => $exception->getTrace(),
            'message'  => $exception->getMessage(),
            'previous' => $exception->getPrevious() ? null : new self($exception->getPrevious()),
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