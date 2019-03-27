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

    public function blacklistFlags()
    {
        yield 'null' => [null];
        yield 'false' => [false];
    }

    /**
     * @see https://framework.zend.com/security/advisory/ZF2019-01
     * @dataProvider blacklistFlags
     * @param null|bool $flagValue
     */
    public function testOnlyWhitelistedToolbarEntriesShouldBeEnabled($flagValue)
    {
        $reportMock     = $this->prophesize(ReportInterface::class)->reveal();
        $options        = new Options([], $reportMock);
        $toolbarOptions = [
            'enabled' => true,
            'entries' => [
                'request' => $flagValue,
                'time'    => true,
                'config'  => $flagValue,
            ],
        ];

        $options->setToolbar($toolbarOptions);

        $this->assertTrue($options->isToolbarEnabled());

        $entries = $options->getToolbarEntries();
        $this->assertArrayNotHasKey(
            'request',
            $entries,
            'Request key found in toolbar entries, and should not have been'
        );
        $this->assertArrayHasKey(
            'time',
            $entries,
            'Time key NOT found in toolbar entries, and should have been'
        );
        $this->assertArrayNotHasKey(
            'config',
            $entries,
            'Config key found in toolbar entries, and should not have been'
        );
    }
}
