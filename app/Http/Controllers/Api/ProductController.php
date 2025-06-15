<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    /**
     * Get products data for DataTable
     */
    public function index(Request $request): JsonResponse
    {
        // Product data (you would normally get this from a database)
        $products = $this->getProductsData();

        // Handle server-side processing
        $page = $request->input('page', 0);
        $rows = $request->input('rows', 10);
        $sortField = $request->input('sortField');
        $sortOrder = $request->input('sortOrder', 1);
        $globalFilter = $request->input('globalFilter', '');

        // Apply global filter
        if (!empty($globalFilter)) {
            $products = array_filter($products, function ($product) use ($globalFilter) {
                $searchStr = strtolower($globalFilter);
                return str_contains(strtolower($product['name']), $searchStr) ||
                    str_contains(strtolower($product['category']), $searchStr) ||
                    str_contains(strtolower($product['code']), $searchStr);
            });
        }

        // Apply sorting
        if ($sortField) {
            usort($products, function ($a, $b) use ($sortField, $sortOrder) {
                $aValue = $a[$sortField] ?? '';
                $bValue = $b[$sortField] ?? '';

                if ($sortOrder == 1) {
                    return $aValue <=> $bValue;
                } else {
                    return $bValue <=> $aValue;
                }
            });
        }

        // Reset array keys after filtering
        $products = array_values($products);

        // Calculate pagination
        $totalRecords = count($products);
        $start = $page * $rows;
        $paginatedProducts = array_slice($products, $start, $rows);

        return response()->json([
            'data' => $paginatedProducts,
            'total' => $totalRecords
        ]);
    }

    /**
     * Get mini product dataset
     */
    public function mini(): JsonResponse
    {
        $products = array_slice($this->getProductsData(), 0, 5);
        return response()->json($products);
    }

    /**
     * Get small product dataset
     */
    public function small(): JsonResponse
    {
        $products = array_slice($this->getProductsData(), 0, 10);
        return response()->json($products);
    }

    /**
     * Get full product dataset
     */
    public function all(): JsonResponse
    {
        return response()->json($this->getProductsData());
    }

    /**
     * Product data array
     */
    private function getProductsData(): array
    {
        return [
            [
                'id' => '1000',
                'code' => 'f230fh0g3',
                'name' => 'Bamboo Watch',
                'description' => 'Product Description',
                'image' => 'bamboo-watch.jpg',
                'price' => 65,
                'category' => 'Accessories',
                'quantity' => 24,
                'inventoryStatus' => 'INSTOCK',
                'rating' => 5
            ],
            [
                'id' => '1001',
                'code' => 'nvklal433',
                'name' => 'Black Watch',
                'description' => 'Product Description',
                'image' => 'black-watch.jpg',
                'price' => 72,
                'category' => 'Accessories',
                'quantity' => 61,
                'inventoryStatus' => 'INSTOCK',
                'rating' => 4
            ],
            [
                'id' => '1002',
                'code' => 'zz21cz3c1',
                'name' => 'Blue Band',
                'description' => 'Product Description',
                'image' => 'blue-band.jpg',
                'price' => 79,
                'category' => 'Fitness',
                'quantity' => 2,
                'inventoryStatus' => 'LOWSTOCK',
                'rating' => 3
            ],
            [
                'id' => '1003',
                'code' => '244wgerg2',
                'name' => 'Blue T-Shirt',
                'description' => 'Product Description',
                'image' => 'blue-t-shirt.jpg',
                'price' => 29,
                'category' => 'Clothing',
                'quantity' => 25,
                'inventoryStatus' => 'INSTOCK',
                'rating' => 5
            ],
            [
                'id' => '1004',
                'code' => 'h456wer53',
                'name' => 'Bracelet',
                'description' => 'Product Description',
                'image' => 'bracelet.jpg',
                'price' => 15,
                'category' => 'Accessories',
                'quantity' => 73,
                'inventoryStatus' => 'INSTOCK',
                'rating' => 4
            ],
            [
                'id' => '1005',
                'code' => 'av2231fwg',
                'name' => 'Brown Purse',
                'description' => 'Product Description',
                'image' => 'brown-purse.jpg',
                'price' => 120,
                'category' => 'Accessories',
                'quantity' => 0,
                'inventoryStatus' => 'OUTOFSTOCK',
                'rating' => 4
            ],
            [
                'id' => '1006',
                'code' => 'bib36pfvm',
                'name' => 'Chakra Bracelet',
                'description' => 'Product Description',
                'image' => 'chakra-bracelet.jpg',
                'price' => 32,
                'category' => 'Accessories',
                'quantity' => 5,
                'inventoryStatus' => 'LOWSTOCK',
                'rating' => 3
            ],
            [
                'id' => '1007',
                'code' => 'mbvjkgip5',
                'name' => 'Galaxy Earrings',
                'description' => 'Product Description',
                'image' => 'galaxy-earrings.jpg',
                'price' => 34,
                'category' => 'Accessories',
                'quantity' => 23,
                'inventoryStatus' => 'INSTOCK',
                'rating' => 5
            ],
            [
                'id' => '1008',
                'code' => 'vbb124btr',
                'name' => 'Game Controller',
                'description' => 'Product Description',
                'image' => 'game-controller.jpg',
                'price' => 99,
                'category' => 'Electronics',
                'quantity' => 2,
                'inventoryStatus' => 'LOWSTOCK',
                'rating' => 4
            ],
            [
                'id' => '1009',
                'code' => 'cm230f032',
                'name' => 'Gaming Set',
                'description' => 'Product Description',
                'image' => 'gaming-set.jpg',
                'price' => 299,
                'category' => 'Electronics',
                'quantity' => 63,
                'inventoryStatus' => 'INSTOCK',
                'rating' => 3
            ],
            // ... Add the rest of the products from your data file here
            // I'm truncating for brevity, but you should include all 30 products
        ];
    }
}
