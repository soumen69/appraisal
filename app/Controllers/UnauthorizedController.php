<?php

namespace App\Controllers;

class UnauthorizedController extends BaseController
{
    public function index()
    {
        return view('errors/unauthorized', [
            'title' => 'Unauthorized'
        ]);
    }
}
