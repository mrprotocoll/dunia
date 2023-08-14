<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

/**
 * @group Authentication
 *
 * Endpoint to manage user authentication
 */
class GoogleAuthController extends Controller
{
    /**
     * Authenticate and log in a user using Google OAuth.
     *
     * @param Request $request The authentication request.
     *
     * @bodyParam token string required The Google OAuth access token.
     *
     * @response {
     *     "token": "generated_token",
     *     "data": {
     *         "id": 1,
     *         "name": "John Doe",
     *         "email": "johndoe@example.com",
     *     }
     * }
     * @response 422 {
     *     "error": "Validation failed."
     * }
     * @response 401 {
     *     "error": "Unauthorized."
     * }
     *
     * @return JsonResponse
     */
    public function __invoke(Request $request): JsonResponse
    {
        //
        $request->validate([
            'token' => ['required', 'string', 'max:255'],
        ]);

        $googleUser = Socialite::driver('github')->userFromToken($request->token);
        $device = substr($request->userAgent() ?? '', 0, 255);

        $user = User::updateOrCreate([
            'google_id' => $googleUser->id,
        ], [
            'name' => $googleUser->name,
            'email' => $googleUser->email,
            'password' => $device,
            'oauth' => true,
            'google_token' => $googleUser->token,
            'google_refresh_token' => $googleUser->refreshToken,
        ]);

        Auth::login($user);

        $token = $user->createToken($device)->plainTextToken;

        return response()->json(['token' => $token, 'data' => new UserResource($user)], 201);
    }
}
