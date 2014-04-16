<?php
/**
 * Zend Developer Tools for Zend Framework (http://framework.zend.com/)
 *
 * @link       http://github.com/zendframework/ZendDeveloperTools for the canonical source repository
 * @copyright  Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendDeveloperTools\FirePhp;

/**
 * The group options provider interface
 */
interface GroupOptionsProviderInterface
{
    const COLOR_BLACK    = '#000000';
    const COLOR_RED      = '#FF0000';
    const COLOR_WHITE    = '#FFFFFF';
    const COLOR_ZF_GREEN = '#80DC09';

    /**
     * Return the group options
     *
     * @return array $groupOptions
     */
    public function getGroupOptions();
}