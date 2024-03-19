<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $role_admin = Role::create(['name' => 'admin']);
        $role_student = Role::create(['name' => 'student']);

        $permission_create_user = Permission::create(['name' => 'create user']);
        $permission_read_user = Permission::create(['name' => 'read user']);
        $permission_update_user = Permission::create(['name' => 'update user']);
        $permission_delete_user = Permission::create(['name' => 'delete user']);

        $permission_create_course = Permission::create(['name' => 'create course']);
        $permission_read_course = Permission::create(['name' => 'read course']);
        $permission_update_course = Permission::create(['name' => 'update course']);
        $permission_delete_course = Permission::create(['name' => 'delete course']);

        $permission_read_course_assign = Permission::create(['name' => 'read course assign']);

        $permission_admin = [$permission_create_user,
                                  $permission_read_user,
                                  $permission_update_user,
                                  $permission_delete_user,
                                  $permission_create_course,
                                  $permission_read_course,
                                  $permission_update_course,
                                  $permission_delete_course];

        $role_admin->syncPermissions($permission_admin);
        $role_student->givePermissionTo($permission_read_course_assign);
    }
}
