<?php

namespace App\Helpers;

enum StatusEnum
{
    const PENDING = 'PENDING';
    const APPROVED = 'APPROVED';
    const FAILED = 'FAILED';

    const AWAITING_SHIPMENT = 'PAID, AWAITING SHIPMENT';
    const SUCCESS = 'SUCCESS';
    const PAID = 'PAID';

    const DELIVERED = 'DELIVERED';
    const SHIPPED = 'SHIPPED, AWAITING DELIVERY';
}
