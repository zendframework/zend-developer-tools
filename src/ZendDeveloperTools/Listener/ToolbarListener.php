<?php
/**
 * Zend Developer Tools for Zend Framework (http://framework.zend.com/)
 *
 * @link       http://github.com/zendframework/ZendDeveloperTools for the canonical source repository
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd New BSD License
 * @package    ZendDeveloperTools
 * @subpackage Listener
 */

namespace ZendDeveloperTools\Listener;

use Zend\View\Model\ViewModel;
use Zend\View\Exception\RuntimeException as ViewRuntimeException;
use ZendDeveloperTools\Options;
use ZendDeveloperTools\Profiler;
use ZendDeveloperTools\ProfilerEvent;
use ZendDeveloperTools\Collector\AutoHideInterface;
use ZendDeveloperTools\Exception\InvalidOptionException;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Developer Toolbar Listener
 *
 * @category   Zend
 * @package    ZendDeveloperTools
 * @subpackage Listener
 */
class ToolbarListener implements ListenerAggregateInterface
{
    /**
     * @var object
     */
    protected $renderer;

    /**
     * @var Options
     */
    protected $options;

    /**
     * @var array
     */
    protected $listeners = array();

    /**
     * Constructor.
     *
     * @param object  $viewRenderer
     * @param Options $options
     */
    public function __construct($viewRenderer, Options $options)
    {
        $this->options  = $options;
        $this->renderer = $viewRenderer;
    }

    /**
     * @inheritdoc
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(
            ProfilerEvent::EVENT_COLLECTED,
            array($this, 'onCollected'),
            Profiler::PRIORITY_TOOBAR
        );
    }

    /**
     * @inheritdoc
     */
    public function detach(EventManagerInterface $events)
    {
        foreach ($this->listeners as $index => $listener) {
            if ($events->detach($listener)) {
                unset($this->listeners[$index]);
            }
        }
    }

    /**
     * ProfilerEvent::EVENT_COLLECTED event callback.
     *
     * @param ProfilerEvent $event
     */
    public function onCollected(ProfilerEvent $event)
    {
        $application = $event->getApplication();
        $request     = $application->getRequest();
        $response    = $application->getResponse();

        if ($request->isXmlHttpRequest()) {
            return;
        }

        // todo: X-Debug-Token logic?
        // todo: redirect logic

        $this->injectToolbar($event);
    }

    /**
     * Tries to injects the toolbar into the view. The toolbar is only injected in well
     * formed HTML by repleacing the closing body tag, leaving ESI untouched.
     *
     * @param ProfilerEvent $event
     */
    protected function injectToolbar(ProfilerEvent $event)
    {
        $entries     = $this->renderEntries($event);
        $response    = $event->getApplication()->getResponse();;

        $toolbarView = new ViewModel(array('entries' => $entries));
        $toolbarView->setTemplate('zend-developer-tools/toolbar/toolbar');
        $toolbar     = $this->renderer->render($toolbarView);
        $toolbar     = str_replace("\n", '', $toolbar);

        $toolbarCss  = new ViewModel(array(
            'position' => $this->options->getToolbarPosition(),
        ));
        $toolbarCss->setTemplate('zend-developer-tools/toolbar/style');
        $style       = $this->renderer->render($toolbarCss);
        $style       = str_replace(array("\n", '  '), '', $style);

        $injected    = preg_replace('/<\/body>/i', $toolbar . "\n</body>", $response->getBody(), 1);
        $injected    = preg_replace('/<\/head>/i', $style . "\n</head>", $injected, 1);

        $response->setContent($injected);
    }

    /**
     * Renders all toolbar entries.
     *
     * @param ProfilerEvent $event
     */
    protected function renderEntries(ProfilerEvent $event)
    {
        $entries    = array();
        $report     = $event->getReport();

        $zfEntry    = new ViewModel();
        $zfEntry->setTemplate('zend-developer-tools/toolbar/zendframework');
        $entries[]  = $this->renderer->render($zfEntry);

        $errors     = array();
        $collectors = $this->options->getCollectors();
        $templates  = $this->options->getToolbarEntries();

        foreach ($templates as $name => $template) {
            if (isset($collectors[$name])) {
                try {
                    $collectorInstance = $report->getCollector($name);

                    if (
                        $this->options->getToolbarAutoHide()
                        && $collectorInstance instanceof AutoHideInterface
                        && $collectorInstance->canHide()
                    ) {
                        continue;
                    }

                    $collector = new ViewModel(array(
                        'collector' => $collectorInstance,
                    ));
                    $collector->setTemplate($template);
                    $entries[] = $this->renderer->render($collector);
                } catch (ViewRuntimeException $e) {
                    $errors[$name] = $template;
                }
            }
        }

        if (!empty($errors) || $report->hasErrors()) {
            $tmp = array();
            foreach ($errors as $name => $template) {
                $cur   = sprintf('Unable to render toolbar template %s (%s).', $name, $template);
                $tmp[] = $cur;
                $report->addError($cur);
            }

            if ($this->options->isStrict()) {
                throw new InvalidOptionException(implode(' ', $tmp));
            }
        }

        if ($report->hasErrors()) {
            $errorTpl  = new ViewModel(array('errors' => $report->getErrors()));
            $errorTpl->setTemplate('zend-developer-tools/toolbar/error');
            $entries[] = $this->renderer->render($errorTpl);
        }

        return $entries;
    }
}