<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Supplier;

class FixSupplierTypes extends Command
{
    protected $signature = 'suppliers:fix-types';
    protected $description = 'Fix old supplier type values to match the new SupplierType enum';

    public function handle()
    {
        $map = [
            'wholesale' => 'wholesaler',
            'manufacturer' => 'producer',
            'retail' => 'producer', 
        ];

        $suppliers = Supplier::whereIn('type', array_keys($map))->get();

        if ($suppliers->isEmpty()) {
            $this->info('No old supplier types found.');
            return;
        }

        foreach ($suppliers as $supplier) {
            $old = $supplier->type;
            $new = $map[$old] ?? null;
            if ($new) {
                $supplier->update(['type' => $new]);
                $this->info("Updated Supplier #{$supplier->id}: {$old} → {$new}");
            }
        }

        $this->info('Supplier types fixed successfully!');
    }
}
