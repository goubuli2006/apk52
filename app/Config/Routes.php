<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override('App\Controllers\Pc\Errors::show404');
// The Auto Routing (Legacy) is very dangerous. It is easy to create vulnerable apps
// where controller filters or CSRF protection are bypassed.
// If you don't want to define all routes, please use the Auto Routing (Improved).
// Set `$autoRoutesImproved` to true in `app/Config/Feature.php` and set the following to true.
// $routes->setAutoRoute(false);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */
/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (is_file(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}

if (array_key_exists('SERVER_NAME', $_SERVER)) {
    // var_dump(env('app.rootUrl','www.97k.com'));
    // dd($_SERVER['SERVER_NAME']);
    if ($_SERVER['SERVER_NAME'] == env('app.rootUrl','www.97k.com')) {
        require APPPATH . 'Config/Routes/Statics/PcRoutes.php';
    } else if ($_SERVER['SERVER_NAME'] == env('app.mobileUrl','m.97k.com')) {
        require APPPATH . 'Config/Routes/Statics/MobileRoutes.php';
    }
}

// 加载 cli 路由
if (is_file(APPPATH . 'Config/Routes/CLIRoutes.php')) {
    require APPPATH . 'Config/Routes/CLIRoutes.php';
}

// 加载 api 路由
if (is_file(APPPATH . 'Config/Routes/APIRoutes.php')) {
    require APPPATH . 'Config/Routes/APIRoutes.php';
}