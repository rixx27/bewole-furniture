<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('products:normalize-sort-order {--category= : ID kategori tertentu}', function (\App\Services\ProductSortOrderService $service) {
    $categoryId = $this->option('category');

    if ($categoryId) {
        $category = \App\Models\Category::find($categoryId);
        if (!$category) {
            $this->error("Kategori dengan ID {$categoryId} tidak ditemukan.");
            return 1;
        }
        $service->normalizeCategoryOrders((int) $categoryId);
        $count = \App\Models\Product::where('category_id', $categoryId)->count();
        $this->info("Berhasil menata urutan produk untuk kategori: {$category->name} ({$count} produk).");
    } else {
        $categories = \App\Models\Category::orderBy('name')->get();
        $total = 0;
        foreach ($categories as $category) {
            $service->normalizeCategoryOrders($category->id);
            $c = \App\Models\Product::where('category_id', $category->id)->count();
            $total += $c;
            $this->line(" - {$category->name}: {$c} produk dinormalisasi.");
        }
        $this->info("Selesai! Seluruh produk ({$total} produk dalam {$categories->count()} kategori) telah dinormalisasi urutannya (1..N).");
    }

    return 0;
})->purpose('Normalisasi urutan produk per kategori (1, 2, 3...)');

