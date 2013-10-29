<?php
namespace ZendDeveloperTools\FirePhp;

/**
 * The FirePHP log writer interface
 */
interface LogWriterInterface
{
    /**
     * Writes the collected data to the browser console
     *
     * @return LogWriterInterface
     */
    public function writeLog();
}