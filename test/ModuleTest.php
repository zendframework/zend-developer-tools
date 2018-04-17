<?php
/**
 * @see       https://github.com/zendframework/ZendDeveloperTools for the canonical source repository
 * @copyright Copyright (c) 2011-2018 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/ZendDeveloperTools/blob/master/LICENSE.md New BSD License
 */

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
