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
 * The group label provider interface
 */
interface GroupLabelProviderInterface
{
    /**
     * Get the group label
     *
     * @return string $groupLabel
     */
    public function getGroupLabel();
}