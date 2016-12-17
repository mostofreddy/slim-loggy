<?php
/**
 * LoggyServiceProvider
 *
 * PHP version 7+
 *
 * Copyright (c) 2016 Federico Lozada Mosto <mosto.federico@gmail.com>
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 *
 * @category  Resty
 * @package   Resty\Providers
 * @author    Federico Lozada Mosto <mosto.federico@gmail.com>
 * @copyright 2016 Federico Lozada Mosto <mosto.federico@gmail.com>
 * @license   MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @link      http://www.mostofreddy.com.ar
 */
namespace Resty\Slim\Providers;

// Resty
use Resty\Slim\AbstractServiceProvider;
// Slim
use Slim\Container;
// Loggy
use Mostofreddy\Loggy\Logger;

/**
 * LoggyServiceProvider
 *
 * @category  Resty
 * @package   Resty\Providers
 * @author    Federico Lozada Mosto <mosto.federico@gmail.com>
 * @copyright 2016 Federico Lozada Mosto <mosto.federico@gmail.com>
 * @license   MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @link      http://www.mostofreddy.com.ar
 */
class LoggyServiceProvider extends AbstractServiceProvider
{
    const ERROR_NO_CONFIG = "Loggy - Config not found";
    /**
     * Registra el servicio
     *
     * @param Container $c Instancia de Container
     *
     * @return void
     */
    public static function register(Container $c)
    {
        $loggy = $c->get('settings')['loggy'];
        if ($loggy === null) {
            throw new \InvalidArgumentException(static::ERROR_NO_CONFIG);
        }

        foreach ($loggy as $channel => $channelConfig) {
            $handlers = [];
            foreach ($channelConfig as $handlerConfig) {
                extract($handlerConfig);
                $handlers[] = (new $handler($level))->config($config);
            }
            $c[$channel] = new Logger($channel, $handlers);
        }

    }
}
