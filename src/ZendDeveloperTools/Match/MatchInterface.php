<?php
/**
 * Zend Developer Tools for Zend Framework (http://framework.zend.com/)
 *
 * @link       http://github.com/zendframework/ZendDeveloperTools for the canonical source repository
 * @copyright  Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendDeveloperTools;

interface MatchInterface
{
    /**
     * The (case-insensitive) name of the matcher.
     *
     * @return string
     */
    public function getName();

    /**
     * Matches the pattern against data.
     *
     * @param  string $pattern
     * @return mixed
     */
    public function matches($pattern);
}
