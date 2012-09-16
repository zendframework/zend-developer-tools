<?php
/**
 * Zend Developer Tools for Zend Framework (http://framework.zend.com/)
 *
 * @link       http://github.com/zendframework/ZendDeveloperTools for the canonical source repository
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd New BSD License
 * @package    ZendDeveloperTools
 * @subpackage Collector
 */

namespace ZendDeveloperTools\Collector;

use Zend\Mvc\MvcEvent;

/**
 * Database (Zend\Db) Data Collector.
 *
 * @category   Zend
 * @package    ZendDeveloperTools
 * @subpackage Collector
 */
class PhpCollector extends AbstractCollector
{
    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'php';
    }

    /**
     * @inheritdoc
     */
    public function getPriority()
    {
        return 20;
    }

    /**
     * @inheritdoc
     */
    /**
     * @inheritdoc
     */
    public function collect(MvcEvent $mvcEvent)
    {
        return;
    }

    /**
     * Get all PHP extensions loaded
     *
     * @return array
     */
    public function getExtensions()
    {
        //Look at $data, already loaded?
        if (!is_array($this->data['extensions'])) {
            $extensions = get_loaded_extensions();
            foreach ($extensions as $extension) {
                //Replace 'true' with some function to get details about the
                //extension. Format output in php toolbar template.
                $this->data['extensions'][$extension] = true;
            }
        }
        return $this->data['extensions'];
    }

    /**
     * Return the version of PHP running
     *
     * @return string
     */
    public function getVersion()
    {
        return PHP_VERSION;
    }
}