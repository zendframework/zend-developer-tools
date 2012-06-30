<?php
return array(
    'zend_developer_tools' => array(
        'layout' => 'layouts/developer-toolbar.phtml',
    ),
    'di' => array(
        'instance' => array(
            'Zend\View\Resolver\TemplatePathStack' => array(
                'parameters' => array(
                    'paths' => array(
                        'zend_developer_tools' => __DIR__.'/../views',
                    ),
                ),
            ),
        ),
    ),
);
