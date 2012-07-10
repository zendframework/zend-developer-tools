<?php
/**
 * Zend Developer Tools for Zend Framework (http://framework.zend.com/)
 *
 * @link       http://github.com/zendframework/ZendDeveloperTools for the canonical source repository
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd New BSD License
 * @package    ZendDeveloperTools
 * @subpackage Listener
 */

namespace ZendDeveloperTools\Listener;

/**
 * Dynamic Identifer
 *
 * @category   Zend
 * @package    ZendDeveloperTools
 * @subpackage Listener
 */
interface DynamicIdentifierInterface
{
    /**
     * Sets an Event Manager identifier
     *
     * @param string $id
     */
    public function setIdentifier($id);
}