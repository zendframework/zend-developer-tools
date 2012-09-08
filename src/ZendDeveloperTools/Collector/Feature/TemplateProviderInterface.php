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
interface TemplateProviderInterface
{
    /**
     * Returns an array containing the template names used for any rendering.
     *
     * Supported template types:
     *   - toolbar
     *   - browser
     *
     * Example:
     * <code>
     * array(
     *     'toolbar' => 'zend-developer-tools/toolbar/example',
     * )
     * </code>
     *
     * @return array
     */
    public function getTemplate();
}