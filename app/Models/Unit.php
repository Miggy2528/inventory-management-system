<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;
use Illuminate\Support\Str;

class Unit extends Model
{
    use HasFactory, Sortable;

    protected $fillable = [
        'name',
        'slug',
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

    protected static function booted()
    {
        static::creating(function (Unit $unit) {
            if (empty($unit->slug) && !empty($unit->name)) {
                $unit->slug = Str::slug($unit->name);
            }
        });
    }
} 