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

    public function testCollectorObfuscateRiskyKeyValues()
    {
        $config = array(
            'username' => 'fooUser',
            'password' => 'password',
            'nested' => array(
                'dsn' => 'hostname:localhost;username:user;pass:password',
            ),
        );

        $collector = new ConfigCollector();

        $application = $this->getMockBuilder("Zend\Mvc\Application")
            ->disableOriginalConstructor()
            ->getMock();
        $serviceManager = $this->getMockBuilder("Zend\ServiceManager\ServiceManager")
            ->disableOriginalConstructor()
            ->getMock();

        $serviceManager
            ->expects($this->any())
            ->method('get')
            ->willReturn($config);

        $serviceManager
            ->expects($this->any())
            ->method('has')
            ->willReturn(true);

        $application
            ->expects($this->once())
            ->method("getServiceManager")
            ->willReturn($serviceManager);

        $mvcEvent = $this->getMockBuilder("Zend\Mvc\MvcEvent")
            ->getMock();

        $mvcEvent->method("getApplication")->willReturn($application);

        $collector->collect($mvcEvent);

        $returnConfig = $collector->getConfig();

        $replacedCount = substr_count(var_export($returnConfig, true), $collector::OBFUSCATE_STRING);
        $this->assertEquals(3, $replacedCount);
        $this->assertCount(count($config), $returnConfig);

        $this->assertEquals($collector::OBFUSCATE_STRING, $returnConfig['username']);
        $this->assertEquals($collector::OBFUSCATE_STRING, $returnConfig['password']);
        $this->assertEquals($collector::OBFUSCATE_STRING, $returnConfig['nested']['dsn']);

    }
}
