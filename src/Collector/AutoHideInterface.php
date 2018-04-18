<?php
/**
 * @see       https://github.com/zendframework/zend-developer-tools for the canonical source repository
 * @copyright Copyright (c) 2011-2018 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-developer-tools/blob/master/LICENSE.md New BSD License
 */

namespace ZendDeveloperTools\Collector;

/**
 * Auto hide Interface provides the ability for collectors, to specify that
 * they can be hidden.
 */
interface AutoHideInterface
{
    /**
     * Returns true if the collector can be hidden, because it is empty.
     *
     * @return bool
     */
    public function canHide();
}
