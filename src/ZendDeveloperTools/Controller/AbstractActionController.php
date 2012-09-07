<?php
/**
 * Zend Developer Tools for Zend Framework (http://framework.zend.com/)
 *
 * @link       http://github.com/zendframework/ZendDeveloperTools for the canonical source repository
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd New BSD License
 * @package    ZendDeveloperTools
 * @subpackage Controller
 */

namespace ZendDeveloperTools\Controller;

use Zend\Mvc\Controlle\AbstractActionController as BaseController;

/**
 * @category   Zend
 * @package    ZendDeveloperTools
 * @subpackage Controller
 */
abstract class AbstractActionController extends BaseController
{
    /**
     * Execute the request, disables the profiler and checks if the request is
     * authorized. If the request is unauthorized, the notFoundAction will be
     * called and returned.
     *
     * @triggers access(ProfilerEvent)
     * @param    MvcEvent $mvcEvent
     * @return   mixed
     * @throws   Exception\DomainException
     */
    public function onDispatch(MvcEvent $mvcEvent)
    {
        $serviceLocator = $this->getServiceLocator();
        $profiler       = $services->get('ZendDeveloperTools\Profiler\Profiler');
        $eventManager   = $profiler->getEventManager();
        $profilerEvent  = $profiler->getEvent();

        $profiler->disable();

        /**
         * Have to check it twice for the -rare- case that somebody disabled
         * the access even before the event was triggered.
         */
        if (!$profilerEvent->isAccessible()) {
            $response = $this->notFoundAction();
            $mvcEvent->setResult($response);

            return $response;
        }

        $eventManager->trigger(ProfilerEvent::EVENT_ACCESS, $event);

        if (!$profilerEvent->isAccessible()) {
            $response = $this->notFoundAction();
            $mvcEvent->setResult($response);

            return $response;
        }

        return parent::onDispatch($mvcEvent);
    }
}