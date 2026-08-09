<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;

class PermissionFilter implements FilterInterface
{
    public function before(
        RequestInterface $request,
        $arguments = null
    ) {

        if (session('is_super')) {
            return;
        }

        if (empty($arguments)) {
            return;
        }

        $permissions = session('permissions') ?? [];

        foreach ($arguments as $permission) {
            if (in_array($permission, $permissions, true)) {
                return;
            }
        }

        if ($request->isAJAX()) {
            return Services::response()
                ->setStatusCode(403)
                ->setJSON([
                    'success' => false,
                    'message' => 'You are not authorized to perform this action.',
                    'data'    => null,
                    'errors'  => []
                ]);
        }

        return redirect()->to('/unauthorized');
    }

    public function after(
        RequestInterface $request,
        ResponseInterface $response,
        $arguments = null
    ) {}
}