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
use Zend\Mvc\Controller\ActionController;

/**
 * @category   Zend
 * @package    ZendDeveloperTools
 * @subpackage Controller
 */
class IndexController extends ActionController
{
    public function indexAction()
    {
        return new ViewModel();
    }
}