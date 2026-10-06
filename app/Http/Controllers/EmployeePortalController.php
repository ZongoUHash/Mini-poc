<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmployeePortalController extends Controller
{
    public function index(Request $request): View
    {
        return view('employee.portal', [
            'employees' => Employee::query()->where('is_active', true)->orderBy('name')->get(),
            'currentEmployee' => $request->session()->has('employee_id')
                ? Employee::find((int) $request->session()->get('employee_id'))
                : null,
        ]);
    }

    public function reset(Request $request): RedirectResponse
    {
        $request->session()->forget('employee_id');

        return to_route('employee.portal');
    }

    public function select(Request $request): RedirectResponse
    {
        $validated = $request->validate(['employee_id' => ['required', 'exists:employees,id']]);
        $request->session()->put('employee_id', (int) $validated['employee_id']);

        return to_route('employee.portal');
    }
}
