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

            'module_id' => [
                'label' => 'Module',
                'rules' => 'required|integer'
            ],

            'name' => [
                'label' => 'Permission Name',
                'rules' => 'required|min_length[3]|max_length[150]'
            ],

            'slug' => [
                'label' => 'Slug',
                'rules' => $slugRule
            ]

        ];
    }
}
