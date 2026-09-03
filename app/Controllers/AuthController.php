<?php

namespace App\Controllers;

use App\Services\AuthService;

class AuthController extends BaseController
{
    protected AuthService $authService;

    public function __construct()
    {
        $this->authService = new AuthService();
    }

    public function login()
    {
        return view('auth/login', [
            'title' => 'Login'
        ]);
    }

    public function authenticate()
    {
        try {
            $this->authService->login(
                $this->request->getPost('email'),
                $this->request->getPost('password'),
                $this->request->getIPAddress()
            );

            return redirect()->to('/dashboard');
        } catch (\Throwable $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function forgotPassword()
    {
        return view('auth/forgot_password', [
            'title' => 'Forgot Password'
        ]);
    }

    public function resetPassword($token = null)
    {
        return view('auth/reset_password', [
            'title' => 'Reset Password',
            'token' => $token
        ]);
    }

    public function profile()
    {
        return view('auth/profile', [
            'title' => 'Profile'
        ]);
    }

    public function logout()
    {
        $this->authService->logout();
        return redirect()->to('/');
    }
}
