<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ProductSearchService;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function __construct(
        protected ProductSearchService $searchService
    ) {}

    public function suggest(Request $request)
    {
        $query = trim($request->get('q', ''));

        if (strlen($query) < 2) {
            return response()->json([
                'categories' => [],
                'products' => [],
                'detected_materials' => [],
            ]);
        }

        $results = $this->searchService->getSuggestions($query, 6);

        return response()->json($results);
    }
}
