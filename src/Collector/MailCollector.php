<?php
/**
 * @see       https://github.com/zendframework/zend-developer-tools for the canonical source repository
 * @copyright Copyright (c) 2011-2018 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-developer-tools/blob/master/LICENSE.md New BSD License
 */

namespace ZendDeveloperTools\Collector;

use Zend\Mvc\MvcEvent;

/**
 * Mail Data Collector.
 *
 */
class MailCollector extends AbstractCollector implements AutoHideInterface
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'mail';
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        return 100;
    }

    /**
     * {@inheritdoc}
     */
    public function collect(MvcEvent $mvcEvent)
    {
        // todo
    }

    /**
     * {@inheritdoc}
     */
    public function canHide()
    {
        return true;
    }

    /**
     * Returns the total number of E-Mails send.
     *
     * @return integer
     */
    public function getMailsSend()
    {
        return 0;
    }
}
