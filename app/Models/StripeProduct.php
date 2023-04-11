<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StripeProduct extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'product_id',
        'active',
        'default_price',
        'description',
        'image',
        'metadata',
        'name',
        'prices',
    ];

    protected $casts = [
        'active' => 'boolean',
        'metadata' => 'array',
        'prices' => 'array',
    ];
}
