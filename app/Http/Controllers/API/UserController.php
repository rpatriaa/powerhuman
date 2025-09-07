<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function fetch(Request $request)
    {
        $user = $request->user();

        return ResponseFormatter::success($user, 'Fetch user data successfully');
    }
}
