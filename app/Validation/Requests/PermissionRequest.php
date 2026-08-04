<?php

namespace App\Validation\Requests;

class PermissionRequest
{
    public static function rules($id = null): array
    {
        $slugRule = 'required|max_length[150]|is_unique[permissions.slug]';

        if ($id) {

            $slugRule = "required|max_length[150]|is_unique[permissions.slug,id,{$id}]";

        }

        return [

            'name' => [

                'label' => 'Permission Name',

                'rules' => 'required|max_length[150]'

            ],

            'slug' => [

                'label' => 'Permission Slug',

                'rules' => $slugRule

            ],

            'module' => [

                'label' => 'Module',

                'rules' => 'required|max_length[100]'

            ]

        ];
    }
}