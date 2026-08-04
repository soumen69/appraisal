<?php

namespace App\Validation\Requests;

class ModuleRequest
{
    public static function rules($id = null): array
    {
        $slugRule = 'required|max_length[150]|alpha_dash|is_unique[modules.slug]';

        if ($id) {
            $slugRule = "required|max_length[150]|alpha_dash|is_unique[modules.slug,id,{$id}]";
        }

        return [

            'name' => [
                'label' => 'Module Name',
                'rules' => 'required|min_length[2]|max_length[150]'
            ],

            'slug' => [
                'label' => 'Slug',
                'rules' => $slugRule
            ],

            'icon' => [
                'label' => 'Icon',
                'rules' => 'permit_empty|max_length[100]'
            ],

            'route' => [
                'label' => 'Route',
                'rules' => 'permit_empty|max_length[255]'
            ],

            'description' => [
                'label' => 'Description',
                'rules' => 'permit_empty|max_length[1000]'
            ],

            'sort_order' => [
                'label' => 'Sort Order',
                'rules' => 'required|integer'
            ],

            'status' => [
                'label' => 'Status',
                'rules' => 'required|in_list[active,inactive]'
            ]

        ];
    }
}