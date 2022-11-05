<?php

namespace ifcanduela\kernel\middleware;

use Closure;
use ifcanduela\kernel\Middleware;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckForJsonMiddleware implements Middleware
{
    public function handle(Request $request, Closure $next): ?Response
    {
        $path = urldecode($request->getPathInfo());

        if (str_ends_with($path, "|json")) {
            $newPath = preg_replace("/|json$/", "", $path);
            dump("Overriding path info {$path} ==> {$newPath}");
            $newRequest = Request::create($newPath);
            $newRequest->headers->add(["Accept" => "application/json"]);
        }

        return $next($request);
    }
}
