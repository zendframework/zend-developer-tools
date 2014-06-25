<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendDeveloperTools;

use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\ConfigInterface;

class MatchManager extends AbstractPluginManager
{
    /**
     * @var array
     */
    protected $aliases = array(
        'ip'    => 'ZendDeveloperTools\IpMatch',
    );
    
    /**
     * @var array
     */
    protected $invokableClasses = array(
        'ZendDeveloperTools\IpMatch'  => 'ZendDeveloperTools\Match\IpMatch',
    );
    
    /**
     * Constructor
     *
     * After invoking parent constructor, add an initializer to inject the
     * attached translator, if any, to the currently requested helper.
     *
     * @param  null|ConfigInterface $configuration
     */
    public function __construct(ConfigInterface $configuration = null)
    {
        parent::__construct($configuration);
        
        $this->addInitializer(array($this, 'injectProfilerEvent'));
    }
    
    /**
     * Inject ProfilerEvent
     *
     * @param  Plugin\PluginInterface $plugin
     * @return void
     */
    public function injectProfilerEvent($plugin)
    {
        $locator = $this->getServiceLocator();
        if ($locator && $locator->has('ZendDeveloperTools\Event')) {
            $plugin->setEvent($locator->get('ZendDeveloperTools\Event'));
        }
    }

    /**
     * Validate the plugin
     *
     * @param  Plugin\PluginInterface $plugin
     * @return void
     * @throws Exception\RuntimeException if invalid
     */
    public function validatePlugin($plugin)
    {
        if ($plugin instanceof Match\MatchInterface) {
            // we're okay
            return;
        }

        throw new \RuntimeException(sprintf(
            'Plugin of type %s is invalid; must implement %s\Form',
            (is_object($plugin) ? get_class($plugin) : gettype($plugin)),
            __NAMESPACE__
        ));
    }
}
