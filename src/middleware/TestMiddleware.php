<?php

namespace ifcanduela\kernel\middleware;

use Closure;
use ifcanduela\kernel\Middleware;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TestMiddleware implements Middleware
{
    public function handle(Request $request, Closure $next): ?Response
    {
        if ($request->query->has("test") && $request->query->get("test") !== "0") {
            return new JsonResponse(["test"]);
        }

        $response = $next();

        if ($response instanceof Response) {
            return $response;
        }

        return new JsonResponse([1, 2, 3]);
    }
}
