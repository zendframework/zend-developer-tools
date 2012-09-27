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

use Zend\Version\Version;
use Zend\View\Model\ViewModel;
use Zend\View\Exception\RuntimeException;
use ZendDeveloperTools\Options;
use ZendDeveloperTools\Profiler;
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
     * Time to live for the version cache in seconds.
     *
     * @var integer
     */
    const VERSION_CACHE_TTL = 3600;

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
            Profiler::PRIORITY_TOOLBAR
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

        if ($request->isXmlHttpRequest()) {
            return;
        }

        $response = $application->getResponse();
        $headers = $response->getHeaders();
        if ($headers->has('Content-Type')
            && false !== strpos($headers->get('Content-Type')->getFieldValue(), 'html')
        ) {
            return;
        }

        // todo: X-Debug-Token logic?
        // todo: redirect logic

        $this->injectToolbar($event);
    }

    /**
     * Tries to injects the toolbar into the view. The toolbar is only injected in well
     * formed HTML by replacing the closing body tag, leaving ESI untouched.
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
     * @param  ProfilerEvent $event
     * @return array
     * @throws InvalidOptionException
     */
    protected function renderEntries(ProfilerEvent $event)
    {
        $entries = array();
        $report  = $event->getReport();

        list($isLatest, $latest) = $this->getLatestVersion(Version::VERSION);

        $zfEntry = new ViewModel(array(
            'zf_version'  => Version::VERSION,
            'is_latest'   => $isLatest,
            'latest'      => $latest,
            'php_version' => phpversion(),
            'has_intl'    => extension_loaded('intl'),
        ));
        $zfEntry->setTemplate('zend-developer-tools/toolbar/zendframework');
        $entries[] = $this->renderer->render($zfEntry);

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
                    $entries[] = $this->renderer->render($collector);
                } catch (RuntimeException $e) {
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

    /**
     * Wrapper for Zend\Version::getLatest with caching functionality, so that
     * ZendDeveloperTools won't act as a "DDoS bot-network".
     *
     * @param  string $currentVersion
     * @return array
     */
    protected function getLatestVersion($currentVersion)
    {
        if (!$this->options->isVersionCheckEnabled()) {
            return array(true, '');
        }

        $cacheDir = $this->options->getCacheDir();

        // exit early if the cache dir doesn't exist,
        // to prevent hitting the GitHub API for every request.
        if (!is_dir($cacheDir)) {
            return array(true, '');
        }

        if (file_exists($cacheDir . '/ZDT_ZF_Version.cache')) {
            $cache = file_get_contents($cacheDir . '/ZDT_ZF_Version.cache');
            $cache = explode('|', $cache);

            if ($cache[0] + self::VERSION_CACHE_TTL > time()) {
                // the cache file was written before the version was upgraded.
                if ($currentVersion === $cache[2] || $cache[2] === 'N/A') {
                    return array(true, '');
                }

                return array(
                    ($cache[1] === 'yes') ? true : false,
                    $cache[2]
                );
            }
        }

        $isLatest = Version::isLatest();
        $latest   = Version::getLatest();

        file_put_contents(
            $cacheDir . '/ZDT_ZF_Version.cache',
            sprintf(
                '%d|%s|%s',
                time(),
                ($isLatest) ? 'yes' : 'no',
                ($latest === null) ? 'N/A' : $latest
            )
        );

        return array($isLatest, $latest);
    }
}
