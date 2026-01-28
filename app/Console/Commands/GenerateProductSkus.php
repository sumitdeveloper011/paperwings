<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class GenerateProductSkus extends Command
{
    protected $signature = 'products:generate-skus {--force : Force regenerate SKUs for products that already have one}';

    protected $description = 'Generate SKUs for all products that don\'t have one';

    public function handle()
    {
        $force = $this->option('force');
        
        $query = Product::query();
        
        if (!$force) {
            $query->whereNull('sku');
        }
        
        $products = $query->with('category')->get();
        
        if ($products->isEmpty()) {
            $this->info('No products found that need SKU generation.');
            return 0;
        }
        
        $this->info("Generating SKUs for {$products->count()} products...");
        
        $bar = $this->output->createProgressBar($products->count());
        $bar->start();
        
        $generated = 0;
        $skipped = 0;
        
        foreach ($products as $product) {
            if (!$force && $product->sku) {
                $skipped++;
                $bar->advance();
                continue;
            }
            
            $sku = Product::generateSku($product->category);
            
            $product->update(['sku' => $sku]);
            $generated++;
            
            $bar->advance();
        }
        
        $bar->finish();
        $this->newLine();
        
        $this->info("✓ Generated {$generated} SKUs");
        if ($skipped > 0) {
            $this->info("⊘ Skipped {$skipped} products (already have SKU)");
        }
        
        return 0;
    }
}
