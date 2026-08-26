<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function suggest(Request $request)
    {
        $query = trim($request->get('q', ''));

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $terms = array_filter(explode(' ', $query), fn($v) => !empty(trim($v)));

        $products = Product::query()
            ->active()
            ->with('category')
            ->where(function ($q) use ($terms) {
                foreach ($terms as $term) {
                    $search = '%' . $term . '%';
                    $q->where(function ($subQ) use ($search) {
                        $subQ->where('name', 'like', $search)
                            ->orWhere('short_description', 'like', $search)
                            ->orWhere('material', 'like', $search)
                            ->orWhere('sku', 'like', $search)
                            ->orWhereHas('category', fn($cq) => $cq->where('name', 'like', $search));
                    });
                }
            })
            ->latest()
            ->take(6)
            ->get();

        $results = $products->map(fn($p) => [
            'name'          => $p->name,
            'slug'          => $p->slug,
            'price'         => $p->discount_price
                                ? 'Rp ' . number_format($p->discount_price, 0, ',', '.')
                                : 'Rp ' . number_format($p->price, 0, ',', '.'),
            'thumbnail'     => $p->thumbnail,
            'category_name' => $p->category?->name ?? 'Bewole',
            'url'           => route('products.show', $p->slug),
        ]);

        return response()->json($results);
    }
}
