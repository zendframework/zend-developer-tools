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
use ZendDeveloperTools\Exception\MatcherException;

/**
 * Route name matcher
 *
 * The route name matcher supports matching nested routes, by the Mvc Router
 * seperated by a slash.
 *
 * This matcher only supports late matching mode.
 *
 * @category   Zend
 * @package    ZendDeveloperTools
 * @subpackage Matcher
 */
class RouteNameMatcher implements MatcherInterface, LateMatchingInterface
{
    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'route_name';
    }

    /**
     * @inheritdoc
     */
    public function match($pattern, MvcEvent $event)
    {
        $name = $event->getRouteMatch()->getMatchedRouteName();

        if ($name === null) {
            return false;
        }

        if (is_string($pattern)) {
            $index = 0;
            $names = explode('/', $name);
            $parts = explode('/', $pattern);

            foreach ($parts as $part) {
                if ($part !== $names[$index]) {
                    return false;
                }

                $index++;
            }
        } else {
            throw new MatcherException(sprintf(
                'Unsupported pattern type. Expects string, %s given.',
                gettype($pattern)
            ));
        }

        return true;
    }
}