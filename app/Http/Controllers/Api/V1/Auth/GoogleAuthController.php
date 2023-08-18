<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

/**
 * @group Authentication
 *
 * Endpoint to manage user authentication
 */
class GoogleAuthController extends Controller
{

    /**
     * Handle the registration or login of a user via OAuth (Google or Facebook).
     *
     * @param Request $request The OAuth registration/login request.
     *
     * @bodyParam oauth string required The OAuth provider (google or facebook).
     * @bodyParam oauth_id string required The user's OAuth ID.
     * @bodyParam name string required The user's name.
     * @bodyParam email string required The user's email address.
     *
     * @response {
     *     "token": "generated_token",
     *     "data": {
     *         "id": 1,
     *         "name": "John Doe",
     *         "email": "johndoe@example.com",
     *     }
     * }
     *
     * @response 500 {
     *     "message": "Oops something went wrong"
     * }
     *
     * @return JsonResponse
     */
    public function __invoke(Request $request): JsonResponse
    {
        //
        try {
            $request->validate([
                'oauth' => ['required', 'string', Rule::in(['google', 'facebook'])],
                'oauth_id' => ['required', 'string'],
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            ]);

            //check if user exists
            $device = substr($request->userAgent() ?? '', 0, 255);

            $user = User::updateOrCreate([
                'email' => $request->email,
            ], [
                'name' => $request->name,
                'oauth_id' => $request->oauth_id,
                'oauth' => true,
                'auth_type' => $request->oauth,
            ]);

            Auth::login($user);

            $token = $user->createToken($device)->plainTextToken;

            return response()->json(['token' => $token, 'data' => new UserResource($user)], 201);
        }
        catch (\Exception $e) {
            Log::error('An exception occurred', ['exception' => $e]);
            return response()->json(['message' => 'Oops something went wrong'], 500);
        }
    }
}
