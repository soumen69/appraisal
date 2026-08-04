<?php

namespace App\Services;

use App\Models\Admin\MenuModel;

class SidebarService
{
    protected MenuModel $menuModel;

    public function __construct()
    {
        $this->menuModel = new MenuModel();
    }

    public function getSidebar(array $permissions): array
    {
        $menus = $this->menuModel

            ->where('status','active')

            ->orderBy('sort_order','ASC')

            ->findAll();

        $sidebar = [];

        foreach ($menus as $menu){

            if(

                empty($menu['permission_slug'])

                ||

                in_array(

                    $menu['permission_slug'],

                    $permissions

                )

            ){

                $sidebar[] = $menu;

            }

        }

        return $sidebar;
    }

}