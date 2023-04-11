<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPaymentIntent extends Model
{
    use HasFactory;

    protected $table = 'payment_intents';

    protected $fillable = [
        'user_id',
        'stripe_payment_intent_id',
        'stripe_customer_id',
        'payment_method',
        'status',
        'currency',
        'amount',
        'description',
        'barcode',
        'expiration_date',
        'voucher_url',
        'last_digits',
        'brand',
        'card_id',
        'card_type',
        'receipt_url'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
