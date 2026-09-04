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


    $routes->group('my-reviews', static function ($routes) {
        $routes->get('/', 'MyReviewController::index');
        $routes->get('list', 'MyReviewController::list');
        $routes->post('(:num)/start', 'MyReviewController::start/$1');
        $routes->get('review/(:num)', 'MyReviewController::review/$1');
        $routes->get('review/(:num)/data', 'MyReviewController::reviewData/$1');
        $routes->post('review/(:num)/save-draft', 'MyReviewController::saveDraft/$1');
        $routes->post('review/(:num)/submit', 'MyReviewController::submit/$1');
    });


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
                    $routes->get('edit/(:num)', 'ModuleController::edit/$1', ['filter' => 'permission:module.edit']);
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
                    $routes->get('options', 'MenuController::options');
                    $routes->get('edit/(:num)', 'MenuController::edit/$1', ['filter' => 'permission:menu.edit']);
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
                    $routes->get('options', 'PermissionController::options');
                    $routes->get('edit/(:num)', 'PermissionController::edit/$1', ['filter' => 'permission:permission.edit']);
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
                    $routes->get('edit/(:num)', 'RoleController::edit/$1', ['filter' => 'permission:role.edit']);
                    // $routes->get('options', 'RoleController::options', ['filter' => 'permission_dependency:employee.create,employee.edit']);
                    $routes->get('permissions/(:num)', 'RoleController::permissions/$1');
                    $routes->get('permissions-data/(:num)', 'RoleController::permissionData/$1');
                    $routes->post('store', 'RoleController::store', ['filter' => 'permission:role.create']);
                    $routes->post('update/(:num)', 'RoleController::update/$1', ['filter' => 'permission:role.edit']);
                    $routes->post('delete/(:num)', 'RoleController::delete/$1', ['filter' => 'permission:role.delete']);
                    $routes->post('permissions/(:num)', 'RoleController::updatePermissions/$1', ['filter' => 'permission:role.permission']);
                }
            );

            $routes->group(
                '',
                [
                    'namespace' => 'App\Controllers\Admin'
                ],
                static function ($routes) {

                    $routes->get(
                        'roles/options',
                        'RoleController::options',
                        [
                            'filter' =>
                            'permission_dependency:employee.create,employee.edit'
                        ]
                    );

                    $routes->get(
                        'organizations/options',
                        'OrganizationController::options',
                        [
                            'filter' =>
                            'permission_dependency:employee.create,employee.edit'
                        ]
                    );

                    $routes->get(
                        'branches/options',
                        'BranchController::options',
                        [
                            'filter' =>
                            'permission_dependency:employee.create,employee.edit'
                        ]
                    );

                    $routes->get(
                        'departments/options',
                        'DepartmentController::options',
                        [
                            'filter' =>
                            'permission_dependency:employee.create,employee.edit'
                        ]
                    );

                    $routes->get(
                        'designations/options',
                        'DesignationController::options',
                        [
                            'filter' =>
                            'permission_dependency:employee.create,employee.edit'
                        ]
                    );

                    $routes->get(
                        'employees/options',
                        'EmployeeController::options',
                        [
                            'filter' =>
                            'permission_dependency:employee.create,employee.edit'
                        ]
                    );
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
                    $routes->get('data/(:num)', 'EmployeeController::edit/$1', ['filter' => 'permission:employee.edit']);
                    $routes->post('update/(:num)', 'EmployeeController::update/$1', ['filter' => 'permission:employee.edit']);
                    $routes->post('delete/(:num)', 'EmployeeController::delete/$1', ['filter' => 'permission:employee.delete']);
                    $routes->get('view/(:num)', 'EmployeeController::view/$1', ['filter' => 'permission:employee.view']);
                    $routes->post('toggle-status/(:num)', 'EmployeeController::toggleStatus/$1', ['filter' => 'permission:employee.edit']);
                    $routes->get('details/(:num)', 'EmployeeController::details/$1');
                    $routes->get(
                        'options',
                        'EmployeeController::options',
                        ['filter' => 'permission_dependency:employee.create,employee.edit']
                    );
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

                    // $routes->get(
                    //     'options',
                    //     'BranchController::options',
                    //     [
                    //         'filter' =>
                    //         'permission_dependency:employee.create,employee.edit'
                    //     ]
                    // );

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

            $routes->group(
                'organizations',
                ['filter' => 'permission:organization.view'],
                static function ($routes) {

                    $routes->get('/', 'OrganizationController::index');

                    $routes->get(
                        'list',
                        'OrganizationController::list'
                    );

                    $routes->get('edit/(:num)', 'OrganizationController::edit/$1', ['filter' => 'permission:organization.edit']);

                    $routes->post(
                        'store',
                        'OrganizationController::store',
                        ['filter' => 'permission:organization.create']
                    );

                    $routes->post(
                        'update/(:num)',
                        'OrganizationController::update/$1',
                        ['filter' => 'permission:organization.edit']
                    );

                    $routes->post(
                        'delete/(:num)',
                        'OrganizationController::delete/$1',
                        ['filter' => 'permission:organization.delete']
                    );

                    $routes->post(
                        'toggle-status/(:num)',
                        'OrganizationController::toggleStatus/$1',
                        ['filter' => 'permission:organization.edit']
                    );
                    // $routes->get(
                    //     'options',
                    //     'OrganizationController::options',
                    //     [
                    //         'filter' =>
                    //         'permission_dependency:employee.create,employee.edit'
                    //     ]
                    // );
                }
            );

            $routes->group(
                'departments',
                ['filter' => 'permission:department.view'],
                static function ($routes) {
                    $routes->get('/', 'DepartmentController::index');
                    $routes->get('list', 'DepartmentController::list');
                    $routes->get('edit/(:num)', 'DepartmentController::edit/$1', ['filter' => 'permission:department.edit']);

                    $routes->post(
                        'store',
                        'DepartmentController::store',
                        ['filter' => 'permission:department.create']
                    );

                    $routes->post(
                        'update/(:num)',
                        'DepartmentController::update/$1',
                        ['filter' => 'permission:department.edit']
                    );

                    $routes->post(
                        'delete/(:num)',
                        'DepartmentController::delete/$1',
                        ['filter' => 'permission:department.delete']
                    );

                    $routes->post(
                        'toggle-status/(:num)',
                        'DepartmentController::toggleStatus/$1',
                        ['filter' => 'permission:department.edit']
                    );

                    $routes->get(
                        'group/(:any)',
                        'DepartmentController::group/$1'
                    );
                    // $routes->get(
                    //     'options',
                    //     'DepartmentController::options',
                    //     [
                    //         'filter' =>
                    //         'permission_dependency:employee.create,employee.edit'
                    //     ]
                    // );
                }
            );

            $routes->group(
                'designations',
                ['filter' => 'permission:designation.view'],
                static function ($routes) {
                    $routes->get('/', 'DesignationController::index');
                    $routes->get('list', 'DesignationController::list');
                    $routes->get('edit/(:num)', 'DesignationController::edit/$1', ['filter' => 'permission:designation.edit']);
                    $routes->post('store', 'DesignationController::store', ['filter' => 'permission:designation.create']);
                    $routes->post('update/(:num)', 'DesignationController::update/$1', ['filter' => 'permission:designation.edit']);
                    $routes->post('delete/(:num)', 'DesignationController::delete/$1', ['filter' => 'permission:designation.delete']);
                    $routes->post('toggle-status/(:num)', 'DesignationController::toggleStatus/$1', ['filter' => 'permission:designation.edit']);
                    $routes->get('group/(:any)', 'DesignationController::group/$1');
                    // $routes->get(
                    //     'options',
                    //     'DesignationController::options',
                    //     [
                    //         'filter' =>
                    //         'permission_dependency:employee.create,employee.edit'
                    //     ]
                    // );
                }
            );

            $routes->group(
                'cycles',
                [
                    'filter' =>
                    'permission:appraisal_cycle.view'
                ],
                static function ($routes) {

                    $routes->get('/', 'AppraisalCycleController::index');
                    $routes->get('list', 'AppraisalCycleController::list');
                    $routes->get(
                        'edit/(:num)',
                        'AppraisalCycleController::edit/$1',
                        [
                            'filter' =>
                            'permission:appraisal_cycle.edit'
                        ]
                    );

                    $routes->post(
                        'store',
                        'AppraisalCycleController::store',
                        [
                            'filter' =>
                            'permission:appraisal_cycle.create'
                        ]
                    );

                    $routes->post(
                        'update/(:num)',
                        'AppraisalCycleController::update/$1',
                        [
                            'filter' =>
                            'permission:appraisal_cycle.edit'
                        ]
                    );

                    $routes->post(
                        'delete/(:num)',
                        'AppraisalCycleController::delete/$1',
                        [
                            'filter' =>
                            'permission:appraisal_cycle.delete'
                        ]
                    );
                }
            );

            $routes->group(
                'templates',
                [
                    'filter' =>
                    'permission:appraisal_template.view'
                ],
                static function ($routes) {

                    $routes->get(
                        '/',
                        'AppraisalTemplateController::index'
                    );

                    $routes->get(
                        'list',
                        'AppraisalTemplateController::list'
                    );

                    $routes->get(
                        'edit/(:num)',
                        'AppraisalTemplateController::edit/$1',
                        [
                            'filter' =>
                            'permission:appraisal_template.edit'
                        ]
                    );

                    $routes->post(
                        'store',
                        'AppraisalTemplateController::store',
                        [
                            'filter' =>
                            'permission:appraisal_template.create'
                        ]
                    );

                    $routes->post(
                        'update/(:num)',
                        'AppraisalTemplateController::update/$1',
                        [
                            'filter' =>
                            'permission:appraisal_template.edit'
                        ]
                    );

                    $routes->post(
                        'delete/(:num)',
                        'AppraisalTemplateController::delete/$1',
                        [
                            'filter' =>
                            'permission:appraisal_template.delete'
                        ]
                    );

                    $routes->get(
                        '(:num)/builder',
                        'AppraisalTemplateController::builder/$1',
                        [
                            'filter' =>
                            'permission:appraisal_template.edit'
                        ]
                    );

                    $routes->get(
                        '(:num)/builder-data',
                        'AppraisalTemplateController::builderData/$1',
                        [
                            'filter' =>
                            'permission:appraisal_template.edit'
                        ]
                    );

                    $routes->post(
                        '(:num)/sections',
                        'AppraisalTemplateController::storeSection/$1',
                        [
                            'filter' =>
                            'permission:appraisal_template.edit'
                        ]
                    );

                    $routes->post(
                        'sections/(:num)/update',
                        'AppraisalTemplateController::updateSection/$1',
                        [
                            'filter' =>
                            'permission:appraisal_template.edit'
                        ]
                    );

                    $routes->post(
                        'sections/(:num)/delete',
                        'AppraisalTemplateController::deleteSection/$1',
                        [
                            'filter' =>
                            'permission:appraisal_template.edit'
                        ]
                    );

                    $routes->post(
                        '(:num)/sections/reorder',
                        'AppraisalTemplateController::reorderSections/$1',
                        [
                            'filter' =>
                            'permission:appraisal_template.edit'
                        ]
                    );

                    $routes->post(
                        'sections/(:num)/questions',
                        'AppraisalTemplateController::storeQuestion/$1',
                        [
                            'filter' =>
                            'permission:appraisal_template.edit'
                        ]
                    );

                    $routes->post(
                        'questions/(:num)/update',
                        'AppraisalTemplateController::updateQuestion/$1',
                        [
                            'filter' =>
                            'permission:appraisal_template.edit'
                        ]
                    );

                    $routes->post(
                        'questions/(:num)/delete',
                        'AppraisalTemplateController::deleteQuestion/$1',
                        [
                            'filter' =>
                            'permission:appraisal_template.edit'
                        ]
                    );

                    $routes->post(
                        'sections/(:num)/questions/reorder',
                        'AppraisalTemplateController::reorderQuestions/$1',
                        [
                            'filter' =>
                            'permission:appraisal_template.edit'
                        ]
                    );
                    $routes->get(
                        'options',
                        'AppraisalTemplateController::options'
                    );
                }
            );

            $routes->group(
                'review-matrix',
                [
                    'filter' => 'permission:review_matrix.view'
                ],
                static function ($routes) {

                    $routes->get('/', 'ReviewMatrixController::index');

                    $routes->get(
                        'list',
                        'ReviewMatrixController::list'
                    );

                    $routes->get(
                        'edit/(:num)',
                        'ReviewMatrixController::edit/$1'
                    );

                    $routes->post(
                        'store',
                        'ReviewMatrixController::store',
                        [
                            'filter' =>
                            'permission:review_matrix.create'
                        ]
                    );

                    $routes->post(
                        'update/(\:num)',
                        'ReviewMatrixController::update/$1',
                        [
                            'filter' =>
                            'permission:review_matrix.edit'
                        ]
                    );

                    $routes->post(
                        'delete/(\:num)',
                        'ReviewMatrixController::delete/$1',
                        [
                            'filter' =>
                            'permission:review_matrix.delete'
                        ]
                    );
                    $routes->get(
                        'view/(:num)',
                        'ReviewMatrixController::view/$1'
                    );
                }
            );

            $routes->group(
                'rating-scales',
                [
                    'filter' => 'permission:rating_scale.view'
                ],
                static function ($routes) {

                    $routes->get(
                        '/',
                        'RatingScaleController::index'
                    );

                    $routes->get(
                        'list',
                        'RatingScaleController::list'
                    );

                    $routes->get(
                        'edit/(:num)',
                        'RatingScaleController::edit/$1',
                        [
                            'filter' => 'permission:rating_scale.edit'
                        ]
                    );

                    $routes->post(
                        'store',
                        'RatingScaleController::store',
                        [
                            'filter' => 'permission:rating_scale.create'
                        ]
                    );

                    $routes->post(
                        'update/(:num)',
                        'RatingScaleController::update/$1',
                        [
                            'filter' => 'permission:rating_scale.edit'
                        ]
                    );

                    $routes->post(
                        'delete/(:num)',
                        'RatingScaleController::delete/$1',
                        [
                            'filter' => 'permission:rating_scale.delete'
                        ]
                    );
                }
            );

            $routes->group('appraisal/cycles', static function ($routes) {

                // ==========================================
                // Participants
                // ==========================================

                $routes->get(
                    '(:num)/participants',
                    'AppraisalCycleParticipantController::index/$1'
                );

                $routes->get(
                    '(:num)/participants/list',
                    'AppraisalCycleParticipantController::list/$1'
                );

                $routes->get(
                    '(:num)/participants/available-employees',
                    'AppraisalCycleParticipantController::availableEmployees/$1'
                );

                $routes->post(
                    '(:num)/participants',
                    'AppraisalCycleParticipantController::store/$1'
                );

                $routes->post(
                    '(:num)/participants/bulk',
                    'AppraisalCycleParticipantController::bulkStore/$1'
                );

                $routes->post(
                    '(:num)/participants/(:num)/update',
                    'AppraisalCycleParticipantController::update/$2'
                );

                $routes->post(
                    '(:num)/participants/(:num)/delete',
                    'AppraisalCycleParticipantController::delete/$2'
                );


                // ==========================================
                // Template Assignments
                // ==========================================

                $routes->get(
                    '(:num)/template-assignments',
                    'AppraisalCycleTemplateAssignmentController::index/$1'
                );

                $routes->get(
                    '(:num)/template-assignments/list',
                    'AppraisalCycleTemplateAssignmentController::list/$1'
                );

                $routes->get(
                    '(:num)/template-assignments/options',
                    'AppraisalCycleTemplateAssignmentController::options/$1'
                );

                $routes->post(
                    '(:num)/template-assignments',
                    'AppraisalCycleTemplateAssignmentController::store/$1'
                );

                $routes->post(
                    '(:num)/template-assignments/(:num)/update',
                    'AppraisalCycleTemplateAssignmentController::update/$2'
                );

                $routes->post(
                    '(:num)/template-assignments/(:num)/delete',
                    'AppraisalCycleTemplateAssignmentController::delete/$2'
                );
            });
        }
    );
});
