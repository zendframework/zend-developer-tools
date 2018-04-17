<?php
/**
 * @see       https://github.com/zendframework/ZendDeveloperTools for the canonical source repository
 * @copyright Copyright (c) 2011-2018 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/ZendDeveloperTools/blob/master/LICENSE.md New BSD License
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
