<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\BillingAddressRequest;
use App\Http\Resources\V1\BillingAddressResource;
use App\Models\BillingAddress;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

/**
 * @group Billing Address
 *
 * Endpoint to manage customers billing address
 */
class BillingAddressController extends Controller
{

    /**
     * Get all customer Billing addresses
     *
     * Retrieve the billing addresses associated with the currently authenticated user.
     *
     * @authenticated
     *
     * @response {
     *     "data": [
     *         {
     *             "id": 1,
     *             "country": {...},
     *             "address": "123 Main St",
     *             "city": {...},
     *             "state": {...},
     *         },
     *         ...
     *     ]
     * }
     * @response 500 {
     *     "message": "Oops something went wrong"
     * }
     *
     * @return \Illuminate\Http\JsonResponse | ResourceCollection
     */
    public function index()
    {
        // Retrieve the currently authenticated user
        $user = User::current();

        // Retrieve billing addresses with related country, city, and state information
        $billing = $user->billingAddresses()->with(['country', 'state', 'city'])->get();
        return BillingAddressResource::collection($billing);
    }

    /**
     * Create billing address
     * Store a new billing address for the currently authenticated user.
     *
     * @authenticated
     *
     * @bodyParam country int required The ID of the country for the address.
     * @bodyParam address string required The address information.
     * @bodyParam city int required The ID of the city for the address.
     * @bodyParam state int required The ID of the state for the address.
     *
     * @response {
     *     "data": {
     *         "id": 1,
     *         "country": {...},
     *         "address": "123 Main St",
     *         "city": {...},
     *         "state": {...},
     *     },
     *     "message": "Billing address created successfully"
     * }
     * @response 500 {
     *     "message": "Oops something went wrong"
     * }
     *
     * @param BillingAddressRequest $request The billing address request.
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(BillingAddressRequest $request)
    {
        //
        try {
            $validatedData = $request->validated();

            // Retrieve the currently authenticated user
            $user = User::current();

            // Create a new billing address for the user
            $billing = new BillingAddress();
            $billing->country_id = $validatedData['country'];
            $billing->address = $validatedData['address'];
            $billing->city_id = $validatedData['city'];
            $billing->state_id = $validatedData['state'];
            $billingAddress = $user->billingAddresses()->save($billing);

            return response()->json([
                'data' => new BillingAddressResource($billingAddress),
                'message' => 'Billing address created successfully'
            ], 201);
        } catch (\Exception $exception) {
            return response()->json(['message' => 'Oops something went wrong'], 500);
        }
    }

    /**
     * Update billing address
     * Update an existing billing address for the currently authenticated user.
     *
     * @authenticated
     *
     * @bodyParam country int required The ID of the country for the address.
     * @bodyParam address string required The address information.
     * @bodyParam city int required The ID of the city for the address.
     * @bodyParam state int required The ID of the state for the address.
     *
     * @param BillingAddressRequest $request The billing address request.
     * @param BillingAddress $billingAddress The billing address to update.
     *
     * @response {
     *     "data": {
     *         "id": 1,
     *         "country": {...},
     *         "address": "123 Main St",
     *         "city": {...},
     *         "state": {...},
     *     },
     *     "message": "Billing address updated successfully"
     * }
     * @response 422 {
     *     "errors": {...}
     * }
     * @response 500 {
     *     "message": "Oops something went wrong"
     * }
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(BillingAddressRequest $request, BillingAddress $billingAddress)
    {
        try {
            $validatedData = $request->validated();

            // Assuming the relationships are properly defined in the models
            $billingAddress->country_id = $validatedData['country'];
            $billingAddress->address = $validatedData['address'];
            $billingAddress->city_id = $validatedData['city'];
            $billingAddress->state_id = $validatedData['state'];
            $billingAddress->save();

            return response()->json([
                'data' => new BillingAddressResource($billingAddress),
                'message' => 'Billing address updated successfully'
            ]);
        } catch (ValidationException $validationException) {
            return response()->json(['errors' => $validationException->errors()], 422);
        } catch (\Exception $exception) {
            return response()->json('Oops something went wrong', 500);
        }
    }

    /**
     * Delete a billing address.
     *
     * @param string $id The ID of the billing address to delete.
     *
     * @response 200 {
     *     "message": "Billing address deleted successfully"
     * }
     * @response 404 {
     *     "message": "Billing address not found"
     * }
     * @response 500 {
     *     "message": "Oops something went wrong"
     * }
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(string $id)
    {
        //
        try {
            $deleted = BillingAddress::destroy($id);

            if ($deleted) {
                return response()->json(['message' => 'Billing address deleted successfully'], 200);
            } else {
                return response()->json(['message' => 'Billing address not found'], 404);
            }
        }
        catch (\Exception $exception) {
            return response()->json('Oops something went wrong', 500);
        }
    }
}
