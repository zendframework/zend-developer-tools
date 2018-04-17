<?php
namespace ZendDeveloperToolsTest\Collector;

use ZendDeveloperTools\Collector\ConfigCollector;
use Zend\Mvc;
use Zend\ServiceManager;

class ConfigCollectorTest extends \PHPUnit_Framework_TestCase
{
    public function testCollect()
    {
        $collector = new ConfigCollector();

        $application = $this->getMockBuilder(Mvc\Application::class)
            ->disableOriginalConstructor()
            ->getMock();
        $serviceManager = $this->getMockBuilder(ServiceManager\ServiceManager::class)
            ->disableOriginalConstructor()
            ->getMock();

        $application
            ->expects($this->once())
            ->method("getServiceManager")
            ->willReturn($serviceManager);
        $mvcEvent = $this->getMockBuilder(Mvc\MvcEvent::class)
            ->getMock();

        $mvcEvent->method("getApplication")->willReturn($application);

        $collector->collect($mvcEvent);
    }
}
