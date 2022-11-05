<?php

namespace ifcanduela\kernel\middleware;

use Closure;
use ifcanduela\kernel\Middleware;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class BasicMiddleware implements Middleware
{
    public function handle(Request $request, Closure $next): ?Response
    {
        if ($request->query->get("basic", false)) {
            return new JsonResponse(["you" => "basic"]);
        }

        return $next();
    }
}
