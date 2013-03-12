<?php
return array(
    'view_manager' => array(
        'template_path_stack' => array(
            'zenddevelopertools' => __DIR__ . '/../view',
        ),
    ),
    'controllers' => array(
        'factories' => array(
            'ZendDeveloperTools\\Controller\\Datadump' => 'ZendDeveloperTools\\Controller\\Factory\\DatadumpControllerFactory',
        ),
    ),
    'router' => array(
        'routes' => array(
            'zend_developer_tools_config' => array(
                'type' => 'Zend\\Mvc\\Router\\Http\\Literal',
                'options' => array(
                    'route' => '/zdt/request/config',
                    'defaults' => array(
                        'controller' => 'ZendDeveloperTools\\Controller\\Datadump',
                        'action'     => 'show',
                        'title'      => 'Loaded Configuration'
                    ),
                ),
            ),
            'zend_developer_tools_services' => array(
                'type' => 'Zend\\Mvc\\Router\\Http\\Literal',
                'options' => array(
                    'route' => '/zdt/request/services',
                    'defaults' => array(
                        'controller' => 'ZendDeveloperTools\\Controller\\Datadump',
                        'action'     => 'show',
                        'title'      => 'Loaded Services'
                    ),
                ),
            ),
        ),
    ),
);
