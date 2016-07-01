<?php

namespace ZendDeveloperToolsTest\Exception;

use PHPUnit_Framework_TestCase;
use ZendDeveloperTools\Exception\SerializableException;

class SerializableExceptionTest extends PHPUnit_Framework_TestCase
{
    public function testExceptionAndThrowable()
    {
        try {
            $a = 1 / 0;
        } catch (\Throwable $exception) {
            $serializable = new SerializableException($exception);
            $this->assertEquals('Division by zero', $serializable->getMessage());
        } catch (\Exception $exception) {
            $serializable = new SerializableException($exception);
            $this->assertEquals('Division by zero', $serializable->getMessage());
        }
    }

    public function testStdClassCallNotExistMethod()
    {
        try {
            (new \stdClass)->iDoNotExist();
        } catch (\Throwable $exception) {
            $serializable = new SerializableException($exception);
            $this->assertEquals('Call to undefined method stdClass::iDoNotExist()', $serializable->getMessage());
        } catch (\Exception $exception) {
            $serializable = new SerializableException($exception);
            $this->assertEquals('Call to undefined method stdClass::iDoNotExist()', $serializable->getMessage());
        }
    }
}
