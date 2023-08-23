<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\OrderRequest;
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
                'billing_address_id' => $shipping['billing_address_id']
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

    /**
     * Process the checkout for placing an order.
     *
     * @authenticated
     * @param OrderRequest $request The order request.
     *
     * @bodyParam cart array required The array of products in the cart.
     * @bodyParam cart[].product_id int required The ID of the product in the cart.
     * @bodyParam cart[].quantity int required The quantity of the product in the cart.
     * @bodyParam shipping.billing_address_id string shipping address_id if shipping is involved.
     * @bodyParam shipping.price float required The shipping price if shipping is involved.
     * @bodyParam success_url string required The URL to redirect to on successful payment.
     * @bodyParam cancel_url string required The URL to redirect to on payment cancellation.
     *
     * @response {
     *     "url": "https://checkout.stripe.com/session/...",
     *     "message": "Checkout initiated successfully"
     * }
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkout(OrderRequest $request) {
        $request->validated();
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

        if(count($request['shipping']) > 1) {
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

    public function webhook() {
        $payload = @file_get_contents('php://input');
        $event = null;
        $webhookSecret = env('WEBHOOK_SECRET');

        try {
            $event = Event::constructFrom(
                json_decode($payload, true)
            );
        } catch(\UnexpectedValueException $e) {
            // Invalid payload
            response()->json('error',400);
            exit();
        }

        // Handle the event
        switch ($event->type) {
            case 'payment_intent.succeeded':
                $paymentIntent = $event->data->object; // contains a \Stripe\PaymentIntent
                // Then define and call a method to handle the successful payment intent.
                // handlePaymentIntentSucceeded($paymentIntent);
                break;
            case 'payment_method.attached':
                $paymentMethod = $event->data->object; // contains a \Stripe\PaymentMethod
                // Then define and call a method to handle the successful attachment of a PaymentMethod.
                // handlePaymentMethodAttached($paymentMethod);
            break;
            // ... handle other event types
            default:
                echo 'Received unknown event type ' . $event->type;
        }

        response(200);
    }

}
