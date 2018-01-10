<?php declare(strict_types=1);

namespace WyriHaximus\React\Http\PSR15MiddlewareGroup;

use FriendsOfReact\Http\Middleware\Psr15Adapter\GroupedPSR15Middleware;
use Middlewares\AccessLog;
use Middlewares\ClientIp;
use Middlewares\Expires;
use Middlewares\ResponseTime;
use Middlewares\Uuid;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use React\EventLoop\LoopInterface;
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
        'access_log_format' => '%a %l %u %t "%r" %>s %b "%{Referer}i" "%{User-Agent}i"',
    ];

    public static function create(LoopInterface $loop, LoggerInterface $logger, array $options = []): GroupedPSR15Middleware
    {
        $options = array_merge(self::DEFAULT_OPTIONS, $options);
        $expires = require package('middlewares/cache')->getPath('src/expires_defaults.php');
        foreach ($options['extra_expires'] as $key => $value) {
            $expires[$key] = $value;
        }

        return (new GroupedPSR15Middleware($loop))->
            withMiddleware(ClientIp::class, [], function ($clientIp) use ($options) {
                if (count($options['proxy']) > 0) {
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
                            'request-id' => $request->getHeaderLine('X-Uuid'),
                            'response-time' => $response->getHeaderLine('X-Response-Time'),
                            'response-time-float' => substr($response->getHeaderLine('X-Response-Time'), 0, -2),
                            'client-ip' => $request->getAttribute('client-ip'),
                            'response-status-code' => $response->getStatusCode(),
                        ];
                    })
                ;
            })->
            withMiddleware(ResponseTime::class)->
            withMiddleware(Expires::class, [$expires]);
    }
}
