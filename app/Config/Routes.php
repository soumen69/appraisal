<?php

use CodeIgniter\Router\RouteCollection;

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

$routes->group('modules', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'Admin\ModuleController::index');
    $routes->get('list', 'Admin\ModuleController::list');
    $routes->post('store', 'Admin\ModuleController::store');
    $routes->post('update/(:num)', 'Admin\ModuleController::update/$1');
    $routes->post('delete/(:num)', 'Admin\ModuleController::delete/$1');
    $routes->get('edit/(:num)', 'Admin\ModuleController::edit/$1');
});