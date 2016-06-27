<?php
namespace ZendDeveloperToolsTest;

use PHPUnit_Framework_TestCase;
use ZendDeveloperTools\Options;

class OptionsTest extends PHPUnit_Framework_TestCase
{
    public function testStatusOfDefaultConfiguration()
    {
        $dist = require __DIR__."/../config/zenddevelopertools.local.php.dist";
        $reportMock = $this->getMock("ZendDeveloperTools\\ReportInterface");
        $options = new Options($dist['zenddevelopertools'], $reportMock);
        $this->assertTrue($options->isEnabled());
        $this->assertTrue($options->isToolbarEnabled());
    }
}
