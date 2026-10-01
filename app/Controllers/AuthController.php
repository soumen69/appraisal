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
            'title' => 'Login',
        ]);
    }

    public function requestOtp()
    {
        try {
            $email = (string) $this->request->getPost('email');

            $this->authService->requestOtp($email);

            return redirect()->to('/login')
                ->with('success', 'If an active account exists for this email, a login code will be sent.')
                ->with('otp_email', trim($email));
        } catch (\Throwable $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Step 2: Verify the submitted OTP and log the user in.
     */
    public function verifyOtp()
    {
        try {
            $email = (string) $this->request->getPost('email');
            $otp = (string) $this->request->getPost('otp');

            $this->authService->verifyOtp(
                $email,
                $otp,
                $this->request->getIPAddress()
            );

            return redirect()->to('/dashboard');
        } catch (\Throwable $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    public function profile()
    {
        return view('auth/profile', [
            'title' => 'Profile',
        ]);
    }

    public function logout()
    {
        $this->authService->logout();

        return redirect()->to('/');
    }
}
