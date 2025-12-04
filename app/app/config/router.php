<?php

$router = $di->getRouter();

// Define your routes here

$router->setDefaultNamespace('App\Controllers');

$router->handle($_SERVER['REQUEST_URI']);
