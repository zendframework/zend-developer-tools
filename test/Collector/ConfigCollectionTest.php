<?php
/**
 * @see       https://github.com/zendframework/ZendDeveloperTools for the canonical source repository
 * @copyright Copyright (c) 2011-2018 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/ZendDeveloperTools/blob/master/LICENSE.md New BSD License
 */

namespace ZendDeveloperToolsTest\Collector;

use PHPUnit\Framework\TestCase;
use ZendDeveloperTools\Collector\ConfigCollector;
use Zend\Mvc;
use Zend\ServiceManager;

class ConfigCollectorTest extends TestCase
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
