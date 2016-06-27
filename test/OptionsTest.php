<?php
namespace ZendDeveloperToolsTest;

use PHPUnit_Framework_TestCase;
use ZendDeveloperTools\Options;
use ZendDeveloperTools\ReportInterface;

class OptionsTest extends PHPUnit_Framework_TestCase
{
    public function testStatusOfDefaultConfiguration()
    {
        $dist = require __DIR__ . '/../config/zenddevelopertools.local.php.dist';
        $reportMock = $this->prophesize(ReportInterface::class)->reveal();
        $options = new Options($dist['zenddevelopertools'], $reportMock);
        $this->assertTrue($options->isEnabled());
        $this->assertTrue($options->isToolbarEnabled());
    }
}
