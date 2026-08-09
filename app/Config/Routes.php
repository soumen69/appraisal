<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->get('unauthorized', 'UnauthorizedController::index', ['filter' => 'auth']);

/*
|--------------------------------------------------------------------------
| Guest Routes
|--------------------------------------------------------------------------
*/

$routes->group('', ['filter' => 'guest'], static function ($routes) {
    $routes->get('/', 'AuthController::login');
    $routes->post('login', 'AuthController::authenticate');
    $routes->get('forgot-password', 'AuthController::forgotPassword');
    $routes->get('reset-password/(:any)', 'AuthController::resetPassword/$1');
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/

$routes->group('', ['filter' => 'auth'], static function ($routes) {
    $routes->get('dashboard', 'Dashboard::index');
    $routes->get('profile', 'AuthController::profile');
    $routes->get('logout', 'AuthController::logout');

    $routes->group(
        '',
        ['namespace' => 'App\Controllers\Admin'],
        static function ($routes) {
            $routes->group(
                'modules',
                ['filter' => 'permission:module.view'],
                static function ($routes) {
                    $routes->get('/', 'ModuleController::index');
                    $routes->get('list', 'ModuleController::list');
                    $routes->get('edit/(:num)', 'ModuleController::edit/$1');
                    $routes->get('options', 'ModuleController::options');
                    $routes->post('store', 'ModuleController::store', ['filter' => 'permission:module.create']);
                    $routes->post('update/(:num)', 'ModuleController::update/$1', ['filter' => 'permission:module.edit']);
                    $routes->post('delete/(:num)', 'ModuleController::delete/$1', ['filter' => 'permission:module.delete']);
                }
            );

            $routes->group(
                'menus',
                ['filter' => 'permission:menu.view'],
                static function ($routes) {
                    $routes->get('/', 'MenuController::index');
                    $routes->get('list', 'MenuController::list');
                    $routes->get('edit/(:num)', 'MenuController::edit/$1');
                    $routes->get('options', 'MenuController::options');
                    $routes->post('store', 'MenuController::store', ['filter' => 'permission:menu.create']);
                    $routes->post('update/(:num)', 'MenuController::update/$1', ['filter' => 'permission:menu.edit']);
                    $routes->post('delete/(:num)', 'MenuController::delete/$1', ['filter' => 'permission:menu.delete']);
                }
            );

            $routes->group(
                'permissions',
                ['filter' => 'permission:permission.view'],
                static function ($routes) {
                    $routes->get('/', 'PermissionController::index');
                    $routes->get('list', 'PermissionController::list');
                    $routes->get('edit/(:num)', 'PermissionController::edit/$1');
                    $routes->post('store', 'PermissionController::store', ['filter' => 'permission:permission.create']);
                    $routes->post('update/(:num)', 'PermissionController::update/$1', ['filter' => 'permission:permission.edit']);
                    $routes->post('delete/(:num)', 'PermissionController::delete/$1', ['filter' => 'permission:permission.delete']);
                }
            );

            $routes->group(
                'roles',
                [
                    'filter' => 'permission:role.view'
                ],
                static function ($routes) {
                    $routes->get('/', 'RoleController::index');
                    $routes->get('list', 'RoleController::list');
                    $routes->get('edit/(:num)', 'RoleController::edit/$1');
                    $routes->get('options', 'RoleController::options');
                    $routes->get('permissions/(:num)', 'RoleController::permissions/$1');
                    $routes->get('permissions-data/(:num)', 'RoleController::permissionData/$1');
                    $routes->post('store', 'RoleController::store', ['filter' => 'permission:role.create']);
                    $routes->post('update/(:num)', 'RoleController::update/$1', ['filter' => 'permission:role.edit']);
                    $routes->post('delete/(:num)', 'RoleController::delete/$1', ['filter' => 'permission:role.delete']);
                    $routes->post('permissions/(:num)', 'RoleController::updatePermissions/$1', ['filter' => 'permission:role.permission']);
                }
            );

            $routes->group(
                'employees',
                [
                    'filter' => 'permission:employee.view'
                ],
                static function ($routes) {
                    $routes->get('/', 'EmployeeController::index');
                    $routes->get('list', 'EmployeeController::list');
                    $routes->get('create', 'EmployeeController::create', ['filter' => 'permission:employee.create']);
                    $routes->post('store', 'EmployeeController::store', ['filter' => 'permission:employee.create']);
                    $routes->get('edit/(:num)', 'EmployeeController::editPage/$1', ['filter' => 'permission:employee.edit']);
                    $routes->get('data/(:num)', 'EmployeeController::edit/$1');
                    $routes->post('update/(:num)', 'EmployeeController::update/$1', ['filter' => 'permission:employee.edit']);
                    $routes->post('delete/(:num)', 'EmployeeController::delete/$1', ['filter' => 'permission:employee.delete']);
                    $routes->get('view/(:num)', 'EmployeeController::view/$1', ['filter' => 'permission:employee.view']);
                    $routes->post('toggle-status/(:num)', 'EmployeeController::toggleStatus/$1', ['filter' => 'permission:employee.edit']);
                    $routes->get('details/(:num)', 'EmployeeController::details/$1');
                }
            );

            $routes->group(
                'branches',
                [
                    'filter' => 'permission:branch.view'
                ],
                static function ($routes) {

                    $routes->get(
                        '/',
                        'BranchController::index'
                    );

                    $routes->get(
                        'list',
                        'BranchController::list'
                    );

                    $routes->get(
                        'create',
                        'BranchController::create',
                        [
                            'filter' =>
                            'permission:branch.create'
                        ]
                    );

                    $routes->get(
                        'edit/(:num)',
                        'BranchController::editPage/$1',
                        [
                            'filter' =>
                            'permission:branch.edit'
                        ]
                    );

                    $routes->get(
                        'data/(:num)',
                        'BranchController::edit/$1'
                    );

                    $routes->get(
                        'options',
                        'BranchController::options'
                    );

                    $routes->post(
                        'store',
                        'BranchController::store',
                        [
                            'filter' =>
                            'permission:branch.create'
                        ]
                    );

                    $routes->post(
                        'update/(:num)',
                        'BranchController::update/$1',
                        [
                            'filter' =>
                            'permission:branch.edit'
                        ]
                    );

                    $routes->post(
                        'delete/(:num)',
                        'BranchController::delete/$1',
                        [
                            'filter' =>
                            'permission:branch.delete'
                        ]
                    );
                }
            );
        }
    );
});
