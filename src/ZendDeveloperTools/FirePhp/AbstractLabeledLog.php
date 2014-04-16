<?php
/**
 * Zend Developer Tools for Zend Framework (http://framework.zend.com/)
 *
 * @link       http://github.com/zendframework/ZendDeveloperTools for the canonical source repository
 * @copyright  Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendDeveloperTools\FirePhp;

/**
 * The abstract labeled (grouped) FirePHP log class
 */
abstract class AbstractLabeledLog
    extends AbstractLog
        implements GroupLabelProviderInterface,
                   GroupOptionsProviderInterface
{
    /**
     * @var string
     */
    protected $groupLabel = null;

    /**
     * @var array
     */
    protected $groupOptions = array(
        'Collapsed' => true,
        'Color'     => GroupOptionsProviderInterface::COLOR_BLACK
    );

    /**
     * Return the group label
     *
     * @return string $groupLabel
    */
    public function getGroupLabel()
    {
        return $this->groupLabel;
    }

    /**
     * Set the group label
     *
     * @param string $groupLabel
     * @return this
     */
    public function setGroupLabel($groupLabel)
    {
        $this->groupLabel = $groupLabel;
        return $this;
    }

    /**
     * Return the group options
     *
     * @return array $groupOptions
     */
    public function getGroupOptions()
    {
        return $this->groupOptions;
    }

    /**
     * Set the group options
     *
     * @param array $groupOptions
     * @return GroupOptionsProviderInterface
     */
    public function setGroupOptions(array $groupOptions)
    {
        $this->groupOptions = $groupOptions;
        return $this;
    }

    /**
     * Writes the collected data to the browser console
     *
     * @return AbstractLog
     */
    public function writeLog()
    {
        $firePhp = $this->getFirePhp();

        //Start group
        $firePhp->group($this->getGroupLabel(), $this->getGroupOptions());

        $this->internalWriteLog();

        //End group
        $firePhp->groupEnd();

        return $this;
    }
}