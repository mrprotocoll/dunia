<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\LoginRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

/**
 * @group Authentication
 *
 * Endpoint to manage user authentication
 */
class LoginController extends Controller
{
    /**
     * Login.
     */
    public function __invoke(LoginRequest $request)
    {
        //
        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json(['error' => 'The provided credentials are incorrect.'], 422);
        }

        $device = substr($request->userAgent() ?? '', 0, 255);

        return response()->json(['token' => $user->createToken($device)->plainTextToken], 200);
    }
}
