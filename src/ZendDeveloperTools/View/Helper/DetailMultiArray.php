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
 * @category   Zend
 * @package    ZendDeveloperTools_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendDeveloperTools\View\Helper;

use Zend\View\Helper\AbstractHelper;

/**
 * @category   Zend
 * @package    ZendDeveloperTools_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class DetailMultiArray extends AbstractHelper
{
    /**
     * Renders a detail entry for a multidemensional array.
     *
     * @param  string  $label Label name
     * @param  array   $details Value array (list)
     * @param  boolean $redundant Marks this detail as redundant.
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

        $r[] = '<span class="zdt-toolbar-info ddt-toolbar-info zdt-toolbar-info-maxheight">';
        $r[] = $this->_renderArray($details);
        $r[] = '</span>';

        $r[] = '</span>';

        return implode('', $r);
    }


    /**
     * Generate the HTML for a detail array
     *
     * @param array $data
     * @param int $depth
     * @return string
     */
    protected function _renderArray(array $data, $depth=0)
    {
        $r = array();

        foreach ($data as $key => $record) {
            $extraClass = ($depth == 0) ? '' : '  zdt-toolbar-info-ul-hidden';
            $r[] = '<ul class="zdt-toolbar-info-ul' . $extraClass . '">';

            if (is_array($record)) {
                $r[] = '<li>';
                if (count ($record)) {
                    $r[] = '<a href="#" onclick="' . $this->_addOnclick() . '">' . $key . '</a>';
                    $r[] = $this->_renderArray($record, ($depth + 1));
                } else {
                    $r[] = $key . ': {}';
                }
                $r[] = '</li>';
            } else {
                $r[] = '<li>';
                if (!is_int($key)) {
                    $r[] = $key . ': <br />';
                }
                $r[] =  $record;
                $r[] = '</li>';
            }

            $r[] = '</ul>';
        }

        return implode('', $r);
    }

    /**
     * Generate an onclick statement for the parent labels.
     *
     * @return string
     */
    protected function _addOnclick()
    {
        $r = array();
        $r[] = 'javascript:';
        $r[] = 'var element = this;';
        $r[] = 'while (element.nextSibling) {';
        $r[] = 'element = element.nextSibling;';
        $r[] = 'console.log(element.tagName);';
        $r[] = 'if (element.tagName.toLowerCase() ==  \'ul\') {';
        $r[] = 'if (element.style.display == \'block\') {';
        $r[] = 'element.style.display = \'none\';';
        $r[] = '} else { ';
        $r[] = 'element.style.display = \'block\';';
        $r[] = '}';
        $r[] = '}';
        $r[] = '}';
        return implode('', $r);
    }
}