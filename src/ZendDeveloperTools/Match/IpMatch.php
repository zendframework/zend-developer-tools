<?php
/**
 * Zend Developer Tools for Zend Framework (http://framework.zend.com/)
 *
 * @link       http://github.com/zendframework/ZendDeveloperTools for the canonical source repository
 * @copyright  Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendDeveloperTools\Match;

class IpMatch extends AbstractMatch
{
    /**
     * The (case-insensitive) name of the matcher.
     *
     * @return string
     */
    public function getName()
    {
        return 'Ip';
    }
    
    /**
     * Matches the pattern against data.
     *
     * @param  mixed   $pattern
     * @return boolean
     */
    public function matches($pattern)
    {
        $ip = $this->getEvent()->getApplication()->getRequest()->getServer()->get('REMOTE_ADDR');
        
        if (is_string($pattern)) {
            return $pattern === $ip;
        } elseif (is_array($pattern)) {
            $match = false;
            foreach ($pattern as $address) {
                if ($address === $ip) {
                    $match = true;
                }
            }
            
            return $match;
        }
        
        return false;
    }
}