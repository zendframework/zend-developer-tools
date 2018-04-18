<?php
/**
 * @see       https://github.com/zendframework/zend-developer-tools for the canonical source repository
 * @copyright Copyright (c) 2011-2018 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-developer-tools/blob/master/LICENSE.md New BSD License
 */

namespace ZendDeveloperTools\View\Helper;

use Zend\View\Helper\AbstractHelper;

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
        }

        if ($time * 1000 >= 1) {
            return sprintf('%.' . $precision . 'f ms', $time * 1000);
        }

        return sprintf('%.' . $precision . 'f µs', $time * 1000000);
    }
}
