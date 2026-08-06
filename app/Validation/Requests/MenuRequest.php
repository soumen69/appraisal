<?php

namespace App\Validation\Requests;

class MenuRequest
{
    public static function rules(?int $id = null): array
    {
        return [

            'module_id' => [
                'label' => 'Module',
                'rules' => 'required|is_natural_no_zero'
            ],

            'parent_id' => [
                'label' => 'Parent Menu',
                'rules' => 'permit_empty|is_natural_no_zero'
            ],

            'title' => [
                'label' => 'Title',
                'rules' => 'required|min_length[2]|max_length[150]'
            ],

            'icon' => [
                'label' => 'Icon',
                'rules' => 'permit_empty|max_length[100]'
            ],

            'route' => [
                'label' => 'Route',
                'rules' => 'permit_empty|max_length[255]'
            ],

            'permission_id' => [
                'label' => 'Permission',
                'rules' => 'permit_empty|is_natural_no_zero'
            ],

            'sort_order' => [
                'label' => 'Sort Order',
                'rules' => 'required|integer'
            ],

            'is_sidebar' => [
                'label' => 'Sidebar',
                'rules' => 'required|in_list[0,1]'
            ],

            'is_visible' => [
                'label' => 'Visible',
                'rules' => 'required|in_list[0,1]'
            ],

            'status' => [
                'label' => 'Status',
                'rules' => 'required|in_list[active,inactive]'
            ]
        ];
    }
}
