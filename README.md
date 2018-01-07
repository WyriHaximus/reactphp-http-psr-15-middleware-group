# Opinionated PSR-15 middleware group for react/http

[![Build Status](https://travis-ci.org/WyriHaximus/reactphp-http-psr-15-middleware-group.png)](https://travis-ci.org/WyriHaximus/reactphp-http-psr-15-middleware-group)
[![Latest Stable Version](https://poser.pugx.org/WyriHaximus/react-http-psr-15-middleware-group/v/stable.png)](https://packagist.org/packages/WyriHaximus/react-http-psr-15-middleware-group)
[![Total Downloads](https://poser.pugx.org/WyriHaximus/react-http-psr-15-middleware-group/downloads.png)](https://packagist.org/packages/WyriHaximus/react-http-psr-15-middleware-group)
[![Code Coverage](https://scrutinizer-ci.com/g/WyriHaximus/reactphp-http-psr-15-middleware-group/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/WyriHaximus/reactphp-http-psr-15-middleware-group/?branch=master)
[![License](https://poser.pugx.org/wyrihaximus/react-http-psr-15-middleware-group/license.png)](https://packagist.org/packages/wyrihaximus/react-http-psr-15-middleware-group)

## Installation ##

To install via [Composer](http://getcomposer.org/), use the command below, it will automatically detect the latest version and bind it with `~`.

```
composer require wyrihaximus/react-http-psr-15-middleware-group 
```

## Usage ##

The factory sets up a accesslog, with response time, UUID for each 
request and detects the correct client IP per request.

```php
$loop = \React\EventLoop\Factory::create(); 
$logger = new \Monolog\Logger(); // Any PSR-3 logger will do
$options = []; // Optional see Factory::DEFAULT_OPTIONS for thedefaults
$server = new Server([
    /** Other middleware */
    Factory::create($loop, $logger),
    /** Other middleware */
]);
```


## License ##

Copyright 2018 [Cees-Jan Kiewiet](http://wyrihaximus.net/)

Permission is hereby granted, free of charge, to any person
obtaining a copy of this software and associated documentation
files (the "Software"), to deal in the Software without
restriction, including without limitation the rights to use,
copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the
Software is furnished to do so, subject to the following
conditions:

The above copyright notice and this permission notice shall be
included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
OTHER DEALINGS IN THE SOFTWARE.
