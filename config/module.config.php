<?php
return array(
    'zend_developer_tools' => array(
        'layout' => 'layouts/developer-toolbar.phtml',
    ),
    'di' => array(
        'instance' => array(
            'alias' => array(
                'view' => 'Zend\View\Renderer\PhpRenderer'
            ),
                
            'Zend\View\Resolver\TemplatePathStack' => array(
                'parameters' => array(
                    'paths' => array(
                        'zend_developer_tools' => __DIR__ . '/../views',
                    ),
                ),
            ),
        ),
    ),
);
