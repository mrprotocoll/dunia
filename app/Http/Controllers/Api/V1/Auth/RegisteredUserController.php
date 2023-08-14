<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\UserResource;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

/**
 * @group Authentication
 *
 * Endpoint to manage user authentication
 */
class RegisteredUserController extends Controller
{

    /**
     * Register a new user.
     *
     * @throws \Illuminate\Validation\ValidationException
     *
     * @apiResource App\Http\Resources\V1\UserResource
     * @apiResourceModel App\Models\User
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Auth::login($user);

        $device = substr($request->userAgent() ?? '', 0, 255);
        $token = $user->createToken($device)->plainTextToken;

        return response()->json(['token' => $token, 'data' => new UserResource($user)], 201);

    }
}
