<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateCompanyRequest;
use App\Http\Requests\UpdateCompanyRequest;
use App\Models\Company;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompanyController extends Controller
{
    public function fetch(Request $request)
    {

        $id = $request->input('id');
        $name = $request->input('name');
        $limit = $request->input('limit', 10);

        $companyQuery = Company::with('users')->whereHas('users', function ($query) {
            $query->where('user_id', Auth::id());
        });

        // powerhuman.test/api/company?id=1
        if ($id) {
            $company = $companyQuery->find($id);

            if ($company) {
                return ResponseFormatter::success(
                    $company,
                    'Company Found'
                );
            }

            return  ResponseFormatter::error(
                null,
                'Company not found',
                404
            );
        }

        // powerhuman.test/api/company?name=powerhuman
        $companies = $companyQuery;

        // cek query yang dijalankan diatas
        // dd($companies->toSql());

        if ($name) {
            $companies->where('name', 'like', '%' . $name . '%');
        }

        return ResponseFormatter::success(
            $companies->paginate($limit),
            'Company Found'
        );
    }

    public function create(CreateCompanyRequest $request)
    {
        try {
            // kondisi jika ada logo yang diupload menggunakan form request
            // if ($request->hasFile('logo')) {
            //     $request->validated()['logo'] = $request->file('logo')->store('public/logos');
            // }

            // kondisi jika logo yang diupload cek manual
            if ($request->hasFile('logo')) {
                $path = $request->file('logo')->store('public/logos');
            }


            // Penggunaan fungsi validated() validasi custom form request pada folder App\Http\Requests
            // $company = Company::create($request->validated());

            // Cara manual setelah lolos validasi
            $company = Company::create([
                'name' => $request->name,
                'logo' => $path,
            ]);

            if (!$company) {
                throw new \Exception('Company not created');
            }

            // Menambahkan data ke table pivot karena data berelasi manytomany
            // Mengambil id user yang sudah authentication, lalu kirim id ke fungsi companies di model Company tambahkan id ke table pivot
            $user = User::find(Auth::id());
            $user->companies()->attach($company->id);

            // load user at company
            $company->load('users');

            return ResponseFormatter::success(
                $company,
                'Company created successfully'
            );
        } catch (\Exception $e) {
            return ResponseFormatter::error(
                null,
                $e->getMessage(),
                500
            );
        }
    }

    public function update(UpdateCompanyRequest $request, $id)
    {
        try {

            $company = Company::find($id);

            if (!$company) {
                throw new Exception('Company not found');
            }


            // For update request validated form request
            // if ($request->hasFile('logo')) {
            //     $path = $request->file('logo')->store('public/logos');
            //     $company->logo = $path;
            // }

            // Update the company with the validated data form request
            // $company->update($request->validated());

            if ($request->hasFile('logo')) {
                $path = $request->file('logo')->store('public/logos');
            }

            // Update the company manually
            $company->update([
                'name' => $request->name,
                'logo' => isset($path) ? $path : $company->logo,
            ]);

            return ResponseFormatter::success(
                $company,
                'Company updated successfully'
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
