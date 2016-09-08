<?php

namespace ZendDeveloperToolsTest\Exception;

use PHPUnit_Framework_TestCase;
use ZendDeveloperTools\Exception\SerializableException;

class SerializableExceptionTest extends PHPUnit_Framework_TestCase
{
    public function testException()
    {
        try {
            new \Exception('foo');
        } catch (\Exception $exception) {
            $serializable = new SerializableException($exception);
            $this->assertEquals('foo', $serializable->getMessage());
        }
    }

    /**
     * @requires PHP 7
     */
    public function testStdClassCallNotExistMethod()
    {
        try {
            (new \stdClass)->iDoNotExist();
        } catch (\Throwable $exception) {
            $serializable = new SerializableException($exception);
            $this->assertEquals('Call to undefined method stdClass::iDoNotExist()', $serializable->getMessage());
        }
    }
}
