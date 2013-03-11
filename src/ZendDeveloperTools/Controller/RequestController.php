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

use Zend\View\Model\ViewModel;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Json\Json as JsonParser;

/**
 * @category   Zend
 * @package    ZendDeveloperTools
 * @subpackage Controller
 */
class RequestController extends AbstractActionController
{
    /**
     *
     */
    protected function init()
    {
        if (!$this->getRequest()->isPost()) {
            $this->plugin('redirect')->toUrl('/');
        }
    }

    /**
     * @return array|\Zend\View\Model\ViewModel
     */
    public function configAction()
    {
        $this->init();
        $config = JsonParser::decode($this->getRequest()->getPost('config'));
        $vm = new ViewModel(array(
                                 'title' => 'Loaded configuration',
                                 'data' => $config
                            ));

        return $vm->setTemplate('zend-developer-tools/request/data-page');
    }

    /**
     * @return \Zend\View\Model\ViewModel
     */
    public function servicesAction()
    {
        $this->init();
        $services = JsonParser::decode($this->getRequest()->getPost('services'));
        $vm = new ViewModel(array(
                                 'title' => 'Loaded Services',
                                 'data' => $services
                            ));

        return $vm->setTemplate('zend-developer-tools/request/data-page');
    }
}