<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;
use CodeIgniter\HTTP\ResponseTrait;

class Logger implements FilterInterface
{
    use ResponseTrait;
    /**
     * This is the implementation of logging for the application.
     *
     * @param array|null $arguments
     *
     * @return mixed
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        // Log IP Address
        log_message('info', 'Request from IP:{ip}', ['ip' => $request->getIPAddress()]);
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
        // Return controller method response
        $statusCode = Services::response()->getStatusCode();
        $statusMessage = Services::response()->getReasonPhrase();
        log_message('info', $statusCode . ': ' . $statusMessage);
    }
}