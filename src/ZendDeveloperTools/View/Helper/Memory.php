<?php
/**
 * ZendDeveloperTools
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @copyright  Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendDeveloperTools\View\Helper;

use Zend\View\Helper\AbstractHelper;

/**
 * @copyright  Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Memory extends AbstractHelper
{
    /**
     * Returns the formatted time.
     *
     * @param  integer $size
     * @param  integer $precision Only used for MegaBytes
     * @return string
     */
    public function __invoke($size, $precision = 2)
    {
        if ($size < 1024) {
            return sprintf('%d B', $size);
        } elseif (($size / 1024) < 1024) {
            return sprintf('%.0f Kb', $size / 1024);
        } else {
            return sprintf('%.' . $precision . 'f Mb', $size / 1024 / 1024);
        }
    }
}
