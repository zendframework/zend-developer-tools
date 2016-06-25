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
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendDeveloperTools\View\Helper;

use Zend\View\Helper\AbstractHelper;

/**
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Time extends AbstractHelper
{
    /**
     * Returns the formatted time.
     *
     * @param  integer|float $time
     * @param  integer       $precision Will only be used for seconds.
     * @return string
     */
    public function __invoke($time, $precision = 2)
    {
        if ($time === 0) {
            return '0 s';
        }

        if ($time >= 1) {
            return sprintf('%.' . $precision . 'f s', $time);
        } elseif ($time * 1000 >= 1) {
            return sprintf('%.' . $precision . 'f ms', $time * 1000);
        } else {
            return sprintf('%.' . $precision . 'f µs', $time * 1000000);
        }
    }
}
