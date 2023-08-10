<?php

namespace App\Observers;

use App\Models\Order;

class OrderObserver
{
    //
    public function creating(Order $order) {
        // create order number
        $order->order_number = Order::generateOrderNumber();
    }

    public function created(Order $order) {
        // send ORDER RECEIVED email to client

        // send NEW ORDER email to admin
    }
}
