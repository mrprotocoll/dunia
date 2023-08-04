<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(RegisterRequest $request)
    {
        // check if user already exist

        if(User::where("email", $request->email)->where("role", 0)->exists()){
            return response()->json(["message" => "Account already exist, kindly login"]);
        }

        $request->password = Hash::make($request->password);
        $user = User::create($request->validated());
        if($user) {
            event(new Registered($user));
            return new UserResource($user);
        }else{
            return response()->json(["message" => "Error occurred. Please try again"], 422);
        }
    }
}
