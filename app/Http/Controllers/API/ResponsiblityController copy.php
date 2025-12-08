<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateResponsibilityRequest;
use App\Http\Requests\UpdateResponsibilityRequest;
use App\Models\Responsibility;
use Exception;
use Illuminate\Http\Request;

class ResponsibilityController extends Controller
{


    public function fetch(Request $request)
    {

        $id = $request->input('id');
        $name = $request->input('name');
        $limit = $request->input('limit', 10);

        $ResponsibilityQuery = Responsibility::query();

        // powerhuman.test/api/Responsibility?id=1
        if ($id) {
            $Responsibility = $ResponsibilityQuery->find($id);

            if ($Responsibility) {
                return ResponseFormatter::success(
                    $Responsibility,
                    'Responsibility Found'
                );
            }

            return  ResponseFormatter::error(
                null,
                'Responsibility not found',
                404
            );
        }

        // powerhuman.test/api/responsibility?name=powerhuman
        $responsibility = $ResponsibilityQuery->where('company_id', $request->company_id);

        // cek query yang dijalankan diatas
        // dd($responsibilitys->toSql());

        if ($name) {
            $responsibility->where('name', 'like', '%' . $name . '%');
        }

        return ResponseFormatter::success(
            $responsibility->paginate($limit),
            'Responsibility Found'
        );
    }

    public function create(CreateResponsibilityRequest $request)
    {
        try {
            // Penggunaan fungsi validated() validasi custom form request pada folder App\Http\Requests
            // $responsibility = Responsibility::create($request->validated());

            // Cara manual setelah lolos validasi
            $responsibility = Responsibility::create([
                'name' => $request->name,
                'company_id' => $request->company_id,
            ]);

            if (!$responsibility) {
                throw new \Exception('Responsibility not created');
            }

            return ResponseFormatter::success(
                $responsibility,
                'Responsibility created successfully'
            );
        } catch (\Exception $e) {
            return ResponseFormatter::error(
                null,
                $e->getMessage(),
                500
            );
        }
    }

    public function update(UpdateResponsibilityRequest $request, $id)
    {
        try {

            $responsibility = Responsibility::find($id);

            if (!$responsibility) {
                throw new Exception('Responsibility not found');
            }

            // Update the Responsibility with the validated data form request
            // $responsibility->update($request->validated());

            // Update the Responsibility manually
            $responsibility->update([
                'name' => $request->name,
                'icon' => isset($path) ? $path : $responsibility->icon,
                'company_id' => $request->company_id,
            ]);

            return ResponseFormatter::success(
                $responsibility,
                'Responsibility updated successfully'
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
            $responsibility = Responsibility::find($id);

            if (!$responsibility) {
                throw new Exception('Responsibility not found');
            }

            $responsibility->delete();

            return ResponseFormatter::success(
                null,
                'Responsibility deleted successfully'
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
