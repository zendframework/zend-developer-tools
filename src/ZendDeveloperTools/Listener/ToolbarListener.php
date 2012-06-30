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
 * @subpackage EventListener
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendDeveloperTools\Listener;

use Zend\View\Model\ViewModel;
use Zend\Stdlib\ResponseInterface;
use ZendDeveloperTools\ProfilerEvent;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Developer Toolbar Listener
 *
 * @category   Zend
 * @package    ZendDeveloperTools
 * @subpackage EventListener
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
     * @var array
     */
    protected $listeners = array();

    /**
     * @param ServiceLocatorInterface $serviceLocator
     */
    public function __construct(ServiceLocatorInterface $serviceLocator)
    {
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

        $this->injectToolbar($response, $event);
    }

    /**
     * Tries to injects the toolbar into the view. The toolbar is only injected in well
     * formed HTML by repleacing the closing body tag, leaving ESI untouched.
     *
     * @param ResponseInterface $response
     * @param ProfilerEvent     $event
     */
    public function injectToolbar(ResponseInterface $response, ProfilerEvent $event)
    {
        // todo: support different toolbar positions, e.g. top.

        $renderer = $this->serviceLocator->get('ViewRenderer');
        $resolver = $this->serviceLocator->get('ViewTemplateMapResolver');

        $resolver->add(
            'zend-developer-tools/toolbar',
            __DIR__ . '/../../../views/zend-developer-tools/toolbar.phtml'
        );

        $toolbarView = new ViewModel(array('report' => $event->getReport()));
        $toolbarView->setTemplate('zend-developer-tools/toolbar');
        $toolbar     = $renderer->render($toolbarView);
        $toolbar     = str_replace("\n", '', $toolbar);
        $injected    = str_ireplace('</body>', $toolbar . "\n</body>", $response->getBody(), $count);
        if ($count > 1) {
            // todo: re-render toolbar with warning or use preg_replace with limit 1?
        }

        $response->setContent($injected);
    }
}