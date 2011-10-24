<?php

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
