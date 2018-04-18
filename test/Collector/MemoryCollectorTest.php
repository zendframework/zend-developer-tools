<?php
/**
 * @see       https://github.com/zendframework/zend-developer-tools for the canonical source repository
 * @copyright Copyright (c) 2011-2018 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-developer-tools/blob/master/LICENSE.md New BSD License
 */

namespace ZendDeveloperToolsTest\Collector;

use PHPUnit\Framework\TestCase;
use ZendDeveloperTools\Collector\MemoryCollector;
use Zend\Mvc;

class MemoryCollectorTest extends TestCase
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
