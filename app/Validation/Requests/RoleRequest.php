<?php

namespace App\Validation\Requests;

class RoleRequest
{
    public static function rules($id = null): array
    {
        $nameRule = 'required|max_length[100]|is_unique[roles.name]';
        $slugRule = 'required|max_length[100]|alpha_dash|is_unique[roles.slug]';

        if ($id) {

            $nameRule = "required|max_length[100]|is_unique[roles.name,id,{$id}]";

            $slugRule = "required|max_length[100]|alpha_dash|is_unique[roles.slug,id,{$id}]";

        }

        return [

            'name' => [

                'label' => 'Role Name',

                'rules' => $nameRule

            ],

            'slug' => [

                'label' => 'Slug',

                'rules' => $slugRule

            ],

            'description' => [

                'label' => 'Description',

                'rules' => 'permit_empty|max_length[1000]'

            ],

            'is_system' => [

                'label' => 'System Role',

                'rules' => 'required|in_list[0,1]'

            ]

        ];
    }
}