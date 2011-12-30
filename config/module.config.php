<?php
return array(
    'zend_developer_tools' => array(
        'layout' => 'layouts/developer-toolbar.phtml',
    ),
    'di' => array(
        'instance' => array(
            'Zend\View\PhpRenderer' => array(
                'parameters' => array(
                    'options'  => array(
                        'script_paths' => array(
                            'zend_developer_tools' => __DIR__ . '/../views',
                        ),
                    ),
                ),
            ),
        ),
    ),
);
