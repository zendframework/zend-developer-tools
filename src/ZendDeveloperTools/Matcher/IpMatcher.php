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
 * IP matcher
 *
 * The IP matcher supports one or multiple IPs to match against, supplied as
 * string or array.
 *
 * This matcher can run in both, early and late, modes.
 *
 * @category   Zend
 * @package    ZendDeveloperTools
 * @subpackage Matcher
 */
class IpMatcher
{
    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'ip';
    }

    /**
     * @inheritdoc
     */
    public function match($pattern, MvcEvent $event)
    {
        $ip = $event->getRequest()->getServer()->get('REMOTE_ADDR');

        if (is_string($pattern)) {
            if ($ip !== $pattern) {
                return false;
            }
        } elseif (is_array($pattern)) {
            if (!in_array($ip, $pattern)) {
                return false;
            }
        } else {
            throw new MatcherException(sprintf(
                'Unsupported pattern type. Expects string or array, %s given.',
                gettype($pattern)
            ));
        }

        return true;
    }
}