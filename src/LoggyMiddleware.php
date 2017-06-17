<?php
/**
 * LoggyMiddleware
 *
 * PHP version 7+
 *
 * Copyright (c) 2016 Federico Lozada Mosto <mosto.federico@gmail.com>
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 *
 * @category  Resty
 * @package   Resty
 * @author    Federico Lozada Mosto <mosto.federico@gmail.com>
 * @copyright 2017 Federico Lozada Mosto <mosto.federico@gmail.com>
 * @license   MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @link      http://www.mostofreddy.com.ar
 */
namespace Resty\Slim\Middleware;

use Mostofreddy\Loggy\Logger;
/**
 * LoggyMiddleware
 *
 * @category  Resty
 * @package   Resty
 * @author    Federico Lozada Mosto <mosto.federico@gmail.com>
 * @copyright 2017 Federico Lozada Mosto <mosto.federico@gmail.com>
 * @license   MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @link      http://www.mostofreddy.com.ar
 */
class LoggyMiddleware
{
    protected $container;
    protected $configName = 'loggy';
    /**
     * Constructor
     * 
     * @param Container $container Instancia de container
     */
    public function __construct($container)
    {
        $this->container = $container;
    }
    /**
     * Setea donde se encuentra la configuracion del logger dentro del array 'settings' de Slim
     * 
     * @param string $name Nombre de la configuraicon
     * 
     * @return self
     */
    public function config(string $name)
    {
        $this->configName = $name;
        return $this;
    }
    /**
     * Loggy middleware invokable class
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request  PSR7 request
     * @param \Psr\Http\Message\ResponseInterface      $response PSR7 response
     * @param callable                                 $next     Next middleware
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function __invoke($request, $response, $next)
    {
        $this->setLoggers();
        $response = $next($request, $response);
        return $response
            ->withHeader(
                'Slim-Loggy-uid', 
                Logger::generateUid()
            );
    }
    /**
     * Crea los loggers en el container
     *
     * @return void
     */
    protected function setLoggers()
    {
        if ($loggers = $this->hasLoggerConfig()) {
            foreach ($loggers as $channel => $logger) {
                $loggerChannel = $this->getChannelName($logger, $channel);
                $loggerName = $this->getLoggerIdentified($logger, $loggerChannel);
                
                $this->container[$loggerName] = Logger::get(
                    $loggerChannel, 
                    $this->buildHandlers($logger['handlers']??[])
                );
            }
        }
    }
    /**
     * Instancia los handlers del logger
     * 
     * @param array $config Configuracion de los handlers del logger
     * 
     * @return array
     */
    protected function buildHandlers(array $config):array
    {
        $handlers = [];
        foreach ($config as $handler) {
            $level = constant('\Mostofreddy\Loggy\Logger::'. strtoupper($handler['level']));
            $handlerObj = '\Mostofreddy\Loggy\Handler\\'.ucfirst($handler['handler']);
            $handlers[] = (new $handlerObj($level))->config($handler);
        }
        return $handlers;
    }
    /**
     * Devuelve el identificador del logger en el container
     * 
     * @param array  $config            Configuracion del logger
     * @param string $defaultIdentified Nombre por defecto
     * 
     * @return string
     */
    protected function getLoggerIdentified(array $config, string $defaultIdentified):string
    {
        return $config['alias']??$defaultIdentified;
    }
    /**
     * Devuelve el nombre del canal
     * 
     * @param array  $config             Configuracion del logger
     * @param string $defaultChannelName Nombre por defecto
     * 
     * @return string
     */
    protected function getChannelName(array $config, string $defaultChannelName):string
    {
        return $config['channel']??$defaultChannelName;
    }
    /**
     * Si existe la configuracion de los loggers la devuevle, caso contrario devuevle null
     * 
     * @return array|null
     */
    protected function hasLoggerConfig()
    {
        return $this->container->get('settings')[$this->configName]??null;
    }
}
