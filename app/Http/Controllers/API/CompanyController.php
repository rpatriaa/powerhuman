<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Company;
use GuzzleHttp\Psr7\Response;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    public function all(Request $request)
    {
        $id = $request->input('id');
        $name = $request->input('name');
        $limit = $request->input('limit', 10);

        // powerhuman.test/api/company?i=1
        if ($id) {
            $company = Company::with('user')->find($id);

            if ($company) {
                return ResponseFormatter::success(
                    $company,
                    'Company data retrieved successfully'
                );
            }

            return  ResponseFormatter::error(
                'Company not found',
                404
            );
        }

        // powerhuman.test/api/company?name=powerhuman
        $companies = Company::with('user');

        if ($name) {
            $companies->where('name', 'like', '%' . $name . '%');
        }

        return ResponseFormatter::success(
            $companies->paginate($limit),
            'Company list retrieved successfully'
        );
    }
}
