<?php

namespace ifcanduela\kernel\middleware;

use Closure;
use ifcanduela\kernel\Middleware;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class NothingMiddleware implements Middleware
{
    public function handle(Request $request, Closure $next): ?Response
    {
        return $next();
    }
}