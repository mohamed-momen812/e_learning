<?php

return [
    'failed' => 'Validation failed',
    'required' => 'The :attribute field is required',
    'string' => 'The :attribute must be a string',
    'max' => [
        'string' => 'The :attribute must not exceed :max characters',
        'array' => 'The :attribute must not have more than :max items',
    ],
    'min' => [
        'string' => 'The :attribute must be at least :min characters',
        'array' => 'The :attribute must have at least :min items',
    ],
    'email' => 'The :attribute must be a valid email address',
    'unique' => 'The :attribute has already been taken',
    'array' => 'The :attribute must be an array',
    'integer' => 'The :attribute must be an integer',
    'confirmed' => 'The :attribute confirmation does not match',
    'in' => 'The selected :attribute is invalid',
    'exists' => 'The selected :attribute does not exist',
    'boolean' => 'The :attribute field must be true or false',
    'date' => 'The :attribute is not a valid date',
    'date_format' => 'The :attribute does not match the format :format',
    'time' => 'The :attribute is not a valid time',
    'datetime' => 'The :attribute is not a valid datetime',
    'email_address' => 'The :attribute must be a valid email address',
    'url' => 'The :attribute must be a valid URL',
    'ip' => 'The :attribute must be a valid IP address',
    'mac_address' => 'The :attribute must be a valid MAC address',
    'regex' => 'The :attribute format is invalid',
    'image' => 'The :attribute must be an image',
    'mimes' => 'The :attribute must be a file of type: :values',
    'phone' => [
        'egypt' => 'The :attribute must be a valid Egyptian phone number (e.g., 01XXXXXXXXX, 201XXXXXXXXX, or +201XXXXXXXXX).',
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [
        // Common fields
        'name' => 'name',
        'email' => 'email',
        'phone' => 'phone',
        'password' => 'password',
        'avatar' => 'avatar',

        // Role and permission fields
        'roles' => 'roles',
        'roles.*' => 'role',
        'permissions' => 'permissions',
        'permissions.*' => 'permission',
        'label' => 'label',
        'label.en' => 'label (English)',
        'label.ar' => 'label (Arabic)',

        // Display order fields
        'orders' => 'orders',
        'orders.*' => 'order',
        'orders.*.id' => 'ID',
        'orders.*.display_order' => 'display order',
        'display_order' => 'display order',

        // Bulk operation fields
        'ids' => 'IDs',
        'ids.*' => 'ID',
    ],
];
