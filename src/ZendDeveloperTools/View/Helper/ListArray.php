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
class ListArray extends AbstractHelper
{
    /**
     * Formats Array structure in a HTML list structure
     *
     * @param array $config
     * @return string
     */
    public function __invoke(array $config)
    {
        $result = "<ul>";

        foreach ($config as $key => $value) {
            if (is_array($value)) {
                $result .= "$key =>" . $this($value);
                continue;
            }

            $result .= "<li>$key => " . htmlentities($value) . "</li>";
        }

        $result .= "</ul>";

        return $result;
    }
}
