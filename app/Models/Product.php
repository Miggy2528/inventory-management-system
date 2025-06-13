<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class Product extends Model
{
    use HasFactory, Sortable;

    protected $fillable = [
        'name',
        'slug',
        'code',
        'category_id',
        'unit_id',
        'meat_cut_id',
        'quantity',
        'weight_per_unit',
        'price_per_kg',
        'total_weight',
        'storage_location',
        'expiration_date',
        'source',
        'grade',
        'processing_date',
        'notes'
    ];

    public $sortable = [
        'name',
        'code',
        'quantity',
        'weight_per_unit',
        'price_per_kg',
        'total_weight',
        'expiration_date',
        'processing_date'
    ];

    protected $casts = [
        'expiration_date' => 'date',
        'processing_date' => 'date',
        'weight_per_unit' => 'decimal:2',
        'price_per_kg' => 'decimal:2',
        'total_weight' => 'decimal:2'
    ];

    public function scopeSearch($query, $value)
    {
        return $query->where('name', 'like', "%{$value}%")
            ->orWhere('code', 'like', "%{$value}%")
            ->orWhereHas('meatCut', function($q) use ($value) {
                $q->where('name', 'like', "%{$value}%");
            });
    }

    public function meatCut()
    {
        return $this->belongsTo(MeatCut::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function inventoryMovements()
    {
        return $this->hasMany(InventoryMovement::class);
    }

    public function getCurrentStockAttribute()
    {
        return $this->inventoryMovements()
            ->where('type', 'in')
            ->sum('quantity') - 
            $this->inventoryMovements()
            ->where('type', 'out')
            ->sum('quantity');
    }
} 