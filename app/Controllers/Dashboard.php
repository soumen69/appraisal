<?php

namespace App\Controllers;

class Dashboard extends BaseController
{
    public function index()
    {
        $data = [
            'title'          => 'Dashboard',
            'page_title'     => 'Dashboard',
            'page_subtitle'  => 'Welcome back, Super Admin'
        ];

        return view('dashboard/index', $data);
    }
}
