<?php
/**
 * @see       https://github.com/zendframework/zend-developer-tools for the canonical source repository
 * @copyright Copyright (c) 2011-2018 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-developer-tools/blob/master/LICENSE.md New BSD License
 */

namespace ZendDeveloperTools;

use BjyProfiler\Db\Adapter\ProfilingAdapter;
use Zend\EventManager\EventInterface;
use Zend\ModuleManager\Feature\BootstrapListenerInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\InitProviderInterface;
use Zend\ModuleManager\Feature\ServiceProviderInterface;
use Zend\ModuleManager\Feature\ViewHelperProviderInterface;
use Zend\ModuleManager\ModuleEvent;
use Zend\ModuleManager\ModuleManagerInterface;

class Module implements
    InitProviderInterface,
    ConfigProviderInterface,
    ServiceProviderInterface,
    BootstrapListenerInterface,
    ViewHelperProviderInterface
{
    /**
     * Initialize workflow
     *
     * @param  ModuleManagerInterface $manager
     */
    public function init(ModuleManagerInterface $manager)
    {
        defined('REQUEST_MICROTIME') || define('REQUEST_MICROTIME', microtime(true));

        if (PHP_SAPI === 'cli') {
            return;
        }

        $eventManager = $manager->getEventManager();
        $eventManager->attach(
            ModuleEvent::EVENT_LOAD_MODULES_POST,
            [$this, 'onLoadModulesPost'],
            -1100
        );
    }

    /**
     * loadModulesPost callback
     *
     * @param  $event
     */
    public function onLoadModulesPost($event)
    {
        $eventManager  = $event->getTarget()->getEventManager();
        $configuration = $event->getConfigListener()->getMergedConfig(false);

        if (isset($configuration['zenddevelopertools']['profiler']['enabled'])
            && $configuration['zenddevelopertools']['profiler']['enabled'] === true
        ) {
            $eventManager->trigger(ProfilerEvent::EVENT_PROFILER_INIT, $event);
        }
    }

    /**
     * Zend\Mvc\MvcEvent::EVENT_BOOTSTRAP event callback
     *
     * @param  EventInterface $event
     * @throws Exception\InvalidOptionException
     * @throws Exception\ProfilerException
     */
    public function onBootstrap(EventInterface $event)
    {
        if (PHP_SAPI === 'cli') {
            return;
        }

        $app = $event->getApplication();
        $sm  = $app->getServiceManager();

        $options = $sm->get('ZendDeveloperTools\Config');

        if (! $options->isToolbarEnabled()) {
            return;
        }

        $em  = $app->getEventManager();
        $report = $sm->get(Report::class);

        if ($options->canFlushEarly()) {
            $flushListener = $sm->get('ZendDeveloperTools\FlushListener');
            $flushListener->attach($em);
        }

        if ($options->isStrict() && $report->hasErrors()) {
            throw new Exception\InvalidOptionException(implode(' ', $report->getErrors()));
        }

        if ($options->eventCollectionEnabled()) {
            $sem = $em->getSharedManager();
            $eventLoggingListener = $sm->get(Listener\EventLoggingListenerAggregate::class);
            $eventLoggingListener->attachShared($sem);
        }

        $profilerListener = $sm->get(Listener\ProfilerListener::class);
        $profilerListener->attach($em);

        if ($options->isToolbarEnabled()) {
            $toolbarListener = $sm->get(Listener\ToolbarListener::class);
            $toolbarListener->attach($em);
        }

        if ($options->isStrict() && $report->hasErrors()) {
            throw new Exception\ProfilerException(implode(' ', $report->getErrors()));
        }
    }

    /**
     * @inheritdoc
     */
    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    public function getViewHelperConfig()
    {
        return [
            'invokables' => [
                'ZendDeveloperToolsTime'        => View\Helper\Time::class,
                'ZendDeveloperToolsMemory'      => View\Helper\Memory::class,
                'ZendDeveloperToolsDetailArray' => View\Helper\DetailArray::class,
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function getServiceConfig()
    {
        return [
            'aliases' => [
                'ZendDeveloperTools\ReportInterface' => Report::class,
            ],
            'invokables' => [
                Report::class                           => Report::class,
                'ZendDeveloperTools\ExceptionCollector' => Collector\ExceptionCollector::class,
                'ZendDeveloperTools\RequestCollector'   => Collector\RequestCollector::class,
                'ZendDeveloperTools\ConfigCollector'    => Collector\ConfigCollector::class,
                'ZendDeveloperTools\MailCollector'      => Collector\MailCollector::class,
                'ZendDeveloperTools\MemoryCollector'    => Collector\MemoryCollector::class,
                'ZendDeveloperTools\TimeCollector'      => Collector\TimeCollector::class,
                'ZendDeveloperTools\FlushListener'      => Listener\FlushListener::class,
            ],
            'factories' => [
                Profiler::class => function ($sm) {
                    $a = new Profiler($sm->get(Report::class));
                    $a->setEvent($sm->get('ZendDeveloperTools\Event'));
                    return $a;
                },
                'ZendDeveloperTools\Config' => function ($sm) {
                    $config = $sm->get('Configuration');
                    $config = isset($config['zenddevelopertools']) ? $config['zenddevelopertools'] : null;

                    return new Options($config, $sm->get(Report::class));
                },
                'ZendDeveloperTools\Event' => function ($sm) {
                    $event = new ProfilerEvent();
                    $event->setReport($sm->get(Report::class));
                    $event->setApplication($sm->get('Application'));

                    return $event;
                },
                'ZendDeveloperTools\StorageListener' => function ($sm) {
                    return new Listener\StorageListener($sm);
                },
                Listener\ToolbarListener::class => function ($sm) {
                    return new Listener\ToolbarListener(
                        $sm->get('ViewRenderer'),
                        $sm->get('ZendDeveloperTools\Config')
                    );
                },
                Listener\ProfilerListener::class => function ($sm) {
                    return new Listener\ProfilerListener($sm, $sm->get('ZendDeveloperTools\Config'));
                },
                Listener\EventLoggingListenerAggregate::class => function ($sm) {
                    $config = $sm->get('ZendDeveloperTools\Config');

                    return new Listener\EventLoggingListenerAggregate(
                        array_map([$sm, 'get'], $config->getEventCollectors()),
                        $config->getEventIdentifiers()
                    );
                },
                'ZendDeveloperTools\DbCollector' => function ($sm) {
                    $p  = false;
                    $db = new Collector\DbCollector();

                    if ($sm->has('Zend\Db\Adapter\Adapter') && isset($sm->get('config')['db'])) {
                        $adapter = $sm->get('Zend\Db\Adapter\Adapter');
                        if ($adapter instanceof ProfilingAdapter) {
                            $p = true;
                            $db->setProfiler($adapter->getProfiler());
                        }
                    }

                    if (! $p && $sm->has('Zend\Db\Adapter\AdapterInterface') && isset($sm->get('config')['db'])) {
                        $adapter = $sm->get('Zend\Db\Adapter\AdapterInterface');
                        if ($adapter instanceof ProfilingAdapter) {
                            $p = true;
                            $db->setProfiler($adapter->getProfiler());
                        }
                    }

                    if (! $p && $sm->has('Zend\Db\Adapter\ProfilingAdapter')) {
                        $adapter = $sm->get('Zend\Db\Adapter\ProfilingAdapter');
                        if ($adapter instanceof ProfilingAdapter) {
                            $db->setProfiler($adapter->getProfiler());
                        }
                    }

                    return $db;
                },
            ],
        ];
    }
}
