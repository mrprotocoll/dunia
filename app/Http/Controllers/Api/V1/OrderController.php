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
    public function store(Request $request)
    {
        $user = Auth::user();
        DB::transaction(function () use ($user, $request) {
            $shipping = $request['shipping'];
            $order = $user->orders()->create([
                'total_price' => $request['total'],
                'shipping_price' => $shipping['price'] ?? 0,
                'billing_address_id' => $shipping['destination']
            ]);

            foreach($request['cart'] as $productData){
                $product = Product::findOrFail($productData['product_id']);
                $quantity = intval($productData['quantity']);
                $total_price = $quantity * floatval($product->price);

                $order->products()->attach($product, ['quantity' => $quantity, 'total_price' => $total_price]);
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
            'cart.*.product_id' => ['required', 'exists:products,id'],
            'cart.*.quantity' => ['required', 'numeric', 'gt:0'],
            'shipping' => ['array'],
            'shipping.price' => ['required_if:shipping,*'],
            'shipping.destination' => ['required_if:shipping,*','exists:billing_addresses,id']
        ]);

        $stripe = new StripeClient(env('STRIPE_SECRET'));

        $line_items = [];
        $shipping = $request['shipping'];
        $request['total'] = 0;
        foreach ($request['cart'] as $item) {
            $product = Product::find($item['product_id']);
            // Add print_price to product price if shipping is involved
            $price = $shipping
                ? floatval($product->price) + floatval($product->print_price)
                : floatval($product->price);

            // reset total price of all ordered products in cart
            $request['total'] += $price * intval($item['quantity']);

            // add product data to stripe items
            $line_items[] = [
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => [
                        'name' => $product->name,
                    ],
                    'unit_amount' => $price * 100,
                ],
                'quantity' => $item['quantity'],
            ];
        }

        if($request['shipping']) {
            $line_items[] = [
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => [
                        'name' => 'Shipping',
                    ],
                    'unit_amount' => $shipping['price'] * 100,
                ],
                'quantity' => 1,
            ];
        }

        $checkout_session = $stripe->checkout->sessions->create([
            'line_items' => $line_items,
            'mode' => 'payment',
            'success_url' => $request->success_url,
            'cancel_url' => $request->cancel_url,
        ]);

        if($checkout_session->url) {
            // add a new order
            $this->store($request);
        }

        return response()->json(['url' => $checkout_session->url]);
    }

}
