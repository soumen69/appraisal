<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;

class PermissionFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (!session('is_logged_in')) {
            return redirect()->to('/login');
        }

        if ((bool) session('is_super')) {
            return;
        }

        if (empty($arguments)) {
            return $this->deny($request);
        }

        $permissions = session('permissions') ?? [];

        if (in_array('*', $permissions, true)) {
            return;
        }

        foreach ($arguments as $permission) {
            if (in_array((string) $permission, $permissions, true)) {
                return;
            }
        }

        return $this->deny($request);
    }

    protected function deny(RequestInterface $request)
    {
        if ($this->isAjax($request)) {
            return Services::response()
                ->setStatusCode(403)
                ->setJSON([
                    'success' => false,
                    'message' => 'You are not authorized to perform this action.',
                    'data' => null,
                    'errors' => []
                ]);
        }

        return redirect()->to('/unauthorized');
    }

    protected function isAjax(RequestInterface $request): bool
    {
        return strtolower($request->getHeaderLine('X-Requested-With')) === 'xmlhttprequest';
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null) {}
}
