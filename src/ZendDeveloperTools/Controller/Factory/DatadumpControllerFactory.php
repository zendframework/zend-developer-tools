<?php
/**
 * Zend Developer Tools for Zend Framework (http://framework.zend.com/)
 *
 * @link       http://github.com/zendframework/ZendDeveloperTools for the canonical source repository
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd New BSD License
 * @package    ZendDeveloperTools
 * @subpackage Controller\Factory
 */

namespace ZendDeveloperTools\Controller\Factory;

use \Zend\ServiceManager\FactoryInterface;
use \Zend\ServiceManager\ServiceLocatorInterface;
use \ZendDeveloperTools\Controller\DatadumpController;
use \ZendDeveloperTools\Exception\ParameterMissingException;

/**
 * @category   Zend
 * @package    ZendDeveloperTools
 * @subpackage Controller
 */
class DatadumpControllerFactory implements FactoryInterface
{

    /**
     * @param \Zend\ServiceManager\ServiceLocatorInterface $serviceLocator
     *
     * @return mixed|\ZendDeveloperTools\Controller\DatadumpController
     * @throws \ZendDeveloperTools\Exception\ParameterMissingException
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var \Zend\Http\PhpEnvironment\Request $request  */
        $request = $serviceLocator->getServiceLocator()->get('request');

        if (!$request->getPost('data')) {
            throw new ParameterMissingException('The data parameter is missing.');
        }

        $controller = new DatadumpController();
        return $controller;
    }

}

