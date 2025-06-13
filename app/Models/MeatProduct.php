<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MeatProduct extends Model
{
    use HasFactory;
    protected $fillable = [
    'name',
    'cut_type',
    'weight',
    'expiration_date',
    'supplier',
    'price_per_kilo',
    // other existing fields
];

}
