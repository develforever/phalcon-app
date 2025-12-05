<?php

declare(strict_types=1);

// var_dump($_ENV, ini_get_all('xdebug'));exit(1);

use Phalcon\Di\FactoryDefault\Cli as DiFactoryDefault;
use AppDomain\Application\User\Handler\ChangeUserEmailHandler;
use AppDomain\Application\User\Handler\RegisterUserHandler;
use AppDomain\Application\User\Query\UserReadModel;
use AppDomain\Domain\User\UserRepository;
use AppDomain\Infrastructure\Persistence\EventStoreUserRepository;
use Phalcon\Mvc\Model\Metadata\Memory as MetaDataAdapter;
use Phalcon\Mvc\Url as UrlResolver;
use Phalcon\Mvc\View;
use Phalcon\Mvc\View\Engine\Php as PhpEngine;
use Phalcon\Mvc\View\Engine\Volt as VoltEngine;

define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');

$_SERVER['REQUEST_URI'] = $_SERVER['REQUEST_URI'] ?? '/';

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

if ($uri !== '/' && file_exists(__DIR__ . '/public' . $uri)) {
    return false;
}

$_GET['_url'] = $_SERVER['REQUEST_URI'];

try {
    $di = new DiFactoryDefault();


    /**
     * Shared configuration service
     */
    $di->setShared('config', function () {
        return include APP_PATH . '/config/tests/config.php';
    });

    /**
     * The URL component is used to generate all kind of urls in the application
     */
    $di->setShared('url', function () {
        $config = $this->getConfig();

        $url = new UrlResolver();
        $url->setBaseUri($config->application->baseUri);

        return $url;
    });

    /**
     * Setting up the view component
     */
    $di->setShared('view', function () {
        $config = $this->getConfig();

        $view = new View();
        $view->setDI($this);
        $view->setViewsDir($config->application->viewsDir);

        $view->registerEngines([
            '.volt' => function ($view) {
                $config = $this->getConfig();

                $volt = new VoltEngine($view, $this);

                $volt->setOptions([
                    'path' => $config->application->cacheDir,
                    'separator' => '_'
                ]);

                return $volt;
            },
            '.phtml' => PhpEngine::class

        ]);

        return $view;
    });

    /**
     * Database connection is created based in the parameters defined in the configuration file
     */
    $di->setShared('db', function () {
        $config = $this->getConfig();

        $class = 'Phalcon\Db\Adapter\Pdo\\' . $config->database->adapter;
        $params = [
            'dbname'     => $config->database->dbname,
        ];

        if ($config->database->adapter == 'Postgresql') {
            unset($params['charset']);
        }

        return new $class($params);
    });


    /**
     * If the configuration specify the use of metadata adapter use it or use memory otherwise
     */
    $di->setShared('modelsMetadata', function () {
        return new MetaDataAdapter();
    });


    $di->setShared(EventStoreUserRepository::class, function () use ($di) {
        return new EventStoreUserRepository($di->get('db')); // adapter PDO z Phalcona
    });

    $di->setShared(UserRepository::class, function () use ($di) {
        return $di->get(EventStoreUserRepository::class);
    });

    $di->setShared(RegisterUserHandler::class, function () use ($di) {
        return new RegisterUserHandler($di->get(UserRepository::class));
    });

    $di->setShared(ChangeUserEmailHandler::class, function () use ($di) {
        return new ChangeUserEmailHandler($di->get(UserRepository::class));
    });

    $di->setShared(UserReadModel::class, function () {
        return new UserReadModel();
    });

    $config = $di->getConfig();

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





    /**
     * Handle the request
     */
    $application = new \Phalcon\Cli\Console($di);
    $arguments = [];

    $application->handle($arguments);
} catch (\Exception $e) {
    print_r($e->getMessage() . PHP_EOL);
}
