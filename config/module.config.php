<?php
$root = dirname(__DIR__);

return array(
    'view_manager' => array(
        'template_map' => array(
            'zend-developer-tools/toolbar/style'         => $root . '/view/zend-developer-tools/toolbar/style.phtml',
            'zend-developer-tools/toolbar/toolbar'       => $root . '/view/zend-developer-tools/toolbar/toolbar.phtml',
            'zend-developer-tools/toolbar/zendframework' => $root . '/view/zend-developer-tools/toolbar/zendframework.phtml',
            'zend-developer-tools/toolbar/error'         => $root . '/view/zend-developer-tools/toolbar/error.phtml',
            'zend-developer-tools/toolbar/time'          => $root . '/view/zend-developer-tools/toolbar/time.phtml',
            'zend-developer-tools/toolbar/memory'        => $root . '/view/zend-developer-tools/toolbar/memory.phtml',
            'zend-developer-tools/toolbar/request'       => $root . '/view/zend-developer-tools/toolbar/request.phtml',
            'zend-developer-tools/toolbar/db'            => $root . '/view/zend-developer-tools/toolbar/db.phtml',
            'zend-developer-tools/toolbar/mail'          => $root . '/view/zend-developer-tools/toolbar/mail.phtml',
        ),
    ),
);
