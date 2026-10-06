<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_hr_can_sign_in_and_employee_area_is_protected(): void
    {
        $hr = User::factory()->create([
            'email' => 'rh@example.test',
            'role' => 'hr',
            'password' => Hash::make('secret-password'),
        ]);

        $this->get(route('hr.dashboard'))->assertRedirect(route('login'));

        $this->post(route('login.store'), ['email' => $hr->email, 'password' => 'secret-password'])
            ->assertRedirect(route('hr.dashboard'));

        $this->actingAs($hr)->get(route('employee.portal'))->assertForbidden();
    }

    public function test_employee_can_sign_in_and_hr_area_is_protected(): void
    {
        $employeeUser = User::factory()->create(['role' => 'employee']);
        Employee::factory()->create(['user_id' => $employeeUser->id]);

        $this->actingAs($employeeUser)->get(route('employee.portal'))->assertOk();
        $this->actingAs($employeeUser)->get(route('hr.dashboard'))->assertForbidden();
    }
}
