<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Models\Role;
use Exception;
use Illuminate\Http\Request;

class RoleController extends Controller
{


    public function fetch(Request $request)
    {

        $id = $request->input('id');
        $name = $request->input('name');
        $limit = $request->input('limit', 10);
        $with_responsibilities = $request->input('with_responsibilities', 0);

        $roleQuery = Role::query();

        // powerhuman.test/api/role?id=1
        if ($id) {
            $role = $roleQuery->find($id)->with('responsibility');

            if ($role) {
                return ResponseFormatter::success(
                    $role,
                    'Role Found'
                );
            }

            return  ResponseFormatter::error(
                null,
                'Role not found',
                404
            );
        }

        // powerhuman.test/api/role?name=powerhuman
        $roles = $roleQuery->where('company_id', $request->company_id);

        // cek query yang dijalankan diatas
        // dd($roles->toSql());

        if ($name) {
            $roles->where('name', 'like', '%' . $name . '%');
        }

        if ($with_responsibilities) {
            $roles->with('responsibility');
        }

        return ResponseFormatter::success(
            $roles->paginate($limit),
            'Role Found'
        );
    }

    public function create(CreateRoleRequest $request)
    {
        try {
            // Penggunaan fungsi validated() validasi custom form request pada folder App\Http\Requests
            // $role = Role::create($request->validated());

            // Cara manual setelah lolos validasi
            $role = Role::create([
                'name' => $request->name,
                'company_id' => $request->company_id,
            ]);

            if (!$role) {
                throw new \Exception('Role not created');
            }

            return ResponseFormatter::success(
                $role,
                'Role created successfully'
            );
        } catch (\Exception $e) {
            return ResponseFormatter::error(
                null,
                $e->getMessage(),
                500
            );
        }
    }

    public function update(UpdateRoleRequest $request, $id)
    {
        try {

            $role = Role::find($id);

            if (!$role) {
                throw new Exception('Role not found');
            }

            // Update the Role with the validated data form request
            // $role->update($request->validated());

            // Update the Role manually
            $role->update([
                'name' => $request->name,
                'icon' => isset($path) ? $path : $role->icon,
                'company_id' => $request->company_id,
            ]);

            return ResponseFormatter::success(
                $role,
                'Role updated successfully'
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
            $role = Role::find($id);

            if (!$role) {
                throw new Exception('Role not found');
            }

            $role->delete();

            return ResponseFormatter::success(
                null,
                'Role deleted successfully'
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
