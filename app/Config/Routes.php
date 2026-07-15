<?php

namespace Config;

use CodeIgniter\Router\RouteCollection;

$routes = Services::routes();

$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
$routes->setAutoRoute(false);

// Home routes
$routes->get('/', 'Home::index');

// Pendaftaran routes
$routes->get('daftar', 'Pendaftaran::index');
$routes->post('daftar/submit', 'Pendaftaran::submit');
$routes->get('pendaftaran', 'Pendaftaran::index');
$routes->post('pendaftaran', 'Pendaftaran::store');
$routes->get('pendaftaran/success', 'Pendaftaran::success');
$routes->post('pendaftaran/import_cv', 'Pendaftaran::import_cv');


// Progres/Peserta routes
$routes->get('peserta', 'Progres::index');
$routes->get('progres', 'Progres::index');
$routes->post('progres/cek', 'Progres::cekStatus');
$routes->post('/progres/cek', 'Progres::cek'); // ✅ Method 'cek'
$routes->get('/progres/cek', 'Progres::index'); // ✅ Fallback untuk GET
$routes->get('admin/download/(:num)/(:any)', 'Admin::download/$1/$2');
$routes->view('admin/parsing-cv', 'admin/parsing_cv');
$routes->post('progres/kirimEmail', 'Progres::kirimEmail');
$routes->get('progres/cetak-pdf/(:any)', 'Progres::cetakPdf/$1');


// Group admin routes dengan filter
$routes->group('admin', function ($routes) {
    // Route login tanpa filter
    $routes->get('login', 'Admin::login');
    $routes->post('login', 'Admin::loginProcess');

    // Route lainnya dengan filter adminauth
    $routes->group('', ['filter' => 'adminauth'], function ($routes) {
        $routes->get('dashboard', 'Admin::dashboard');
        $routes->get('logout', 'Admin::logout');
        $routes->get('export', 'Admin::exportExcel');
        $routes->get('delete/(:num)', 'Admin::delete/$1');
        $routes->get('detail/(:num)', 'Admin::detail/$1');
        $routes->post('update-status/(:num)', 'Admin::updateStatus/$1');
        $routes->get('analyze-cv/(:num)', 'Admin::analyzeCv/$1');
        $routes->get('toggle-registration', 'Admin::toggleRegistration');
        $routes->get('download/(:num)/(:any)', 'Admin::download/$1/$2');
        $routes->get('process-interview/(:num)/(:segment)', 'Admin::processInterview/$1/$2');
        $routes->get('hapus/(:num)', 'Admin::delete/$1');
        $routes->get('testform', 'Testform::index');
    });
});

// Redirect /admin ke /admin/login (TANPA FILTER)
$routes->get('admin', function () {
    return redirect()->to('/admin/login');
});