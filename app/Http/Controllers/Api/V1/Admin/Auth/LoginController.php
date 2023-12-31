<?php

namespace App\Http\Controllers\Api\V1\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Auth\LoginRequest;
use App\Http\Resources\V1\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

/**
 * @group Authentication
 *
 * Endpoint to manage user authentication
 */
class LoginController extends Controller
{
    /**
     * Admin Login.
     *
     * @param LoginRequest $request
     * @response {
     *      "token": "generated_token"
     *      "data": {
     *          "id": 1,
     *          "name": "User",
     *          "email": "user@email.com"
     *      }
     *  }
     * @response 422 {
     *      "error": "The provided credentials are incorrect."
     * }
     *
     * @return JsonResponse
     */
    public function __invoke(LoginRequest $request): JsonResponse
    {
        //
        $user = User::where('email', $request->email)->where('role', 'admin')->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json(['error' => 'The provided credentials are incorrect.'], 422);
        }

        $device = substr($request->userAgent() ?? '', 0, 255);

        return response()->json(['token' => $user->createToken($device)->plainTextToken, 'data' => new UserResource($user)], 200);
    }
}
