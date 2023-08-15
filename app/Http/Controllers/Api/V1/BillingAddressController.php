<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\BillingAddressRequest;
use App\Http\Resources\V1\BillingAddressResource;
use App\Models\BillingAddress;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BillingAddressController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = User::current();
        $billing = $user->billingAddresses()->with(['country', 'state', 'city'])->get();
        return BillingAddressResource::collection($billing);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(BillingAddressRequest $request)
    {
        //
        $request = (object)$request->validated();
        $user = User::current();
        $billing = new BillingAddress();
        $billing->country_id = $request->country;
        $billing->address = $request->address;
        $billing->city_id = $request->city;
        $billing->state_id = $request->state;
        $billingAddress = $user->billingAddresses()->save($billing);

        return response()->json(['data' => new BillingAddressResource($billingAddress),'message' => 'Billing address created successfully'], 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(BillingAddress $billingAddress,BillingAddressRequest $request)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
