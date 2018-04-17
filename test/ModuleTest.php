<?php

namespace ZendDeveloperToolsTest;

use PHPUnit\Framework\TestCase;
use ZendDeveloperTools\Module;

class ModuleTest extends TestCase
{
    public function testGetConfig()
    {
        $module = new Module();
        $config = $module->getConfig();

        $this->assertInternalType('array', $config);
    }

    public function testConfigSerialization()
    {
        $module = new Module();
        $config = $module->getConfig();

        $this->assertSame($config, unserialize(serialize($config)));
    }
}
