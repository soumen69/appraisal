<?php

use CodeIgniter\Router\RouteCollection;
use App\Controllers\Admin\ModuleController;

/** @var RouteCollection $routes */
// $routes->get('/', 'Home::index');
// $routes->get('/', 'Dashboard::index');
$routes->get('/', 'AuthController::login');

// $routes->get('/login', 'AuthController::login');
$routes->post('/login', 'AuthController::authenticate');

$routes->get('/forgot-password', 'AuthController::forgotPassword');

$routes->get('/reset-password/(:any)', 'AuthController::resetPassword/$1');

$routes->get('/profile', 'AuthController::profile');

$routes->get('/logout', 'AuthController::logout');

// $routes->get('/', 'Dashboard::index');
$routes->get('/dashboard', 'Dashboard::index');

// $routes->group('modules', ['filter' => 'auth'], function ($routes) {
$routes->group('modules', ['namespace' => 'App\Controllers\Admin'], function ($routes) {

    $routes->get('/', 'ModuleController::index');
    $routes->get('list', 'ModuleController::list');
    $routes->post('store', 'ModuleController::store');
    $routes->post('update/(:num)', 'ModuleController::update/$1');
    $routes->post('delete/(:num)', 'ModuleController::delete/$1');
    $routes->get('edit/(:num)', 'ModuleController::edit/$1');
});
