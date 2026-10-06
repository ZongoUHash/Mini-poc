<?php

namespace Database\Seeders;

use App\Models\Employee;
use Illuminate\Database\Seeder;

class AttendanceDemoSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            ['name' => 'Awa Traoré', 'email' => 'awa@poc.local'],
            ['name' => 'Moussa Kaboré', 'email' => 'moussa@poc.local'],
            ['name' => 'Sita Ouédraogo', 'email' => 'sita@poc.local'],
        ] as $employee) {
            Employee::query()->updateOrCreate(['email' => $employee['email']], $employee);
        }
    }
}
