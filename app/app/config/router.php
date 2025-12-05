<?php

declare(strict_types=1);


$router = $di->getRouter();

// Define your routes here

$router->add(
    '/api/:controller/:action',
    [
        'namespace'  => 'AppDomain\Presentation\Api',
        'controller' => 1,
        'action'     => 2,
    ]
);

$router->setDefaultNamespace('App\Controllers');

$router->handle($_SERVER['REQUEST_URI']);
