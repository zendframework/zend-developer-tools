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

use Zend\Mvc\MvcEvent;

/**
 * @category   Zend
 * @package    ZendDeveloperTools
 * @subpackage Matcher
 */
interface MatcherInterface
{
    /**
     * The matcher name.
     *
     * @return string
     */
    public function getName();

    /**
     * Tries to match against the pattern.
     *
     * @param  mixed    $pattern
     * @param  MvcEvent $event
     * @return boolean
     */
    public function match($pattern, MvcEvent $event);
}