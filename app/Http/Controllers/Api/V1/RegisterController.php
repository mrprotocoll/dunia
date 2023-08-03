<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(RegisterRequest $request)
    {
        //
        $request->password = Hash::make($request->password);
        $user = User::create($request->validated());
        if($user) {
            return new UserResource($user);
        }else{
            return response()->json(["message" => "Error occurred. Please try again"], 422);
        }
    }
}
