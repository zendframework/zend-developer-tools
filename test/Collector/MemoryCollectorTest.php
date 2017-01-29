<?php
namespace ZendDeveloperToolsTest\Collector;

use ZendDeveloperTools\Collector\MemoryCollector;
use Zend\Mvc;

class MemoryCollectorTest extends \PHPUnit_Framework_TestCase
{
    public function testCollector()
    {
        $collector = new MemoryCollector();

        $mvcEvent = $this->getMockBuilder(Mvc\MvcEvent::class)
            ->getMock();

        $collector->collect($mvcEvent);
        $this->assertInternalType("integer", $collector->getMemory());
    }
}
