<?php
/**
 * @see       https://github.com/zendframework/zend-developer-tools for the canonical source repository
 * @copyright Copyright (c) 2011-2018 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-developer-tools/blob/master/LICENSE.md New BSD License
 */

namespace ZendDeveloperToolsTest;

use PHPUnit\Framework\TestCase;
use ZendDeveloperTools\Options;
use ZendDeveloperTools\ReportInterface;

class OptionsTest extends TestCase
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
