<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use App\Models\MeatCut;
use App\Models\Unit;
use Illuminate\Support\Str;

class BackfillProductFromMeatCuts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:backfill-products-from-meatcuts {--dry-run : Show what will change without saving}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backfill Product price, quantity, unit, selling_price, and flags based on linked MeatCut';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');

        $updated = 0;

        Product::with('meatCut')->chunkById(200, function ($products) use (&$updated, $dryRun) {
            foreach ($products as $product) {
                /** @var Product $product */
                $cut = $product->meatCut;
                if (!$cut) {
                    continue;
                }

                $isPackaged = (bool) $cut->is_packaged;

                $unitId = $isPackaged
                    ? Unit::firstOrCreate(['name' => 'Package'], ['slug' => Str::slug('Package')])->id
                    : Unit::firstOrCreate(['name' => 'Kilogram'], ['slug' => Str::slug('Kilogram')])->id;

                $newAttributes = [
                    'name' => $cut->name,
                    'slug' => Str::slug($cut->name),
                    'quantity' => $cut->quantity ?? 0,
                    'unit_id' => $unitId,
                    'is_sold_by_package' => $isPackaged,
                    'price_per_package' => $isPackaged ? ($cut->package_price ?? 0) : null,
                    'price_per_kg' => $isPackaged ? null : ($cut->default_price_per_kg ?? 0),
                    'selling_price' => $isPackaged ? ($cut->package_price ?? 0) : ($cut->default_price_per_kg ?? 0),
                ];

                $changes = array_diff_assoc($newAttributes, $product->only(array_keys($newAttributes)));

                if (!empty($changes)) {
                    $this->info("Product #{$product->id} changes: " . json_encode($changes));
                    if (!$dryRun) {
                        $product->fill($newAttributes);
                        $product->save();
                    }
                    $updated++;
                }
            }
        });

        $this->info(($dryRun ? '[DRY RUN] ' : '') . "Updated {$updated} products");

        return Command::SUCCESS;
    }
}


