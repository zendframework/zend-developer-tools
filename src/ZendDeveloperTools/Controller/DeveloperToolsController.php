<?php
/**
 * Zend Developer Tools for Zend Framework (http://framework.zend.com/)
 *
 * @link       http://github.com/zendframework/ZendDeveloperTools for the canonical source repository
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendDeveloperTools\Controller;

use Zend\View\Model\ViewModel;
use Zend\Mvc\Controller\AbstractActionController;
use ZendDeveloperTools\Options;

class DeveloperToolsController extends AbstractActionController
{
    /**
     * @var Options
     */
    protected $options;

    public function __construct(Options $options)
    {
        $this->options = $options;
    }

    public function indexAction()
    {
        return new ViewModel();
    }

    public function backgroundRequestsAction()
    {
        $this->options->setToolbar(['enabled' => false]);

        /*TODO: make this configurable */
        $this->options->setBackgroundrequests(['enabled' => false]);

        $toolbars = array();
        $cacheDir = $this->options->getCacheDir();

        foreach (glob($cacheDir . '/ZDT_*.entries') as $entriesFileName) {
            $toolbars[] = unserialize(file_get_contents($entriesFileName));
        }

        $model = new ViewModel(['toolbars' => $toolbars]);
        $model->setTerminal(true);
        return $model;
    }
}