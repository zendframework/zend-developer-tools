<?php
return array(
    'view_manager' => array(
        'template_path_stack' => array(
            'zenddevelopertools' => __DIR__ . '/../view',
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'ZendDeveloperTools\\Controller\\Request' => 'ZendDeveloperTools\\Controller\\RequestController',
        ),
    ),
    'router' => array(
        'routes' => array(
            'zend_developer_tools_config' => array(
                'type' => 'Zend\\Mvc\\Router\\Http\\Literal',
                'options' => array(
                    'route' => '/zdt/request/config',
                    'defaults' => array(
                        'controller' => 'ZendDeveloperTools\\Controller\\Request',
                        'action'     => 'config',
                    ),
                ),
            ),
            'zend_developer_tools_services' => array(
                'type' => 'Zend\\Mvc\\Router\\Http\\Literal',
                'options' => array(
                    'route' => '/zdt/request/services',
                    'defaults' => array(
                        'controller' => 'ZendDeveloperTools\\Controller\\Request',
                        'action'     => 'services',
                    ),
                ),
            ),
        ),
    ),
);
