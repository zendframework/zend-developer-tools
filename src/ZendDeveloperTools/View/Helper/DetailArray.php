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
class DetailArray extends AbstractHelper
{
    /**
     * Renders a detail entry for an array.
     *
     * @param  string  $label Label name
     * @param  array   $details Value array (list)
     * @param  bool $redundant Marks this detail as redundant.
     * @return string
     */
    public function __invoke($label, array $details, $redundant = false)
    {
        $r = array();

        $r[] = '<span class="zdt-toolbar-info';
        $r[] = ($redundant) ? ' zdt-toolbar-info-redundant' : '';
        $r[] = '">';

        $r[] = '<span class="zdt-detail-label">';
        $r[] = $label;
        $r[] = '</span>';


        $extraCss = '';
        $newLine  = false;

        foreach ($details as $entry) {
            if ($newLine === true) {
                $r[] = '</span><span class="zdt-toolbar-info';
                $r[] = ($redundant) ? ' zdt-toolbar-info-redundant' : '';
                $r[] = '">';
            }

            $r[] = sprintf('<span class="zdt-detail-value%s">%s</span>', $extraCss, $entry);

            $newLine  = true;
            $extraCss = ' zdt-detail-extra-value';
        }

        $r[] = '</span>';

        return implode('', $r);
    }
}