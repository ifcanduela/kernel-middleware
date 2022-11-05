# Kernel and Middleware

A request/response processor with a simple implementation of middleware.

## Using the Kernel

The `ifcanduela\kernel\Kernel` class is ready to be extended by your own class:

```php
<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class App extends \ifcanduela\kernel\Kernel
{
    public function __construct(array $middleware)
    {
        $middleware[] = $this->run(...);

        parent::__construct($middleware)
    }

    public function run(Request $request, Closure $next): Response
    {
        return new Response("hello");
    }
}
```

## Using Middleware

Middlewares must either be callable or implement `\ifcanduela\kernel\Middleware`. The expected
signature of the callable is this:

```php
function (Request $request, Closure $next): Response;
```

Where `Request` and `Response` are the Symfony HTTP Foundation classes and the `$next` Closure is a
function that optionally accepts a Request and will return a Response.

A middleware can return `$next()` or `$next($request)` to pass control to the next middleware in the chain and get a Response object.

```php
class ExampleMiddleware implements Middleware
{
    public function handle($request, $next)
    {
        return $next();
    }
}
```

### Middleware implementing `ifcanduela\kernel\Middleware`

Any object that implements this interface is valid middleware, including anonymous classes:

```php
new Kernel([
    InitSessionMiddleware::class,

    new InitContainerMiddleware($container)

    new class implements Middleware {
        public function handle(Request $request, Closure $next): Response
        {
            // ...
        }
    }
])
```

### Middleware using callables

Functions and objects implementing `__invoke()` are acceptable middleware.

```php
new Kernel([
    new class {
        public function __invoke($req, $next) {
            return $next();
        }
    },

    fn ($req, $next) => new JsonResponse([]),
])
```
