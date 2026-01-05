<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $registrar = app(PermissionRegistrar::class);
        $registrar->setPermissionsTeamId(null);
        $registrar->forgetCachedPermissions();

        // Detect if we're seeding the central database
        $isCentralDatabase = $this->isCentralDatabase();

        // Define base permissions (available in both central and tenant databases)
        $permissions = $this->getBasePermissions();

        // Add tenant management permissions only for central database
        if ($isCentralDatabase) {
            $permissions = array_merge($permissions, $this->getTenantPermissions());
        }

        // Create all permissions
        $this->createPermissions($permissions);

        // Clear cache after creating permissions
        $registrar->forgetCachedPermissions();

        // Create roles and assign permissions
        $this->createRolesAndAssignPermissions($isCentralDatabase);
    }

    /**
     * Check if we're seeding the central database.
     */
    protected function isCentralDatabase(): bool
    {
        try {
            $currentConnection = DB::getDefaultConnection();
            $centralConnection = config('tenancy.database.central_connection', 'central');

            // Check if current connection is the central connection
            if ($currentConnection === $centralConnection) {
                return true;
            }

            // Check if central connection exists and we can access it
            if (config("database.connections.{$centralConnection}")) {
                $centralDbName = config("database.connections.{$centralConnection}.database");
                $currentDbName = DB::connection()->getDatabaseName();

                return $currentDbName === $centralDbName;
            }

            return false;
        } catch (\Exception $e) {
            // If we can't determine, assume it's a tenant database
            return false;
        }
    }

    /**
     * Get base permissions (available in all databases).
     */
    protected function getBasePermissions(): array
    {
        return [
            // Course permissions
            'courses.create' => ['en' => 'Create Courses', 'ar' => 'إنشاء الدورات'],
            'courses.view' => ['en' => 'View Courses', 'ar' => 'عرض الدورات'],
            'courses.update' => ['en' => 'Update Courses', 'ar' => 'تحديث الدورات'],
            'courses.delete' => ['en' => 'Delete Courses', 'ar' => 'حذف الدورات'],
            'courses.bulk_delete' => ['en' => 'Bulk Delete Courses', 'ar' => 'حذف الدورات بالجملة'],
            'courses.update_order' => ['en' => 'Update Course Order', 'ar' => 'تحديث ترتيب الدورات'],
            'courses.publish' => ['en' => 'Publish Courses', 'ar' => 'نشر الدورات'],

            // Lesson permissions
            'lessons.create' => ['en' => 'Create Lessons', 'ar' => 'إنشاء الدروس'],
            'lessons.view' => ['en' => 'View Lessons', 'ar' => 'عرض الدروس'],
            'lessons.update' => ['en' => 'Update Lessons', 'ar' => 'تحديث الدروس'],
            'lessons.delete' => ['en' => 'Delete Lessons', 'ar' => 'حذف الدروس'],
            'lessons.bulk_delete' => ['en' => 'Bulk Delete Lessons', 'ar' => 'حذف الدروس بالجملة'],
            'lessons.update_order' => ['en' => 'Update Lesson Order', 'ar' => 'تحديث ترتيب الدروس'],

            // Student permissions
            'students.create' => ['en' => 'Create Students', 'ar' => 'إنشاء الطلاب'],
            'students.view' => ['en' => 'View Students', 'ar' => 'عرض الطلاب'],
            'students.update' => ['en' => 'Update Students', 'ar' => 'تحديث الطلاب'],
            'students.delete' => ['en' => 'Delete Students', 'ar' => 'حذف الطلاب'],
            'students.bulk_delete' => ['en' => 'Bulk Delete Students', 'ar' => 'حذف الطلاب بالجملة'],
            'students.update_order' => ['en' => 'Update Student Order', 'ar' => 'تحديث ترتيب الطلاب'],

            // Enrollment permissions
            'enrollments.create' => ['en' => 'Create Enrollments', 'ar' => 'إنشاء التسجيلات'],
            'enrollments.view' => ['en' => 'View Enrollments', 'ar' => 'عرض التسجيلات'],
            'enrollments.update' => ['en' => 'Update Enrollments', 'ar' => 'تحديث التسجيلات'],
            'enrollments.delete' => ['en' => 'Delete Enrollments', 'ar' => 'حذف التسجيلات'],
            'enrollments.bulk_delete' => ['en' => 'Bulk Delete Enrollments', 'ar' => 'حذف التسجيلات بالجملة'],
            'enrollments.update_order' => ['en' => 'Update Enrollment Order', 'ar' => 'تحديث ترتيب التسجيلات'],

            // Exam permissions
            'exams.create' => ['en' => 'Create Exams', 'ar' => 'إنشاء الامتحانات'],
            'exams.view' => ['en' => 'View Exams', 'ar' => 'عرض الامتحانات'],
            'exams.update' => ['en' => 'Update Exams', 'ar' => 'تحديث الامتحانات'],
            'exams.delete' => ['en' => 'Delete Exams', 'ar' => 'حذف الامتحانات'],
            'exams.take' => ['en' => 'Take Exams', 'ar' => 'أداء الامتحانات'],
            'exams.grade' => ['en' => 'Grade Exams', 'ar' => 'تصحيح الامتحانات'],
            'exams.bulk_delete' => ['en' => 'Bulk Delete Exams', 'ar' => 'حذف الامتحانات بالجملة'],
            'exams.update_order' => ['en' => 'Update Exam Order', 'ar' => 'تحديث ترتيب الامتحانات'],

            // Attendance permissions
            'attendance.view' => ['en' => 'View Attendance', 'ar' => 'عرض الحضور'],
            'attendance.mark' => ['en' => 'Mark Attendance', 'ar' => 'تسجيل الحضور'],
            'attendance.bulk_delete' => ['en' => 'Bulk Delete Attendance', 'ar' => 'حذف الحضور بالجملة'],
            'attendance.update_order' => ['en' => 'Update Attendance Order', 'ar' => 'تحديث ترتيب الحضور'],

            // Reports permissions
            'reports.view' => ['en' => 'View Reports', 'ar' => 'عرض التقارير'],
            'reports.generate' => ['en' => 'Generate Reports', 'ar' => 'إنشاء التقارير'],
            'reports.bulk_delete' => ['en' => 'Bulk Delete Reports', 'ar' => 'حذف التقارير بالجملة'],
            'reports.update_order' => ['en' => 'Update Report Order', 'ar' => 'تحديث ترتيب التقارير'],

            // User management permissions
            'users.create' => ['en' => 'Create Users', 'ar' => 'إنشاء المستخدمين'],
            'users.view' => ['en' => 'View Users', 'ar' => 'عرض المستخدمين'],
            'users.update' => ['en' => 'Update Users', 'ar' => 'تحديث المستخدمين'],
            'users.delete' => ['en' => 'Delete Users', 'ar' => 'حذف المستخدمين'],
            'users.bulk_delete' => ['en' => 'Bulk Delete Users', 'ar' => 'حذف المستخدمين بالجملة'],
            'users.update_order' => ['en' => 'Update User Order', 'ar' => 'تحديث ترتيب المستخدمين'],
            'users.manage_permissions' => ['en' => 'Manage User Direct Permissions', 'ar' => 'إدارة الصلاحيات المباشرة للمستخدمين'],

            // Role management permissions
            'roles.create' => ['en' => 'Create Roles', 'ar' => 'إنشاء الأدوار'],
            'roles.view' => ['en' => 'View Roles', 'ar' => 'عرض الأدوار'],
            'roles.update' => ['en' => 'Update Roles', 'ar' => 'تحديث الأدوار'],
            'roles.delete' => ['en' => 'Delete Roles', 'ar' => 'حذف الأدوار'],
            'roles.bulk_delete' => ['en' => 'Bulk Delete Roles', 'ar' => 'حذف الأدوار بالجملة'],
            'roles.update_order' => ['en' => 'Update Role Order', 'ar' => 'تحديث ترتيب الأدوار'],

            // Permission management permissions
            'permissions.view' => ['en' => 'View Permissions', 'ar' => 'عرض الصلاحيات'],
            'permissions.update_order' => ['en' => 'Update Permission Order', 'ar' => 'تحديث ترتيب الصلاحيات'],
            'permissions.bulk_delete' => ['en' => 'Bulk Delete Permissions', 'ar' => 'حذف الصلاحيات بالجملة'],
        ];
    }

    /**
     * Get tenant management permissions (only for central database).
     */
    protected function getTenantPermissions(): array
    {
        return [
            'tenants.create' => ['en' => 'Create Tenants', 'ar' => 'إنشاء المستأجرين'],
            'tenants.view' => ['en' => 'View Tenants', 'ar' => 'عرض المستأجرين'],
            'tenants.update' => ['en' => 'Update Tenants', 'ar' => 'تحديث المستأجرين'],
            'tenants.delete' => ['en' => 'Delete Tenants', 'ar' => 'حذف المستأجرين'],
            'tenants.bulk_delete' => ['en' => 'Bulk Delete Tenants', 'ar' => 'حذف المستأجرين بالجملة'],
            'tenants.update_order' => ['en' => 'Update Tenant Order', 'ar' => 'تحديث ترتيب المستأجرين'],
        ];
    }

    /**
     * Create all permissions in the database.
     */
    protected function createPermissions(array $permissions): void
    {
        foreach ($permissions as $permissionName => $label) {
            Permission::firstOrCreate(
                [
                    'name' => $permissionName,
                    'guard_name' => 'web',
                ],
                [
                    'label' => $label,
                ]
            );
        }
    }

    /**
     * Create roles and assign permissions.
     */
    protected function createRolesAndAssignPermissions(bool $isCentralDatabase): void
    {
        // Create super admin role only in central database
        if ($isCentralDatabase) {
            $superAdmin = $this->createRole('super-admin', ['en' => 'Super Admin', 'ar' => 'مدير النظام']);
            $superAdmin->syncPermissions($this->getSuperAdminPermissions());
        }

        // Create teacher role
        $teacher = $this->createRole('teacher', ['en' => 'Teacher', 'ar' => 'معلم']);
        $teacher->syncPermissions($this->getTeacherPermissions());

        // Create assistant role
        $assistant = $this->createRole('assistant', ['en' => 'Assistant', 'ar' => 'مساعد']);
        $assistant->syncPermissions($this->getAssistantPermissions());

        // Create student role
        $student = $this->createRole('student', ['en' => 'Student', 'ar' => 'طالب']);
        $student->syncPermissions($this->getStudentPermissions());

        // Create guardian role
        $guardian = $this->createRole('guardian', ['en' => 'Guardian', 'ar' => 'ولي أمر']);
        $guardian->syncPermissions($this->getGuardianPermissions());
    }

    /**
     * Create a role if it doesn't exist.
     */
    protected function createRole(string $name, array $label): Role
    {
        return Role::firstOrCreate(
            [
                'name' => $name,
                'guard_name' => 'web',
            ],
            [
                'label' => $label,
            ]
        );
    }

    /**
     * Get permissions for teacher role.
     */
    protected function getTeacherPermissions(): array
    {
        return [
            'courses.create',
            'courses.update',
            'courses.delete',
            'courses.bulk_delete',
            'courses.update_order',
            'courses.publish',
            'lessons.create',
            'lessons.update',
            'lessons.delete',
            'lessons.bulk_delete',
            'lessons.update_order',
            'students.create',
            'students.update',
            'students.delete',
            'students.bulk_delete',
            'students.update_order',
            'enrollments.create',
            'enrollments.update',
            'enrollments.delete',
            'enrollments.bulk_delete',
            'enrollments.update_order',
            'exams.create',
            'exams.update',
            'exams.delete',
            'exams.bulk_delete',
            'exams.update_order',
            'exams.grade',
            'attendance.view',
            'attendance.mark',
            'attendance.bulk_delete',
            'attendance.update_order',
            'reports.view',
            'reports.generate',
            'reports.bulk_delete',
            'reports.update_order',
            'users.create',
            'users.view',
            'users.update',
            'users.delete',
            'users.bulk_delete',
            'users.update_order',
            'users.manage_permissions',
            'roles.create',
            'roles.view',
            'roles.update',
            'roles.delete',
            'roles.bulk_delete',
            'roles.update_order',
            'permissions.view',
            'permissions.update_order',
            'permissions.bulk_delete',
        ];
    }

    /**
     * Get permissions for assistant role.
     */
    protected function getAssistantPermissions(): array
    {
        return [
            'courses.view',
            'lessons.view',
            'students.view',
            'enrollments.view',
            'exams.view',
            'attendance.view',
            'attendance.mark',
            'reports.view',
            'users.create',
            'users.view',
        ];
    }

    /**
     * Get permissions for student role.
     */
    protected function getStudentPermissions(): array
    {
        return [
            'courses.view',
            'lessons.view',
            'exams.view',
            'exams.take',
            'attendance.view',
        ];
    }

    /**
     * Get permissions for guardian role.
     */
    protected function getGuardianPermissions(): array
    {
        return [
            'students.view',
            'attendance.view',
            'reports.view',
        ];
    }

    /**
     * Get permissions for super admin role (central database only).
     */
    protected function getSuperAdminPermissions(): array
    {
        return [
            'tenants.create',
            'tenants.view',
            'tenants.update',
            'tenants.delete',
            'tenants.bulk_delete',
            'tenants.update_order',
        ];
    }
}
