<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Force the default guard to 'web' for this seeder
        $registrar = app()[\Spatie\Permission\PermissionRegistrar::class];
        $registrar->setPermissionsTeamId(null);

        // Reset cached roles and permissions
        $registrar->forgetCachedPermissions();

        // Create permissions with labels
        $permissions = [
            // Course permissions
            'courses.create' => ['en' => 'Create Courses', 'ar' => 'إنشاء الدورات'],
            'courses.view' => ['en' => 'View Courses', 'ar' => 'عرض الدورات'],
            'courses.update' => ['en' => 'Update Courses', 'ar' => 'تحديث الدورات'],
            'courses.delete' => ['en' => 'Delete Courses', 'ar' => 'حذف الدورات'],
            'courses.publish' => ['en' => 'Publish Courses', 'ar' => 'نشر الدورات'],

            // Lesson permissions
            'lessons.create' => ['en' => 'Create Lessons', 'ar' => 'إنشاء الدروس'],
            'lessons.view' => ['en' => 'View Lessons', 'ar' => 'عرض الدروس'],
            'lessons.update' => ['en' => 'Update Lessons', 'ar' => 'تحديث الدروس'],
            'lessons.delete' => ['en' => 'Delete Lessons', 'ar' => 'حذف الدروس'],

            // Student permissions
            'students.create' => ['en' => 'Create Students', 'ar' => 'إنشاء الطلاب'],
            'students.view' => ['en' => 'View Students', 'ar' => 'عرض الطلاب'],
            'students.update' => ['en' => 'Update Students', 'ar' => 'تحديث الطلاب'],
            'students.delete' => ['en' => 'Delete Students', 'ar' => 'حذف الطلاب'],

            // Enrollment permissions
            'enrollments.create' => ['en' => 'Create Enrollments', 'ar' => 'إنشاء التسجيلات'],
            'enrollments.view' => ['en' => 'View Enrollments', 'ar' => 'عرض التسجيلات'],
            'enrollments.update' => ['en' => 'Update Enrollments', 'ar' => 'تحديث التسجيلات'],
            'enrollments.delete' => ['en' => 'Delete Enrollments', 'ar' => 'حذف التسجيلات'],

            // Exam permissions
            'exams.create' => ['en' => 'Create Exams', 'ar' => 'إنشاء الامتحانات'],
            'exams.view' => ['en' => 'View Exams', 'ar' => 'عرض الامتحانات'],
            'exams.update' => ['en' => 'Update Exams', 'ar' => 'تحديث الامتحانات'],
            'exams.delete' => ['en' => 'Delete Exams', 'ar' => 'حذف الامتحانات'],
            'exams.take' => ['en' => 'Take Exams', 'ar' => 'أداء الامتحانات'],
            'exams.grade' => ['en' => 'Grade Exams', 'ar' => 'تصحيح الامتحانات'],

            // Attendance permissions
            'attendance.view' => ['en' => 'View Attendance', 'ar' => 'عرض الحضور'],
            'attendance.mark' => ['en' => 'Mark Attendance', 'ar' => 'تسجيل الحضور'],

            // Reports permissions
            'reports.view' => ['en' => 'View Reports', 'ar' => 'عرض التقارير'],
            'reports.generate' => ['en' => 'Generate Reports', 'ar' => 'إنشاء التقارير'],
        ];

        foreach ($permissions as $permissionName => $label) {
            Permission::create([
                'name' => $permissionName,
                'label' => $label,
                'guard_name' => 'web'
            ]);
        }

        // Create roles and assign permissions
        $teacher = Role::create([
            'name' => 'teacher',
            'label' => ['en' => 'Teacher', 'ar' => 'معلم'],
            'guard_name' => 'web'
        ]);
        $teacher->givePermissionTo([
            'courses.create',
            'courses.view',
            'courses.update',
            'courses.delete',
            'courses.publish',
            'lessons.create',
            'lessons.view',
            'lessons.update',
            'lessons.delete',
            'students.view',
            'enrollments.create',
            'enrollments.view',
            'enrollments.update',
            'exams.create',
            'exams.view',
            'exams.update',
            'exams.delete',
            'exams.grade',
            'attendance.view',
            'attendance.mark',
            'reports.view',
            'reports.generate',
        ]);

        $assistant = Role::create([
            'name' => 'assistant',
            'label' => ['en' => 'Assistant', 'ar' => 'مساعد'],
            'guard_name' => 'web'
        ]);
        $assistant->givePermissionTo([
            'courses.view',
            'lessons.view',
            'students.view',
            'enrollments.view',
            'exams.view',
            'attendance.view',
            'attendance.mark',
            'reports.view',
        ]);

        $student = Role::create([
            'name' => 'student',
            'label' => ['en' => 'Student', 'ar' => 'طالب'],
            'guard_name' => 'web'
        ]);
        $student->givePermissionTo([
            'courses.view',
            'lessons.view',
            'exams.view',
            'exams.take',
            'attendance.view',
        ]);

        $guardian = Role::create([
            'name' => 'guardian',
            'label' => ['en' => 'Guardian', 'ar' => 'ولي أمر'],
            'guard_name' => 'web'
        ]);
        $guardian->givePermissionTo([
            'students.view',
            'attendance.view',
            'reports.view',
        ]);
    }
}

