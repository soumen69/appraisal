<?php

namespace App\Controllers;

class AuthController extends BaseController
{
    public function login()
    {
        return view('auth/login', [
            'title' => 'Login'
        ]);
    }

    public function authenticate()
    {

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

    }
}