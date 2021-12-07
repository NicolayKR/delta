<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Receipts extends Model
{
    use HasFactory;
    protected $fillable = [
        'payment_types',
        'amount',
        'count_guests',
        'remove_before_payment',
        'remove_after_payment',
        'date_rec'
    ];
}
