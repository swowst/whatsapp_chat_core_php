<?php
return [
    '/add' => [
        'module'     => 'default',
        'controller' => 'add',
        'action'     => 'index',
    ],
    '/list' => [
        'module'     => 'default',
        'controller' => 'list',
        'action'     => 'index',
    ],
    '/delete' => [
        'module'     => 'default',
        'controller' => 'delete',
        'action'     => 'index',
    ],
    '/edit' => [
        'module'     => 'default',
        'controller' => 'edit',
        'action'     => 'index',
    ],
    '/info' => [
        'module'     => 'default',
        'controller' => 'info',
        'action'     => 'index',
    ],
    '/move' => [
        'module'     => 'default',
        'controller' => 'move',
        'action'     => 'index',
    ],

    '/:module/:controller/:action/:params' => [
        'module'     => 1,
        'controller' => 2,
        'action'     => 3,
        'params'     => 4,
    ],
    '/:module/:controller/:action'         => [
        'module'     => 1,
        'controller' => 2,
        'action'     => 3,
    ],
    '/:module/:controller'                 => [
        'module'     => 1,
        'controller' => 2,
        'action'     => 'index',
    ],
    '/:module'                             => [
        'module'     => 1,
        'controller' => 'index',
        'action'     => 'index',
    ],
];
