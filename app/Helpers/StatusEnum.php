<?php

namespace App\Helpers;

enum StatusEnum
{
    const PENDING = 'PENDING';
    const APPROVED = 'APPROVED';
    const REJECTED = 'REJECTED';

    const AWAITING_SHIPMENT = 'PAID, AWAITING SHIPMENT';
    const SUCCESS = 'SUCCESS';
    const PAID = 'PAID';

    const DELIVERED = 'DELIVERED';
    const SHIPPED = 'SHIPPED, AWAITING DELIVERY';
}
