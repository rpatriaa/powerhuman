<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use App\Models\Employee;
use Exception;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{


    public function fetch(Request $request)
    {

        $id = $request->input('id');
        $name = $request->input('name');
        $email = $request->input('email');
        $age = $request->input('age');
        $phone = $request->input('phone');
        $team_id = $request->input('team_id');
        $role_id = $request->input('role_id');
        $limit = $request->input('limit', 10);

        $employeeQuery = Employee::query();

        // powerhuman.test/api/employee?id=1
        if ($id) {
            $employee = $employeeQuery->with(['team', 'role'])->find($id);

            if ($employee) {
                return ResponseFormatter::success(
                    $employee,
                    'Employee Found'
                );
            }

            return  ResponseFormatter::error(
                null,
                'Employee not found',
                404
            );
        }

        // powerhuman.test/api/employee
        $employees = $employeeQuery;

        // cek query yang dijalankan diatas
        // dd($employees->toSql());

        if ($name) {
            $employees->where('name', 'like', '%' . $name . '%');
        }

        if ($email) {
            $employees->where('email', $email);
        }

        if ($age) {
            $employees->where('age', $age);
        }

        if ($phone) {
            $employees->where('phone', 'like', '%' . $phone . '%');
        }

        if ($team_id) {
            $employees->where('team_id', $team_id);
        }

        if ($role_id) {
            $employees->where('role_id', $role_id);
        }

        return ResponseFormatter::success(
            $employees->paginate($limit),
            'Employee Found'
        );
    }

    public function create(CreateEmployeeRequest $request)
    {
        try {
            // kondisi jika ada photo yang diupload menggunakan form request
            // if ($request->hasFile('photo')) {
            //     $request->validated()['photo'] = $request->file('photo')->store('public/photos');
            // }

            // kondisi jika photo yang diupload cek manual
            if ($request->hasFile('photo')) {
                $path = $request->file('photo')->store('public/photos');
            }


            // Penggunaan fungsi validated() validasi custom form request pada folder App\Http\Requests
            // $employee = Employee::create($request->validated());

            // Cara manual setelah lolos validasi
            $employee = Employee::create([
                'name' => $request->name,
                'email' => $request->email,
                'gender' => $request->gender,
                'age' => $request->age,
                'phone' => $request->phone,
                'photo' => $path,
                'team_id' => $request->team_id,
                'role_id' => $request->role_id,
            ]);

            if (!$employee) {
                throw new \Exception('Employee not created');
            }

            return ResponseFormatter::success(
                $employee,
                'Employee created successfully'
            );
        } catch (\Exception $e) {
            return ResponseFormatter::error(
                null,
                $e->getMessage(),
                500
            );
        }
    }

    public function update(UpdateEmployeeRequest $request, $id)
    {
        try {

            $employee = Employee::find($id);

            if (!$employee) {
                throw new Exception('Employee not found');
            }


            // For update request validated form request
            // if ($request->hasFile('photo')) {
            //     $path = $request->file('photo')->store('public/photos');
            //     $employee->photo = $path;
            // }

            // Update the Employee with the validated data form request
            // $employee->update($request->validated());

            if ($request->hasFile('photo')) {
                $path = $request->file('photo')->store('public/photos');
            }

            // Update the Employee manually
            $employee->update([
                'name' => $request->name,
                'email' => $request->email,
                'gender' => $request->gender,
                'age' => $request->age,
                'phone' => $request->phone,
                'photo' => isset($path) ? $path : $employee->photo,
                'team_id' => $request->team_id,
                'role_id' => $request->role_id,
            ]);

            return ResponseFormatter::success(
                $employee,
                'Employee updated successfully'
            );
        } catch (Exception $e) {
            return ResponseFormatter::error(
                null,
                $e->getMessage(),
                500
            );
        }
    }

    public function destroy($id)
    {
        try {
            $employee = Employee::find($id);

            if (!$employee) {
                throw new Exception('Employee not found');
            }

            $employee->delete();

            return ResponseFormatter::success(
                null,
                'Employee deleted successfully'
            );
        } catch (Exception $e) {
            return ResponseFormatter::error(
                null,
                $e->getMessage(),
                500
            );
        }
    }
}
