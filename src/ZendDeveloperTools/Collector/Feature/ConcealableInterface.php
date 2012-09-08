<?php
/**
 * Zend Developer Tools for Zend Framework (http://framework.zend.com/)
 *
 * @link       http://github.com/zendframework/ZendDeveloperTools for the canonical source repository
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd New BSD License
 * @package    ZendDeveloperTools
 * @subpackage Collector_Feature
 */

namespace ZendDeveloperTools\Collector\Feature;

/**
 * @category   Zend
 * @package    ZendDeveloperTools
 * @subpackage Collector_Feature
 */
interface ConcealableInterface
{
    /**
     * Determines wether or not the collector can be hidden, if it is empty.
     *
     * Note: This interface is only recognized by the toolbar.
     *
     * @return boolean
     */
    public function isConcealable();
}