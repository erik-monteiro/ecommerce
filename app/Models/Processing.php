<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Processing extends Model
{
    use HasFactory;
    protected $fillable = [
        'client_id',
        'client_name',
        'client_address',
        'order_details',
        'amount',
        'currency',
        'created_at',
        'updated_at'
    ];
}
