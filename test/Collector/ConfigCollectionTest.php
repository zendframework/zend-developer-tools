<?php
namespace ZendDeveloperToolsTest\Collector;

use ZendDeveloperTools\Collector\ConfigCollector;

class ConfigCollectorTest extends \PHPUnit_Framework_TestCase
{
    public function testCollect()
    {
        $collector = new ConfigCollector();

        $application = $this->getMockBuilder("Zend\Mvc\Application")
            ->disableOriginalConstructor()
            ->getMock();
        $serviceManager = $this->getMockBuilder("Zend\ServiceManager\ServiceManager")
            ->disableOriginalConstructor()
            ->getMock();

        $application
            ->expects($this->once())
            ->method("getServiceManager")
            ->willReturn($serviceManager);
        $mvcEvent = $this->getMockBuilder("Zend\Mvc\MvcEvent")
            ->getMock();

        $mvcEvent->method("getApplication")->willReturn($application);

        $collector->collect($mvcEvent);
    }
}
