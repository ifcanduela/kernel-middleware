<?php

namespace test;

use ifcanduela\kernel\Kernel;
use ifcanduela\kernel\Middleware;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class KernelTest extends TestCase
{
    public function testNoResponseCreated()
    {
        $kernel = new Kernel([]);

        $this->expectException(\exception::class);
        $this->expectExceptionMessage("No response");

        $response = $kernel->handle(Request::create("/", "GET"));
    }

    public function testAnonymousClassMiddleware()
    {
        $kernel = new Kernel([
            new class
            {
                public function __invoke()
                {
                    return new JsonResponse("json response");
                }
            }
        ]);

        $response = $kernel->handle(Request::create("/", "GET"));

        $this->assertEquals('"json response"', $response->getContent());
    }

    public function testRequestIsReplaced()
    {
        $kernel = new Kernel([
            function ($request, $next) {
                return $next(Request::create("/replaced"));
            },
            function ($request, $next) {
                return new Response($request->getPathInfo());
            },
        ]);

        $response = $kernel->handle(Request::create("/original"));

        $this->assertEquals("/replaced", $response->getContent());
    }

    public function testRequestIsMutated()
    {
        $kernel = new Kernel([
            function (Request $request, $next) {
                $request->setMethod("PUT");
                return $next($request);
            },
            function ($request, $next) {
                return new Response($request->getMethod());
            },
        ]);

        $response = $kernel->handle(Request::create("/", "GET"));

        $this->assertEquals("PUT", $response->getContent());
    }

    public function testInvalidMiddleware()
    {
        $kernel = new Kernel([
            new \DateTime(),
        ]);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage("Invalid middleware definition at index 0");
        $kernel->handle(Request::create("/"));
    }

    public function testMiddlewareObject()
    {
        $mockRequest = Request::create("/");
        $mockResponse = new Response();

        /** @var MockObject|Middleware */
        $mock = $this->createMock(Middleware::class);
        $mock->expects($this->once())
            ->method("handle")
            ->with($mockRequest)
            ->willReturn($mockResponse);

        $kernel = new Kernel([
            $mock,
        ]);

        $response = $kernel->handle($mockRequest);
        $this->assertEquals($mockResponse, $response);
    }
}
