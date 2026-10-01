<?php

namespace App\Validation\Requests;

class RoleRequest
{
    public static function rules($id = null): array
    {
        return [
            'name' => [
                'label' => 'Role Name',
                'rules' => 'required|min_length[2]|max_length[100]'
            ],
            'slug' => [
                'label' => 'Slug',
                'rules' => 'required|max_length[100]|alpha_dash'
            ],
            'display_name' => [
                'label' => 'Display Name',
                'rules' => 'permit_empty|max_length[150]'
            ],
            'icon' => [
                'label' => 'Icon',
                'rules' => 'permit_empty|max_length[100]'
            ],
            'color' => [
                'label' => 'Color',
                'rules' => 'permit_empty|max_length[30]'
            ],
            'sort_order' => [
                'label' => 'Sort Order',
                'rules' => 'required|integer'
            ],
            'status' => [
                'label' => 'Status',
                'rules' => 'required|in_list[active,inactive]'
            ],
            'description' => [
                'label' => 'Description',
                'rules' => 'permit_empty|max_length[1000]'
            ]
        ];
    }
}
