<?php declare(strict_types=1);

namespace WyriHaximus\React\Http\PSR15MiddlewareGroup;

use FriendsOfReact\Http\Middleware\Psr15Adapter\GroupedPSR15Middleware;
use Middlewares\AccessLog;
use Middlewares\ClientIp;
use Middlewares\Expires;
use Middlewares\Https;
use Middlewares\ResponseTime;
use Middlewares\Uuid;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use React\EventLoop\LoopInterface;
use WyriHaximus\Psr15\Cat\CatMiddleware;
use function Composed\package;

final class Factory
{
    const DEFAULT_OPTIONS = [
        'extra_expires' => [
            'text/plain' => '+1 year',
        ],
        'proxy' => [
            '127.0.0.1',
        ],
        'access_log_format' => '%a %l %u %Dms "%r" %>s %b "%{Referer}i" "%{User-Agent}i"',
    ];

    public static function create(LoopInterface $loop, LoggerInterface $logger, array $options = []): GroupedPSR15Middleware
    {
        $options = \array_merge(self::DEFAULT_OPTIONS, $options);
        $expires = require package('middlewares/cache')->getPath('src/expires_defaults.php');
        foreach ($options['extra_expires'] as $key => $value) {
            $expires[$key] = $value;
        }

        $middleware = (new GroupedPSR15Middleware($loop))->
            withMiddleware(ClientIp::class, [], function ($clientIp) use ($options) {
                if (\count($options['proxy']) > 0) {
                    return $clientIp->proxy($options['proxy']);
                }

                return $clientIp;
            })->
            withMiddleware(Uuid::class)->
            withMiddleware(AccessLog::class, [$logger], function ($accessLog) use ($options) {
                return $accessLog->
                    format($options['access_log_format'])->
                    ipAttribute('client-ip')->
                    context(function (ServerRequestInterface $request, ResponseInterface $response) {
                        return [
                            'client-ip' => $request->getAttribute('client-ip'),
                            'request-id' => $request->getHeaderLine('X-Uuid'),
                            'request-method' => $request->getMethod(),
                            'request-protocol-version' => $request->getProtocolVersion(),
                            'response-protocol-version' => $response->getProtocolVersion(),
                            'response-status-code' => $response->getStatusCode(),
                            'response-time' => $response->getHeaderLine('X-Response-Time'),
                            'response-time-float' => \substr($response->getHeaderLine('X-Response-Time'), 0, -2),
                            'response-time-float-single-digit' => \round((float)\substr($response->getHeaderLine('X-Response-Time'), 0, -2), 1),
                            'response-time-int' => (int)\round((float)\substr($response->getHeaderLine('X-Response-Time'), 0, -2), 0),
                        ];
                    })
                ;
            })->
            withMiddleware(ResponseTime::class)->
            withMiddleware(CatMiddleware::class, [true])->
            withMiddleware(Expires::class, [$expires]);

        if (isset($options['hsts']) && $options['hsts'] === true) {
            $middleware = $middleware->withMiddleware(Https::class, [], function ($https) {
                return $https->redirect(false)->preload(true)->includeSubdomains(true);
            });
        }

        return $middleware;
    }
}
