<?php

namespace App\Modules\Payment\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'case_id',
        'user_id',
        'stage',
        'amount',
        'currency',
        'status',
        'stripe_payment_intent_id',
        'paid_at',
    ];
}
