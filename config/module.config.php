<?php
return array(
    'router' => array(
        'routes' => array(
            'zdtBackgroundRequests' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/zdt-background-requests[/:uuid][/]',
                    'defaults' => array(
                        'controller' => 'zenddevelopertools',
                        'action' => 'background-requests',
                    ),
                ),
            ),
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'zenddevelopertools' => __DIR__ . '/../view',
        ),
    ),
);
