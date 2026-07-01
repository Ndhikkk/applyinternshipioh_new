<?php

namespace App\Controllers;

use CodeIgniter\Controller;

/**
 * Class BaseController
 *
 * Extended by other controllers in app/Controllers
 */
class BaseController extends Controller
{
    protected $helpers = ['url', 'form'];

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);

        // Preload any models, libraries, etc, here.
    }
}
