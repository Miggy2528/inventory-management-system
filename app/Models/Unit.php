<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class Unit extends Model
{
    use HasFactory, Sortable;

    protected $fillable = [
        'name',
        'short_name',
        'description'
    ];

    public $sortable = [
        'name',
        'short_name',
        'created_at'
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }
} 