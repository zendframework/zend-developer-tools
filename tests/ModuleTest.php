<?php

namespace ZendDeveloperToolsTest;
use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_Assert;
use PHPUnit_Framework_MockObject_MockObject;
use ZendDeveloperTools\Module;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;

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
