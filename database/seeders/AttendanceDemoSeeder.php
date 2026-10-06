<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AttendanceDemoSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'rh@pointage-poc.test'],
            ['name' => 'Mariam Ouédraogo', 'role' => 'hr', 'password' => Hash::make('RH-Pointage-2026')],
        );

        foreach ([
            ['name' => 'Awa Traoré', 'email' => 'awa@pointage-poc.test'],
            ['name' => 'Moussa Kaboré', 'email' => 'moussa@pointage-poc.test'],
            ['name' => 'Sita Ouédraogo', 'email' => 'sita@pointage-poc.test'],
        ] as $employee) {
            $user = User::query()->updateOrCreate(
                ['email' => $employee['email']],
                ['name' => $employee['name'], 'role' => 'employee', 'password' => Hash::make('Salarié-Pointage-2026')],
            );

            Employee::query()->updateOrCreate(
                ['name' => $employee['name']],
                [...$employee, 'user_id' => $user->id, 'is_active' => true],
            );
        }
    }
}
