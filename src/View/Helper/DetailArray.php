<?php
/**
 * @see       https://github.com/zendframework/zend-developer-tools for the canonical source repository
 * @copyright Copyright (c) 2011-2018 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-developer-tools/blob/master/LICENSE.md New BSD License
 */

namespace ZendDeveloperTools\View\Helper;

use Zend\View\Helper\AbstractHelper;

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
        $r   = [];

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
