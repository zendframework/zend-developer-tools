<?php
/**
 * Zend Developer Tools for Zend Framework (http://framework.zend.com/)
 *
 * @link       http://github.com/zendframework/ZendDeveloperTools for the canonical source repository
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd New BSD License
 * @package    ZendDeveloperTools
 * @subpackage Bootstrap
 */

namespace ZendDeveloperTools;

use Zend\EventManager\EventManagerInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use ZendDeveloperTools\Listener\DynamicIdentifierInterface;

/**
 * @category   Zend
 * @package    ZendDeveloperTools
 * @subpackage Bootstrap
 */
class Bootstrap
{
    /**
     * @var Options
     */
    protected $options;

    /**
     * @var ReportInterface
     */
    protected $report;

    /**
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator;

    /**
     * @var EventManagerInterface
     */
    protected $eventManager;

    /**
     * @var \Zend\ServiceManager\SharedEventManagerInterface
     */
    protected $sharedEventManager;

    /**
     * Constructor.
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @param EventManagerInterface   $eventManager
     * @param Options                 $options
     * @param ReportInterface         $report
     */
    public function __construct(
        ServiceLocatorInterface $serviceLocator,
        EventManagerInterface $eventManager,
        Options $options,
        ReportInterface $report
    ) {
        $this->report             = $report;
        $this->options            = $options;
        $this->eventManager       = $eventManager;
        $this->serviceLocator     = $serviceLocator;
        $this->sharedEventManager = $eventManager->getSharedManager();
    }

    /**
     * Registers listeners if enabled.
     *
     * @return self
     */
    public function init()
    {
        if ($this->options->isEnabled()) {
            if ($this->options->canFlushEarly()) {
                $this->eventManager->attachAggregate($this->serviceLocator->get('ZDT_FlushListener'));
            }

            if ($this->options->isStrict() && $this->report->hasErrors()) {
                throw new Exception\InvalidOptionException(implode(' ', $this->report->getErrors()));
            }

            $this->eventManager->attachAggregate($this->serviceLocator->get('ZDT_ProfileListener'));

            $this->registerVerbose()
                 ->registerToolbar();

            if ($this->options->isStrict() && $this->report->hasErrors()) {
                throw new Exception\ProfilerException(implode(' ', $this->report->getErrors()));
            }
        }

        return $this;
    }

    /**
     * Registers verbose listeners if enabled.
     *
     * @return self
     */
    protected function registerVerbose()
    {
        if ($this->options->isVerbose()) {
            foreach ($this->options->getVerboseListeners() as $id => $listeners) {
                foreach ($listeners as $service => $mode) {
                    if ($mode === true) {
                        if (!$this->serviceLocator->has($service)) {
                            $this->report->addError(sprintf(
                                'Unable to fetch or create an instance for %s.',
                                $service
                            ));

                            continue;
                        }

                        $listener = $this->serviceLocator->get($service);

                        if ($listener instanceof DynamicIdentifierInterface) {
                            $listener->setIdentifier($id);
                        }

                        $this->sharedEventManager->attach($id, $listener, null);
                    }
                }
            }
        }

        return $this;
    }

    /**
     * Registers toolbar listeners if enabled.
     *
     * @return self
     */
    protected function registerToolbar()
    {
        if ($this->options->isToolbarEnabled()) {
            $this->sharedEventManager->attach('profiler', $this->serviceLocator->get('ZDT_ToolbarListener'), null);
        }

        return $this;
    }
}