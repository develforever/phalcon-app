<?php

declare(strict_types=1);

$loader = new \Phalcon\Autoload\Loader(true);

$loader->setNamespaces(
    [
        'App'        => APP_PATH . '',
        'AppDomain'        =>  APP_PATH . '/../domain',
    ]
);

/**
 * We're a registering a set of directories taken from the configuration file
 */
$loader->setDirectories(
    [
        $config->application->controllersDir,
        $config->application->modelsDir
    ]
)
    ->setFiles(
        [
            APP_PATH . '/../vendor/autoload.php'
        ]
    );


$loader->register();

// echo '<pre>'; var_dump($loader->getDebug(), $loader);
