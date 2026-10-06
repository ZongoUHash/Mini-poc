<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmployeePortalController extends Controller
{
    public function index(Request $request): View
    {
        /** @var Employee $employee */
        $employee = $request->user()->employee;

        return view('employee.portal', [
            'employee' => $employee,
            'todayRecords' => $employee->attendanceRecords()
                ->with('attendanceSession')
                ->whereDate('pointed_at', today())
                ->latest('pointed_at')
                ->get(),
        ]);
    }
}
