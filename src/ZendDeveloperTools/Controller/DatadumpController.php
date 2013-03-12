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
class DatadumpController extends AbstractActionController
{

    /**
     * @return \Zend\View\Model\ViewModel
     */
    public function showAction()
    {
        $data = JsonParser::decode($this->getRequest()->getPost('data'));
        $title = $this->getEvent()->getRouteMatch()->getParam('title');
        $vm = new ViewModel(array(
                                 'title' => $title,
                                 'data' => $data
                            ));

        return $vm->setTemplate('zend-developer-tools/datadump/show');
    }

}