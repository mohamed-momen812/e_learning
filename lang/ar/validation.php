<?php

return [
    'failed' => 'فشل التحقق من البيانات',
    'required' => 'حقل :attribute مطلوب',
    'string' => 'يجب أن يكون :attribute نص',
    'max' => [
        'string' => 'يجب ألا يتجاوز :attribute :max حرف',
        'array' => 'يجب ألا يحتوي :attribute على أكثر من :max عنصر',
    ],
    'min' => [
        'string' => 'يجب ألا يقل :attribute عن :min حرف',
        'array' => 'يجب أن يحتوي :attribute على :min عنصر على الأقل',
    ],
    'email' => 'يجب أن يكون :attribute بريد إلكتروني صحيح',
    'unique' => 'قيمة :attribute مستخدمة بالفعل',
    'array' => 'يجب أن يكون :attribute مصفوفة',
    'integer' => 'يجب أن يكون :attribute رقماً صحيحاً',
    'confirmed' => 'تأكيد :attribute غير متطابق',
    'in' => 'القيمة المحددة لـ :attribute غير صالحة',
    'exists' => 'القيمة المحددة لـ :attribute غير موجودة',
    'boolean' => 'يجب أن يكون حقل :attribute صحيح أو خطأ',
    'date' => 'قيمة :attribute ليست تاريخاً صحيحاً',
    'date_format' => 'قيمة :attribute لا تطابق التنسيق :format',
    'time' => 'قيمة :attribute ليست وقتاً صحيحاً',
    'datetime' => 'قيمة :attribute ليست تاريخ ووقت صحيح',
    'email_address' => 'يجب أن يكون :attribute بريد إلكتروني صحيح',
    'url' => 'يجب أن يكون :attribute رابط صحيح',
    'ip' => 'يجب أن يكون :attribute عنوان IP صحيح',
    'mac_address' => 'يجب أن يكون :attribute عنوان MAC صحيح',
    'regex' => 'تنسيق :attribute غير صالح',
    'image' => 'يجب أن يكون :attribute صورة',
    'mimes' => 'يجب أن يكون :attribute ملف من نوع: :values',
    'phone' => [
        'egypt' => 'يجب أن يكون :attribute رقم هاتف مصري صحيح (مثل: 01XXXXXXXXX أو 201XXXXXXXXX أو +201XXXXXXXXX).',
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
        'name' => 'الاسم',
        'email' => 'البريد الإلكتروني',
        'phone' => 'الهاتف',
        'password' => 'كلمة المرور',
        'avatar' => 'الصورة الشخصية',

        // Role and permission fields
        'roles' => 'الأدوار',
        'roles.*' => 'الدور',
        'permissions' => 'الصلاحيات',
        'permissions.*' => 'الصلاحية',
        'label' => 'التسمية',
        'label.en' => 'التسمية (الإنجليزية)',
        'label.ar' => 'التسمية (العربية)',

        // Display order fields
        'orders' => 'الترتيبات',
        'orders.*' => 'الترتيب',
        'orders.*.id' => 'المعرف',
        'orders.*.display_order' => 'ترتيب العرض',
        'display_order' => 'ترتيب العرض',

        // Bulk operation fields
        'ids' => 'المعرفات',
        'ids.*' => 'المعرف',
    ],
];
