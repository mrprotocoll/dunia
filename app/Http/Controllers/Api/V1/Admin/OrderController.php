<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\OrderRequest;
use App\Http\Resources\V1\OrderResource;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Stripe\Event;
use Stripe\StripeClient;

/**
 * @group Order Management
 *
 * Endpoint to manage customers orders
 */
class OrderController extends Controller
{

    public function index() {
        try {
            $orders = Order::with(['user', 'products', 'billing_address'])->paginate();
            return OrderResource::collection($orders);
        }
        catch (\Exception $exception) {
            return response()->json('Oops something went wrong', 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        //
    }

}
