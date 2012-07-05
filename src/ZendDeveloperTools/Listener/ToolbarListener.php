<?php
/**
 * ZendDeveloperTools
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    ZendDeveloperTools
 * @subpackage Listener
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendDeveloperTools\Listener;

use Zend\View\Model\ViewModel;
use Zend\View\Exception\RuntimeException;
use ZendDeveloperTools\Options;
use ZendDeveloperTools\ProfilerEvent;
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
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ToolbarListener implements ListenerAggregateInterface
{
    /**
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator;

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
     * @param ServiceLocatorInterface $serviceLocator
     * @param Options                 $options
     */
    public function __construct(ServiceLocatorInterface $serviceLocator, Options $options)
    {
        $this->options        = $options;
        $this->serviceLocator = $serviceLocator;
    }

    /**
     * @inheritdoc
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(ProfilerEvent::EVENT_COLLECTED, array($this, 'onCollected'), 2500);
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
        $renderer    = $this->serviceLocator->get('ViewRenderer');

        $toolbarView = new ViewModel(array('entries' => $entries));
        $toolbarView->setTemplate('zend-developer-tools/toolbar/toolbar');
        $toolbar     = $renderer->render($toolbarView);
        $toolbar     = str_replace("\n", '', $toolbar);

        $toolbarCss  = new ViewModel(array(
            'position' => $this->options->getToolbarPosition(),
        ));
        $toolbarCss->setTemplate('zend-developer-tools/toolbar/style');
        $style       = $renderer->render($toolbarCss);
        $style       = str_replace(array("\n", '    '), '', $style);

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
        $renderer   = $this->serviceLocator->get('ViewRenderer');

        $zfEntry    = new ViewModel(array('version' => \Zend\Version::VERSION));
        $zfEntry->setTemplate('zend-developer-tools/toolbar/zendframework');
        $entries[]  = $renderer->render($zfEntry);

        $errors     = array();
        $collectors = $this->options->getCollectors();
        $templates  = $this->options->getToolbarEntries();

        foreach ($templates as $name => $template) {
            if (isset($collectors[$name])) {
                try {
                    $collector = new ViewModel(array(
                        'report'    => $report,
                        'collector' => $report->getCollector($name),
                    ));
                    $collector->setTemplate($template);
                    $entries[] = $renderer->render($collector);
                } catch (RuntimeException $e) {
                    $errors[$name] = $template;
                }
            }
        }

        if (!empty($errors)) {
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
            $entries[] = $renderer->render($errorTpl);
        }

        return $entries;
    }
}