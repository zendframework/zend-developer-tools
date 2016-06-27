<?php

namespace ZendDeveloperToolsTest;

use PHPUnit_Framework_TestCase;
use ZendDeveloperTools\Module;

class ModuleTest extends PHPUnit_Framework_TestCase
{
    public function testGetConfig()
    {
        $module = new Module();
        $config = $module->getConfig();

        $this->assertInternalType('array', $config);
        $this->assertSame($config, unserialize(serialize($config)));
    }
}
