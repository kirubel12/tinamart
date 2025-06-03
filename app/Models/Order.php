<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'status',
        'payment_status',
        'total_amount',
        'shipping_address',
        'billing_address',
    ];
}
