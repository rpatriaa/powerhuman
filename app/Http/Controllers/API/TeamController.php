<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateTeamRequest;
use App\Http\Requests\UpdateTeamRequest;
use App\Models\Team;
use Exception;
use Illuminate\Http\Request;

class TeamController extends Controller
{


    public function fetch(Request $request)
    {

        $id = $request->input('id');
        $name = $request->input('name');
        $limit = $request->input('limit', 10);

        $teamQuery = Team::query();

        // powerhuman.test/api/team?id=1
        if ($id) {
            $team = $teamQuery->find($id);

            if ($team) {
                return ResponseFormatter::success(
                    $team,
                    'Team Found'
                );
            }

            return  ResponseFormatter::error(
                null,
                'Team not found',
                404
            );
        }

        // powerhuman.test/api/team?name=powerhuman
        $teams = $teamQuery->where('company_id', $request->company_id);

        // cek query yang dijalankan diatas
        // dd($teams->toSql());

        if ($name) {
            $teams->where('name', 'like', '%' . $name . '%');
        }

        return ResponseFormatter::success(
            $teams->paginate($limit),
            'Team Found'
        );
    }

    public function create(CreateTeamRequest $request)
    {
        try {
            // kondisi jika ada icon yang diupload menggunakan form request
            // if ($request->hasFile('icon')) {
            //     $request->validated()['icon'] = $request->file('icon')->store('public/icons');
            // }

            // kondisi jika icon yang diupload cek manual
            if ($request->hasFile('icon')) {
                $path = $request->file('icon')->store('public/icons');
            }


            // Penggunaan fungsi validated() validasi custom form request pada folder App\Http\Requests
            // $team = Team::create($request->validated());

            // Cara manual setelah lolos validasi
            $team = Team::create([
                'name' => $request->name,
                'icon' => $path,
                'company_id' => $request->company_id,
            ]);

            if (!$team) {
                throw new \Exception('Team not created');
            }

            return ResponseFormatter::success(
                $team,
                'Team created successfully'
            );
        } catch (\Exception $e) {
            return ResponseFormatter::error(
                null,
                $e->getMessage(),
                500
            );
        }
    }

    public function update(UpdateTeamRequest $request, $id)
    {
        try {

            $team = Team::find($id);

            if (!$team) {
                throw new Exception('Team not found');
            }


            // For update request validated form request
            // if ($request->hasFile('icon')) {
            //     $path = $request->file('icon')->store('public/icons');
            //     $team->icon = $path;
            // }

            // Update the Team with the validated data form request
            // $team->update($request->validated());

            if ($request->hasFile('icon')) {
                $path = $request->file('icon')->store('public/icons');
            }

            // Update the Team manually
            $team->update([
                'name' => $request->name,
                'icon' => isset($path) ? $path : $team->icon,
                'company_id' => $request->company_id,
            ]);

            return ResponseFormatter::success(
                $team,
                'Team updated successfully'
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
            $team = Team::find($id);

            if (!$team) {
                throw new Exception('Team not found');
            }

            $team->delete();

            return ResponseFormatter::success(
                null,
                'Team deleted successfully'
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
