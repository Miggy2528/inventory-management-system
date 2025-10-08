<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;
use Illuminate\Support\Str;
use App\Models\Unit;

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
        'price_per_package',
        'is_sold_by_package',
        'selling_price',
        'total_weight',
        'storage_location',
        'expiration_date',
        'source',
        'grade',
        'processing_date',
        'notes',
        'buying_price',
        'quantity_alert'
    ];

    public $sortable = [
        'name',
        'code',
        'quantity',
        'weight_per_unit',
        'price_per_kg',
        'selling_price',
        'total_weight',
        'expiration_date',
        'processing_date'
    ];

    protected $casts = [
        'expiration_date' => 'date',
        'processing_date' => 'date',
        'weight_per_unit' => 'decimal:2',
        'price_per_kg' => 'decimal:2',
        'price_per_package' => 'decimal:2',
        'is_sold_by_package' => 'boolean',
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

    protected static function booted()
    {
        // Auto-generate unique slug on create if not provided
        static::creating(function (Product $product) {
            if (empty($product->slug) && !empty($product->name)) {
                $product->slug = static::generateUniqueSlug(Str::slug($product->name));
            }

            // Auto-assign selling price based on type
            if ($product->is_sold_by_package) {
                $product->selling_price = $product->price_per_package;
            } else {
                $product->selling_price = $product->price_per_kg;
            }

            // Auto-assign default unit for packaged products
            if ($product->is_sold_by_package && empty($product->unit_id)) {
                $defaultUnitId = Unit::where('name', 'like', '%package%')->value('id');
                if ($defaultUnitId) {
                    $product->unit_id = $defaultUnitId;
                }
            }
        });

        // Keep slug unique if name changes and slug not explicitly set
        static::updating(function (Product $product) {
            if ($product->isDirty('name') && empty($product->slug)) {
                $product->slug = static::generateUniqueSlug(Str::slug($product->name), $product->id);
            }

            // Update selling price when price fields change
            if ($product->isDirty(['price_per_package', 'price_per_kg', 'is_sold_by_package'])) {
                if ($product->is_sold_by_package) {
                    $product->selling_price = $product->price_per_package;
                } else {
                    $product->selling_price = $product->price_per_kg;
                }
            }
        });

        static::created(function ($product) {
            if ($product->quantity > 0) {
                \App\Models\InventoryMovement::create([
                    'product_id' => $product->id,
                    'type' => 'in',
                    'quantity' => $product->quantity,
                ]);
            }
        });
    }

    protected static function generateUniqueSlug(string $baseSlug, ?int $ignoreId = null): string
    {
        $slug = $baseSlug;
        $counter = 2;

        while (static::query()
            ->when($ignoreId, function ($q) use ($ignoreId) { $q->where('id', '!=', $ignoreId); })
            ->where('slug', $slug)
            ->exists()) {
            $slug = $baseSlug.'-'.($counter++);
        }

        return $slug;
    }
    // Note: meatCut relationship removed as products table doesn't have meat_cut_id field
    // Use separate meat_cuts table for meat-specific products

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
    $movements = $this->relationLoaded('inventoryMovements')
        ? $this->inventoryMovements
        : $this->inventoryMovements()->get();

    $in = $movements->where('type', 'in')->sum('quantity');
    $out = $movements->where('type', 'out')->sum('quantity');

    return $in - $out;
}

    public function meatCut()
{
    return $this->belongsTo(MeatCut::class);
}

} 