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
interface PriorityProviderInterface
{
    /**
     * @var integer
     */
    const PRIORITY_LOW = 25;

    /**
     * @var integer
     */
    const PRIORITY_MEDIUM = 50;

    /**
     * @var integer
     */
    const PRIORITY_HIGH = 75;

    /**
     * Detimines with which priority the collector should run.
     *
     * @return integer
     */
    public function getPriority();
}