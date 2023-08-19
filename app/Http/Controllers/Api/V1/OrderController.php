<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\OrderRequest;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Stripe\StripeClient;

/**
 * @group Order Management
 *
 * Endpoint to manage customers orders
 */
class OrderController extends Controller
{

    /**
     * Store a newly created resource in storage.
     */
    public function store(OrderRequest $request)
    {
        $user = Auth::user();
        DB::transaction(function () use ($user, $request) {
            $order = $user->orders()->create([]);
            foreach($request->products as $productData){
                $product = Product::findOrFail($productData['id']);
                $quantity = $productData['quantity'];
                $total_price = intval($productData['quantity']) * floatval($product->price);

                $order->products()->attach($product, ['quantity' => $quantity, 'total' => $total_price]);
            }
        });
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        //
    }

    public function checkout(Request $request) {
        $request->validate([
            'success_url' => ['required', 'url'],
            'cancel_url' => ['required', 'url'],
            'cart' => ['required', 'array'],
            'shipping' => []
        ]);

        $stripe = new StripeClient(env('STRIPE_SECRET'));

        $checkout_session = $stripe->checkout->sessions->create([
            'line_items' => [[
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => [
                        'name' => 'T-shirt',
                    ],
                    'unit_amount' => 2000,
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => $request->success_url,
            'cancel_url' => $request->cancel_url,
        ]);
    }

}
