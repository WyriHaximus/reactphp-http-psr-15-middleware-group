<?php declare(strict_types=1);

namespace WyriHaximus\React\Tests\Http\PSR15MiddlewareGroup;

use FriendsOfReact\Http\Middleware\Psr15Adapter\GroupedPSR15Middleware;
use Monolog\Logger;
use PHPUnit\Framework\TestCase;
use React\EventLoop\Factory as EventLoopFactory;
use WyriHaximus\React\Http\PSR15MiddlewareGroup\Factory;

final class FactoryTest extends TestCase
{
    public function testReturnType()
    {
        $loop = EventLoopFactory::create();
        $middleware = Factory::create($loop, new Logger('test-return-type'));

        self::assertInstanceOf(GroupedPSR15Middleware::class, $middleware);
    }
}
