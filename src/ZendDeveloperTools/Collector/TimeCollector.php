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
 * @subpackage Collector
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendDeveloperTools\Collector;

use Zend\Mvc\MvcEvent;

/**
 * Time Data Collector.
 *
 * @category   Zend
 * @package    ZendDeveloperTools
 * @subpackage Collector
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class TimeCollector extends CollectorAbstract
{
    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'time';
    }

    /**
     * @inheritdoc
     */
    public function getPriority()
    {
        return 1;
    }

    /**
     * @inheritdoc
     */
    public function collect(MvcEvent $mvcEvent)
    {
        // todo: clean up.

        if ($mvcEvent->getRequest()->server()->get('REQUEST_TIME_MICRO') !== null) {
            $start = $mvcEvent->getRequest()->server()->get('REQUEST_TIME_MICRO');
        } else {
            $start = $mvcEvent->getRequest()->server()->get('REQUEST_TIME', 0);
        }

        $this->data = array(
            'start' => $start,
            'end'   => microtime(true),
        );
    }

    public function getExecutionTime()
    {
        if (!isset($this->data['start']) || !isset($this->data['end'])) {
            return 0;
        }

        return $this->data['end'] - $this->data['start'];
    }
}