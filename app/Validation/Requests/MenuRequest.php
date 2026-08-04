<?php

namespace App\Validation\Requests;

class MenuRequest
{
    public static function rules(): array
    {
        return [

            'module_id' => [

                'label' => 'Module',

                'rules' => 'required|integer'

            ],

            'title' => [

                'label' => 'Title',

                'rules' => 'required|max_length[150]'

            ],

            'icon' => [

                'label' => 'Icon',

                'rules' => 'permit_empty|max_length[100]'

            ],

            'route' => [

                'label' => 'Route',

                'rules' => 'permit_empty|max_length[255]'

            ],

            'permission_slug' => [

                'label' => 'Permission',

                'rules' => 'permit_empty|max_length[150]'

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