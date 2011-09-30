<?php

namespace ZendDeveloperTools\Service;

class DeveloperTools
{
    public function appendResponse($response)
    {
        $append = '<hr/>ZendDeveloperTools Module Loaded';
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
