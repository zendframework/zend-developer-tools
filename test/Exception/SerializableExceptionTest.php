<?php
/**
 * @link      http://github.com/zendframework/zend-developer-tools for the canonical source repository
 * @copyright Copyright (c) 2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendDeveloperToolsTest\Exception;

use Exception;
use PHPUnit\Framework\TestCase;
use stdClass;
use Throwable;
use ZendDeveloperTools\Exception\SerializableException;

class SerializableExceptionTest extends TestCase
{
    public function testSerializableExceptionUsesPreviousExceptionMessage()
    {
        $original = new Exception('foo');
        $serializable = new SerializableException($original);
        $this->assertEquals($original->getMessage(), $serializable->getMessage());
    }

    /**
     * @requires PHP 7
     */
    public function testSerializableExceptionReportsCallToUndefinedMethod()
    {
        try {
            (new stdClass)->iDoNotExist();
        } catch (Throwable $exception) {
            $serializable = new SerializableException($exception);
            $this->assertEquals('Call to undefined method stdClass::iDoNotExist()', $serializable->getMessage());
        }
    }
}
