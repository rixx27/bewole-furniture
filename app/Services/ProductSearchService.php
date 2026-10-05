<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class ProductSearchService
{
    /**
     * Indonesian to English (and vice versa) furniture synonyms & material mappings.
     */
    protected static array $synonymMap = [
        // Category Synonyms
        'kursi' => ['chair', 'stool', 'seat', 'armchair', 'bangkang', 'bangku'],
        'bangku' => ['stool', 'chair', 'bench', 'kursi'],
        'meja' => ['table', 'desk', 'meja kopi', 'meja makan', 'meja konsol'],
        'lemari' => ['wardrobe', 'cabinet', 'bookcase', 'book case', 'cupboard'],
        'rak' => ['book case', 'bookcase', 'shelf', 'rack', 'lemari'],
        'ranjang' => ['bed', 'tempat tidur', 'dipan'],
        'dipan' => ['bed', 'ranjang', 'tempat tidur'],
        'tidur' => ['bed', 'ranjang'],
        'sofa' => ['lazy chair', 'arm chair', 'chair', 'lounge'],

        // Reverse Category Synonyms
        'chair' => ['kursi', 'dining chair', 'arm chair', 'lazy chair'],
        'table' => ['meja', 'dining table', 'coffe table', 'console table', 'bar table'],
        'stool' => ['kursi', 'bangku', 'bar stool'],
        'wardrobe' => ['lemari', 'lemari pakaian'],
        'bed' => ['ranjang', 'tempat tidur', 'dipan'],

        // Material Synonyms
        'jati' => ['teak', 'teak wood', 'kayu jati'],
        'kayu jati' => ['teak', 'teak wood'],
        'kayu' => ['wood', 'teak', 'suar', 'solid wood'],
        'rotan' => ['rattan', 'anyaman'],
        'anyaman' => ['rotan', 'rattan', 'rope'],
        'mahoni' => ['mahogany', 'mahogani'],
        'marmer' => ['marble', 'trafentin', 'travertine'],
        'kulit' => ['leather'],
        'tali' => ['rope'],
        'besi' => ['iron', 'metal', 'steel'],

        // Reverse Material Synonyms
        'teak' => ['jati', 'kayu jati', 'teak wood'],
        'rattan' => ['rotan', 'anyaman'],
        'leather' => ['kulit'],
        'marble' => ['marmer'],
        'rope' => ['tali', 'anyaman'],
    ];

    /**
     * Expand query into search terms including synonyms.
     */
    public function expandTerms(string $query): array
    {
        $normalized = mb_strtolower(trim($query));
        if (empty($normalized)) {
            return [
                'raw' => '',
                'terms' => [],
                'expanded' => [],
            ];
        }

        // Split into words
        $words = preg_split('/\s+/', $normalized);
        $words = array_filter($words, fn($w) => mb_strlen($w) >= 2);

        $expanded = [];
        foreach ($words as $word) {
            $expanded[] = $word;
            if (isset(static::$synonymMap[$word])) {
                foreach (static::$synonymMap[$word] as $syn) {
                    $expanded[] = $syn;
                }
            }
        }

        // Check multi-word synonyms (e.g. "kayu jati", "arm chair")
        foreach (static::$synonymMap as $key => $syns) {
            if (str_contains($normalized, $key)) {
                foreach ($syns as $syn) {
                    $expanded[] = $syn;
                }
            }
        }

        return [
            'raw' => $normalized,
            'terms' => array_values(array_unique($words)),
            'expanded' => array_values(array_unique($expanded)),
        ];
    }

    /**
     * Find categories that match the search query or its synonyms.
     */
    public function findMatchingCategories(array $expandedData): Collection
    {
        $terms = $expandedData['expanded'] ?? [];
        if (empty($terms)) {
            return collect();
        }

        return Category::query()
            ->active()
            ->where(function ($q) use ($terms) {
                foreach ($terms as $term) {
                    $q->orWhere('name', 'like', '%' . $term . '%')
                      ->orWhere('slug', 'like', '%' . $term . '%');
                }
            })
            ->withCount(['products' => fn($pq) => $pq->active()])
            ->get();
    }

    /**
     * Detect if user query contains material keywords.
     */
    public function detectMaterials(array $expandedData): array
    {
        $terms = $expandedData['expanded'] ?? [];
        $materialKeywords = ['jati', 'teak', 'rotan', 'rattan', 'marmer', 'marble', 'trafentin', 'leather', 'kulit', 'rope', 'tali', 'mahoni', 'mahogani', 'kayu'];
        
        $matched = [];
        foreach ($terms as $t) {
            foreach ($materialKeywords as $mk) {
                if (str_contains($t, $mk)) {
                    $matched[] = $mk;
                }
            }
        }

        return array_values(array_unique($matched));
    }

    /**
     * Build the relevance ranking query for Product search.
     * 
     * Priority:
     * 1. Category match (Weight 100)
     * 2. Material match (Weight 80)
     * 3. Name match (Weight 50)
     * 4. Short Description match (Weight 30)
     * 5. Description match (Weight 20)
     * 6. SKU / Code match (Weight 10)
     */
    public function applySearch(Builder $query, string $rawQuery, string $sort = 'latest'): array
    {
        $expandedData = $this->expandTerms($rawQuery);
        $terms = $expandedData['terms'];
        $expandedTerms = $expandedData['expanded'];

        if (empty($terms)) {
            return [
                'matching_categories' => collect(),
                'detected_materials' => [],
            ];
        }

        $matchingCategories = $this->findMatchingCategories($expandedData);
        $matchingCategoryIds = $matchingCategories->pluck('id')->toArray();
        $detectedMaterials = $this->detectMaterials($expandedData);

        // Filter products matching any of the raw or expanded terms
        $query->where(function ($mainQ) use ($terms, $expandedTerms, $matchingCategoryIds) {
            // Check direct terms across fields
            foreach ($terms as $term) {
                $searchStr = '%' . $term . '%';
                $mainQ->orWhere('products.name', 'like', $searchStr)
                      ->orWhere('products.material', 'like', $searchStr)
                      ->orWhere('products.short_description', 'like', $searchStr)
                      ->orWhere('products.description', 'like', $searchStr)
                      ->orWhere('products.sku', 'like', $searchStr)
                      ->orWhereHas('category', fn($cq) => $cq->where('name', 'like', $searchStr));
            }

            // Also check expanded synonyms on categories & materials
            if (!empty($matchingCategoryIds)) {
                $mainQ->orWhereIn('products.category_id', $matchingCategoryIds);
            }

            foreach ($expandedTerms as $exTerm) {
                $exSearch = '%' . $exTerm . '%';
                $mainQ->orWhere('products.material', 'like', $exSearch)
                      ->orWhereHas('category', fn($cq) => $cq->where('name', 'like', $exSearch));
            }
        });

        // Relevance Scoring when sort is 'latest' (default search order)
        if ($sort === 'latest') {
            $scoreCases = [];
            $bindings = [];

            // Category match (highest weight)
            if (!empty($matchingCategoryIds)) {
                $idList = implode(',', array_map('intval', $matchingCategoryIds));
                $scoreCases[] = "CASE WHEN products.category_id IN ({$idList}) THEN 100 ELSE 0 END";
            }

            // Material match (high weight)
            $materialConditions = [];
            foreach ($expandedTerms as $term) {
                $materialConditions[] = "products.material LIKE ?";
                $bindings[] = '%' . $term . '%';
            }
            if (!empty($materialConditions)) {
                $scoreCases[] = "CASE WHEN (" . implode(' OR ', $materialConditions) . ") THEN 80 ELSE 0 END";
            }

            // Name match (medium-high weight)
            $nameConditions = [];
            foreach ($terms as $term) {
                $nameConditions[] = "products.name LIKE ?";
                $bindings[] = '%' . $term . '%';
            }
            if (!empty($nameConditions)) {
                $scoreCases[] = "CASE WHEN (" . implode(' OR ', $nameConditions) . ") THEN 50 ELSE 0 END";
            }

            // Short Description match
            $shortDescConditions = [];
            foreach ($terms as $term) {
                $shortDescConditions[] = "products.short_description LIKE ?";
                $bindings[] = '%' . $term . '%';
            }
            if (!empty($shortDescConditions)) {
                $scoreCases[] = "CASE WHEN (" . implode(' OR ', $shortDescConditions) . ") THEN 30 ELSE 0 END";
            }

            // Description match
            $descConditions = [];
            foreach ($terms as $term) {
                $descConditions[] = "products.description LIKE ?";
                $bindings[] = '%' . $term . '%';
            }
            if (!empty($descConditions)) {
                $scoreCases[] = "CASE WHEN (" . implode(' OR ', $descConditions) . ") THEN 20 ELSE 0 END";
            }

            // SKU match
            $skuConditions = [];
            foreach ($terms as $term) {
                $skuConditions[] = "products.sku LIKE ?";
                $bindings[] = '%' . $term . '%';
            }
            if (!empty($skuConditions)) {
                $scoreCases[] = "CASE WHEN (" . implode(' OR ', $skuConditions) . ") THEN 10 ELSE 0 END";
            }

            if (!empty($scoreCases)) {
                $relevanceSql = '(' . implode(' + ', $scoreCases) . ')';
                $query->select('products.*')
                      ->selectRaw("{$relevanceSql} as search_relevance", $bindings)
                      ->orderByDesc('search_relevance')
                      ->orderBy('products.sort_order', 'asc')
                      ->orderByDesc('products.id');
            }
        }

        return [
            'matching_categories' => $matchingCategories,
            'detected_materials' => $detectedMaterials,
        ];
    }

    /**
     * Get suggestions for navbar autocomplete.
     */
    public function getSuggestions(string $rawQuery, int $limit = 6): array
    {
        $expandedData = $this->expandTerms($rawQuery);
        if (empty($expandedData['terms'])) {
            return [
                'categories' => [],
                'products' => [],
                'detected_materials' => [],
            ];
        }

        $matchingCategories = $this->findMatchingCategories($expandedData)->take(3);
        $detectedMaterials = $this->detectMaterials($expandedData);

        $query = Product::query()->active()->with('category');
        $this->applySearch($query, $rawQuery, 'latest');

        $products = $query->take($limit)->get();

        $categoryResults = $matchingCategories->map(fn($cat) => [
            'id' => $cat->id,
            'name' => $cat->name,
            'slug' => $cat->slug,
            'url' => route('products.index', ['selectedCategory' => $cat->slug]),
            'product_count' => $cat->products_count ?? 0,
        ])->values()->toArray();

        $productResults = $products->map(function ($p) use ($detectedMaterials, $expandedData) {
            // Check if product's material matches detected search materials
            $isMaterialMatch = false;
            $matchedMaterialLabel = null;

            if (!empty($p->material)) {
                foreach ($expandedData['expanded'] as $term) {
                    if (stripos($p->material, $term) !== false) {
                        $isMaterialMatch = true;
                        $matchedMaterialLabel = $p->material;
                        break;
                    }
                }
            }

            return [
                'name' => $p->name,
                'slug' => $p->slug,
                'price' => $p->discount_price
                    ? 'Rp ' . number_format($p->discount_price, 0, ',', '.')
                    : 'Rp ' . number_format($p->price, 0, ',', '.'),
                'thumbnail' => $p->thumbnail,
                'category_name' => $p->category?->name ?? 'Bewole',
                'material' => $p->material,
                'is_material_match' => $isMaterialMatch,
                'matched_material_label' => $matchedMaterialLabel,
                'url' => route('products.show', $p->slug),
            ];
        })->values()->toArray();

        return [
            'categories' => $categoryResults,
            'products' => $productResults,
            'detected_materials' => $detectedMaterials,
        ];
    }
}
