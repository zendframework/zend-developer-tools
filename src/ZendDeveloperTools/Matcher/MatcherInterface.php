<?php
/**
 * Zend Developer Tools for Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendDeveloperTools for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   ZendDeveloperTools
 */

namespace ZendDeveloperTools\Matcher;

/**
 * @category   Zend
 * @package    ZendDeveloperTools
 * @subpackage Matcher
 */
interface MatcherInterface
{
    /**
     * @return string
     */
    public function getName();

    /**
     * @param  mixed $pattern
     * @return boolean
     */
    public function match($pattern);
}