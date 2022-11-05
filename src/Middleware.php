<?php

namespace ifcanduela\kernel;

use Closure;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

interface Middleware
{
    public function handle(Request $request, Closure $next): ?Response;
}
