<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // Course permissions
            'courses.create',
            'courses.view',
            'courses.update',
            'courses.delete',
            'courses.publish',
            
            // Lesson permissions
            'lessons.create',
            'lessons.view',
            'lessons.update',
            'lessons.delete',
            
            // Student permissions
            'students.create',
            'students.view',
            'students.update',
            'students.delete',
            
            // Enrollment permissions
            'enrollments.create',
            'enrollments.view',
            'enrollments.update',
            'enrollments.delete',
            
            // Exam permissions
            'exams.create',
            'exams.view',
            'exams.update',
            'exams.delete',
            'exams.take',
            'exams.grade',
            
            // Attendance permissions
            'attendance.view',
            'attendance.mark',
            
            // Reports permissions
            'reports.view',
            'reports.generate',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create roles and assign permissions
        $teacher = Role::create(['name' => 'teacher']);
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

        $assistant = Role::create(['name' => 'assistant']);
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

        $student = Role::create(['name' => 'student']);
        $student->givePermissionTo([
            'courses.view',
            'lessons.view',
            'exams.view',
            'exams.take',
            'attendance.view',
        ]);

        $guardian = Role::create(['name' => 'guardian']);
        $guardian->givePermissionTo([
            'students.view',
            'attendance.view',
            'reports.view',
        ]);
    }
}

