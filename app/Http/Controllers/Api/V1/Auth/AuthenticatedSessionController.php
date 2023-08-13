<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Auth\LoginRequest;
use App\Http\Resources\V1\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

/**
 * @group Authentication
 *
 * Endpoint to manage user authentication
 */
class AuthenticatedSessionController extends Controller
{
    /**
     * User Login.
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
    public function store(LoginRequest $request): JsonResponse
    {
        $request->authenticate();
        $user = $request->user();
        $user->tokens()->delete();

        $device = substr($request->userAgent() ?? '', 0, 255);

        return response()->json(['token' => $user->createToken($device)->plainTextToken, 'data' => $user], 200);
    }

    /**
     * Logout.
     * @authenticated
     * @response 204 {
     *      "message": "Logged out successfully."
     *  }
     * @response 402 {
     *      "message": "Unauthorized user"
     *  }
     *
     * @return JsonResponse
     */
    public function destroy(Request $request): JsonResponse
    {
        if(!Auth::check()) {
            return response()->json(['message' => 'Unauthorized user'], 402);
        }

        Auth::guard('api')->logout();

        $user = $request->user();
        $user->currentAccessToken()->delete();

        return response()->json(['message' => 'logged out successfuly'], 204);
    }
}
