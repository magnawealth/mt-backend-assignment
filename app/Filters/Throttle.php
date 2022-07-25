<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;

class Throttle implements FilterInterface
{
    /**
     * This is a demo implementation of using the Throttler class
     * to implement rate limiting for your application.
     *
     * @param array|null $arguments
     *
     * @return mixed
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $throttler = Services::throttler();

        // Restrict an IP address to no more than 10 request
        // per hour across the entire site (for all incomming calls).
        if ($throttler->check(md5($request->getIPAddress()), 10, HOUR) === false) {
            return Services::response()->setStatusCode(429);
        }
    }

    /**
     * We don't have anything to do here.
     *
     * @param array|null $arguments
     *
     * @return mixed
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // ...
    }
}