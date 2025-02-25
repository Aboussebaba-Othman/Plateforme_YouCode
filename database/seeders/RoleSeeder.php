<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run()
    {
        $roles = [
            ['name' => 'admin', 'description' => 'Administrator'],
            ['name' => 'staff', 'description' => 'Staff Member'],
            ['name' => 'candidate', 'description' => 'Candidate'],
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }
    }
}