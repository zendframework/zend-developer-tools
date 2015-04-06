<?php
namespace ZendDeveloperToolsTest\Collector;

use ZendDeveloperTools\Collector\MemoryCollector;

class MemoryCollectorTest extends \PHPUnit_Framework_TestCase
{
    public function testCollector()
    {
        $collector = new MemoryCollector();

        $mvcEvent = $this->getMockBuilder("Zend\Mvc\MvcEvent")
            ->getMock();

        $collector->collect($mvcEvent);
        $this->assertInternalType("integer", $collector->getMemory());
    }
}
