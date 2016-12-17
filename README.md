Loggy Servicer Provider para Slim 3
=================

[![Build Status](https://travis-ci.org/mostofreddy/slim-loggy.svg?branch=master)](https://travis-ci.org/mostofreddy/slim-loggy)

# Versión

__0.1.0__

# License

The MIT License (MIT). Ver el archivo [LICENSE](LICENSE.md) para más información

# Changelog

Ver archvio [CHANGELOG](CHANGELOG.md)

# Documentación

Instalación
-----------

Agregar en el archivo `composer.json`

```
{
    "require": {
        "restyphp/slim-loggy": "0.1.*"
    }
}
```

Registrar loggers
-----------------


```
$config = [];
$config['services'] = [
    '\Resty\Slim\Providers\LoggyServiceProvider'
];


$app = new \Slim\App($config);
$app->add('\Resty\Slim\ServiceProviderMiddleware');
```

Configuración de loggers
---------------------

```
$config = [];
$config['settings'] = [
    // slim config
    "loggy" => [
        "logger_default" => [
            [
                "level" => \Mostofreddy\Loggy\Logger::DEBUG,
                "handler" => "\Mostofreddy\Loggy\Handler\File",
                "config" => [
                    "output" => '/tmp',
                    'fileName' => "slim"
                ]
            ]
        ],
        "logger_otro" => [
            [
                "level" => \Mostofreddy\Loggy\Logger::DEBUG,
                "handler" => "\Mostofreddy\Loggy\Handler\File",
                "config" => [
                    "output" => '/tmp',
                    'fileName' => "slim"
                ]
            ]
        ]
    ]
];
```

Uso
---

```
$app->get('/', function (ServerRequestInterface $request, ResponseInterface $response) {
    $logger = $this->get("logger_default");
    $loggerOtro = $this->get("logger_otro");

    $logger->debug("esto es un mensaje ".rand());
    $loggerOtro->debug("esto es un mensaje otro ".rand());

    $body = $response->getBody();
    $body->write("bla");
    return $response;
});
```
