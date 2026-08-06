<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

abstract class BaseCrudController extends BaseController
{
    protected function success(
        string $message = '',
        $data = null,
        int $statusCode = ResponseInterface::HTTP_OK
    ) {
        return $this->response
            ->setStatusCode($statusCode)
            ->setJSON([
                'success' => true,
                'message' => $message,
                'data' => $data,
                'errors' => null
            ]);
    }

    protected function error(
        string $message,
        array $errors = [],
        int $statusCode = ResponseInterface::HTTP_BAD_REQUEST
    ) {
        return $this->response
            ->setStatusCode($statusCode)
            ->setJSON([
                'success' => false,
                'message' => $message,
                'data' => null,
                'errors' => $errors
            ]);
    }

    protected function validationFailed()
    {
        return $this->error(
            'Validation failed.',
            $this->validator->getErrors(),
            ResponseInterface::HTTP_UNPROCESSABLE_ENTITY
        );
    }
}
