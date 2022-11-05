<?php

namespace ifcanduela\kernel;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Kernel
{
    public array $middleware;

    /**
     * @param Middleware[] $middleware
     */
    public function __construct(array $middleware)
    {
        $this->middleware = $middleware;
    }

    public function handle(Request $request): ?Response
    {
        return $this->call(0, $request);
    }

    public function call(int $index, Request $request): Response
    {
        // check if there is middleware
        if (array_key_exists($index, $this->middleware)) {
            $middleware = $this->middleware[$index];

            // check for middleware implementing the Middleware interface
            if (is_a($middleware, Middleware::class)) {
                $middleware = $middleware->handle(...);
            } elseif (is_string($middleware) && class_exists($middleware) && is_a($middleware, Middleware::class, true)) {
                $middleware = new $middleware;
            }

            if (!is_callable($middleware)) {
                throw new \RuntimeException("Invalid middleware definition at index {$index}");
            }

            return $middleware($request, function (Request $newRequest = null) use ($index, $request) {
                $request = $newRequest ?? $request;

                return $this->call($index + 1, $request);
            });
        }

        // if none of the middleware has created a response yet, we throw an Exception
        throw new \Exception("No response");
    }
}
