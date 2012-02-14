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
 * @subpackage Service
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendDeveloperTools\Service;

use ZendDeveloperTools\Controller\DeveloperToolsController;

class DeveloperTools
{
    public static $startTime;
    public static $stopTime;

    public function appendResponse($event)
    {
        $response = $event->getResponse();
        $controller = new DeveloperToolsController;
        $controller->dispatch($event->getRequest(), $event->getResponse(), $event);
        $append = $event->getResult();
        if (!is_string($append)) {
            return $response;
        }
        if (!$response) {
            return;
        }
        $responseBody = $response->getBody();
        $responseBody = str_ireplace('</body>', $append . "\n</body>", $responseBody, $count);
        if ($count === 0) {
            // no </body> tag found to inject before, just append
            $responseBody = $responseBody . $append;
        } elseif ($count > 1) {
            // more than one replacement performed, not good.
        }
        return $response->setContent($responseBody);
    }
}
