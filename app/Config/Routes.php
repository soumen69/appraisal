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
    $routes->get('login', 'AuthController::login');
    $routes->post('login/request-otp', 'AuthController::requestOtp');
    $routes->post('login/verify-otp', 'AuthController::verifyOtp');
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

    $routes->group('', ['namespace' => 'App\Controllers\Admin'], static function ($routes) {
        // Roles
        $routes->group('roles', ['filter' => 'permission:role.view'], static function ($routes) {
            $routes->get('/', 'RoleController::index');
            $routes->get('list', 'RoleController::list');
            $routes->get('edit/(:num)', 'RoleController::edit/$1', ['filter' => 'permission:role.edit']);
            $routes->get('permissions/(:num)', 'RoleController::permissions/$1', ['filter' => 'permission:role.permission']);
            $routes->get('permissions-data/(:num)', 'RoleController::permissionData/$1', ['filter' => 'permission:role.permission']);
            $routes->post('store', 'RoleController::store', ['filter' => 'permission:role.create']);
            $routes->post('update/(:num)', 'RoleController::update/$1', ['filter' => 'permission:role.edit']);
            $routes->post('delete/(:num)', 'RoleController::delete/$1', ['filter' => 'permission:role.delete']);
            $routes->post('permissions/(:num)', 'RoleController::updatePermissions/$1', ['filter' => 'permission:role.permission']);
        });


        // Supporting Options :

        // These endpoints provide lookup data required by Employee create/edit
        // forms. They do not grant access to manage the related resources.

        $routes->get('roles/options', 'RoleController::options', [
            'filter' => 'permission_dependency:employee.create,employee.edit'
        ]);

        $routes->get('organizations/options', 'OrganizationController::options', [
            'filter' => 'permission_dependency:employee.create,employee.edit'
        ]);

        $routes->get('branches/options', 'BranchController::options', [
            'filter' => 'permission_dependency:employee.create,employee.edit'
        ]);

        $routes->get('departments/options', 'DepartmentController::options', [
            'filter' => 'permission_dependency:employee.create,employee.edit'
        ]);

        $routes->get('designations/options', 'DesignationController::options', [
            'filter' => 'permission_dependency:employee.create,employee.edit'
        ]);

        $routes->get('employees/options', 'EmployeeController::options', [
            'filter' => 'permission_dependency:employee.create,employee.edit'
        ]);


        // Employees
        $routes->group('employees', ['filter' => 'permission:employee.view'], static function ($routes) {
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
            $routes->get('details/(:num)', 'EmployeeController::details/$1', ['filter' => 'permission:employee.view']);
            $routes->get('options', 'EmployeeController::options', ['filter' => 'permission_dependency:employee.create,employee.edit']);
        });


        // Branches
        $routes->group('branches', ['filter' => 'permission:branch.view'], static function ($routes) {
            $routes->get('/', 'BranchController::index');
            $routes->get('list', 'BranchController::list');
            $routes->get('create', 'BranchController::create', ['filter' => 'permission:branch.create']);
            $routes->get('edit/(:num)', 'BranchController::editPage/$1', ['filter' => 'permission:branch.edit']);
            $routes->get('data/(:num)', 'BranchController::edit/$1');
            $routes->post('store', 'BranchController::store', ['filter' => 'permission:branch.create']);
            $routes->post('update/(:num)', 'BranchController::update/$1', ['filter' => 'permission:branch.edit']);
            $routes->post('delete/(:num)', 'BranchController::delete/$1', ['filter' => 'permission:branch.delete']);
        });

        // Organizations
        $routes->group('organizations', ['filter' => 'permission:organization.view'], static function ($routes) {
            $routes->get('/', 'OrganizationController::index');
            $routes->get('list', 'OrganizationController::list');
            $routes->get('edit/(:num)', 'OrganizationController::edit/$1', ['filter' => 'permission:organization.edit']);
            $routes->post('store', 'OrganizationController::store', ['filter' => 'permission:organization.create']);
            $routes->post('update/(:num)', 'OrganizationController::update/$1', ['filter' => 'permission:organization.edit']);
            $routes->post('delete/(:num)', 'OrganizationController::delete/$1', ['filter' => 'permission:organization.delete']);
            $routes->post('toggle-status/(:num)', 'OrganizationController::toggleStatus/$1', ['filter' => 'permission:organization.edit']);
        });


        // Departments
        $routes->group('departments', ['filter' => 'permission:department.view'], static function ($routes) {
            $routes->get('/', 'DepartmentController::index');
            $routes->get('list', 'DepartmentController::list');
            $routes->get('edit/(:num)', 'DepartmentController::edit/$1', ['filter' => 'permission:department.edit']);
            $routes->post('store', 'DepartmentController::store', ['filter' => 'permission:department.create']);
            $routes->post('update/(:num)', 'DepartmentController::update/$1', ['filter' => 'permission:department.edit']);
            $routes->post('delete/(:num)', 'DepartmentController::delete/$1', ['filter' => 'permission:department.delete']);
            $routes->post('toggle-status/(:num)', 'DepartmentController::toggleStatus/$1', ['filter' => 'permission:department.edit']);
            $routes->get('group/(:any)', 'DepartmentController::group/$1');
        });


        // Designations
        $routes->group('designations', ['filter' => 'permission:designation.view'], static function ($routes) {
            $routes->get('/', 'DesignationController::index');
            $routes->get('list', 'DesignationController::list');
            $routes->get('edit/(:num)', 'DesignationController::edit/$1', ['filter' => 'permission:designation.edit']);
            $routes->post('store', 'DesignationController::store', ['filter' => 'permission:designation.create']);
            $routes->post('update/(:num)', 'DesignationController::update/$1', ['filter' => 'permission:designation.edit']);
            $routes->post('delete/(:num)', 'DesignationController::delete/$1', ['filter' => 'permission:designation.delete']);
            $routes->post('toggle-status/(:num)', 'DesignationController::toggleStatus/$1', ['filter' => 'permission:designation.edit']);
            $routes->get('group/(:any)', 'DesignationController::group/$1');
        });


        // Appraisal Cycles
        $routes->group('cycles', ['filter' => 'permission:appraisal_cycle.view'], static function ($routes) {
            $routes->get('/', 'AppraisalCycleController::index');
            $routes->get('list', 'AppraisalCycleController::list');
            $routes->get('edit/(:num)', 'AppraisalCycleController::edit/$1', ['filter' => 'permission:appraisal_cycle.edit']);
            $routes->post('store', 'AppraisalCycleController::store', ['filter' => 'permission:appraisal_cycle.create']);
            $routes->post('update/(:num)', 'AppraisalCycleController::update/$1', ['filter' => 'permission:appraisal_cycle.edit']);
            $routes->post('delete/(:num)', 'AppraisalCycleController::delete/$1', ['filter' => 'permission:appraisal_cycle.delete']);
        });


        // Appraisal Templates
        $routes->group('templates', ['filter' => 'permission:appraisal_template.view'], static function ($routes) {
            $routes->get('/', 'AppraisalTemplateController::index');
            $routes->get('list', 'AppraisalTemplateController::list');
            $routes->get('edit/(:num)', 'AppraisalTemplateController::edit/$1', ['filter' => 'permission:appraisal_template.edit']);
            $routes->post('store', 'AppraisalTemplateController::store', ['filter' => 'permission:appraisal_template.create']);
            $routes->post('update/(:num)', 'AppraisalTemplateController::update/$1', ['filter' => 'permission:appraisal_template.edit']);
            $routes->post('delete/(:num)', 'AppraisalTemplateController::delete/$1', ['filter' => 'permission:appraisal_template.delete']);
            $routes->get('(:num)/builder', 'AppraisalTemplateController::builder/$1', ['filter' => 'permission:appraisal_template.edit']);
            $routes->get('(:num)/builder-data', 'AppraisalTemplateController::builderData/$1', ['filter' => 'permission:appraisal_template.edit']);
            $routes->post('(:num)/sections', 'AppraisalTemplateController::storeSection/$1', ['filter' => 'permission:appraisal_template.edit']);
            $routes->post('sections/(:num)/update', 'AppraisalTemplateController::updateSection/$1', ['filter' => 'permission:appraisal_template.edit']);
            $routes->post('sections/(:num)/delete', 'AppraisalTemplateController::deleteSection/$1', ['filter' => 'permission:appraisal_template.edit']);
            $routes->post('(:num)/sections/reorder', 'AppraisalTemplateController::reorderSections/$1', ['filter' => 'permission:appraisal_template.edit']);
            $routes->post('sections/(:num)/questions', 'AppraisalTemplateController::storeQuestion/$1', ['filter' => 'permission:appraisal_template.edit']);
            $routes->post('questions/(:num)/update', 'AppraisalTemplateController::updateQuestion/$1', ['filter' => 'permission:appraisal_template.edit']);
            $routes->post('questions/(:num)/delete', 'AppraisalTemplateController::deleteQuestion/$1', ['filter' => 'permission:appraisal_template.edit']);
            $routes->post('sections/(:num)/questions/reorder', 'AppraisalTemplateController::reorderQuestions/$1', ['filter' => 'permission:appraisal_template.edit']);
            $routes->get('options', 'AppraisalTemplateController::options');
        });


        // Review Matrix
        $routes->group('review-matrix', ['filter' => 'permission:review_matrix.view'], static function ($routes) {
            $routes->get('/', 'ReviewMatrixController::index');
            $routes->get('list', 'ReviewMatrixController::list');
            $routes->get('edit/(:num)', 'ReviewMatrixController::edit/$1', ['filter' => 'permission:review_matrix.edit']);
            $routes->get('view/(:num)', 'ReviewMatrixController::view/$1', ['filter' => 'permission:review_matrix.view']);
            $routes->post('store', 'ReviewMatrixController::store', ['filter' => 'permission:review_matrix.create']);
            $routes->post('update/(:num)', 'ReviewMatrixController::update/$1', ['filter' => 'permission:review_matrix.edit']);
            $routes->post('delete/(:num)', 'ReviewMatrixController::delete/$1', ['filter' => 'permission:review_matrix.delete']);
        });


        // Appraisal Reviews
        $routes->group('reviews', ['filter' => 'permission:appraisal_review.view'], static function ($routes) {
            $routes->get('/', 'ReviewController::index');
            $routes->get('list', 'ReviewController::list');
            $routes->get('cycles', 'ReviewController::cycles');
            $routes->get('view/(:num)', 'ReviewController::view/$1');
            $routes->get('view/(:num)/data', 'ReviewController::data/$1');
            $routes->post('calculate-final', 'ReviewController::calculateFinalScore');
        });


        // Appraisal Cycle Details
        $routes->group('appraisal/cycles', ['filter' => 'permission:appraisal_cycle.view'], static function ($routes) {
            $routes->get('(:num)/participants', 'AppraisalCycleParticipantController::index/$1');
            $routes->get('(:num)/participants/list', 'AppraisalCycleParticipantController::list/$1');
            $routes->get('(:num)/participants/available-employees', 'AppraisalCycleParticipantController::availableEmployees/$1');
            $routes->post('(:num)/participants', 'AppraisalCycleParticipantController::store/$1', ['filter' => 'permission:appraisal_cycle.edit']);
            $routes->post('(:num)/participants/bulk', 'AppraisalCycleParticipantController::bulkStore/$1', ['filter' => 'permission:appraisal_cycle.edit']);
            $routes->post('(:num)/participants/(:num)/update', 'AppraisalCycleParticipantController::update/$2', ['filter' => 'permission:appraisal_cycle.edit']);
            $routes->post('(:num)/participants/(:num)/delete', 'AppraisalCycleParticipantController::delete/$2', ['filter' => 'permission:appraisal_cycle.edit']);
            $routes->get('(:num)/template-assignments', 'AppraisalCycleTemplateAssignmentController::index/$1');
            $routes->get('(:num)/template-assignments/list', 'AppraisalCycleTemplateAssignmentController::list/$1');
            $routes->get('(:num)/template-assignments/options', 'AppraisalCycleTemplateAssignmentController::options/$1');
            $routes->post('(:num)/template-assignments', 'AppraisalCycleTemplateAssignmentController::store/$1', ['filter' => 'permission:appraisal_cycle.edit']);
            $routes->post('(:num)/template-assignments/(:num)/update', 'AppraisalCycleTemplateAssignmentController::update/$1/$2', ['filter' => 'permission:appraisal_cycle.edit']);
            $routes->post('(:num)/template-assignments/(:num)/delete', 'AppraisalCycleTemplateAssignmentController::delete/$1/$2', ['filter' => 'permission:appraisal_cycle.edit']);
        });


        // Settings
        $routes->group('settings', ['filter' => 'permission:settings.view'], static function ($routes) {
            $routes->get('/', 'SettingsController::index');
            $routes->post('get', 'SettingsController::getSettings', ['filter' => 'permission:settings.view']);
            $routes->post('save', 'SettingsController::saveSettings', ['filter' => 'permission:settings.edit']);
        });
    });
});
