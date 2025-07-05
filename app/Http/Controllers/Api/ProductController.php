<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\DataTableController;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    /**
     * Get products data for DataTable
     */
    public function index(Request $request): JsonResponse
    {
        // Get raw product data
        $products = $this->getProductsData();

        // Get column configuration from request (sent by widget)
        $columns = $request->input('columns', []);

        // Use DataTableController to process the data
        $dataTableController = new DataTableController();
        $result = $dataTableController->process($request, $products, $columns);

        return response()->json($result);
    }

    /**
     * Get product count for auto-lazy detection
     */
    public function count(): JsonResponse
    {
        $count = count($this->getProductsData());
        return response()->json(['count' => $count]);
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
     * Get products with various data types for demo
     */
    public function datatypes(): JsonResponse
    {
        $products = [
            [
                'id' => '1',
                'name' => 'Premium Wireless Headphones with Noise Cancellation',
                'price' => 299.99,
                'cost' => 150.50,
                'discount' => 15.5,
                'quantity' => 125,
                'createdDate' => '2024-01-15T10:30:00',
                'lastUpdate' => '2024-12-20T14:45:30',
                'nextReview' => '2025-02-15T09:00:00',
                'openTime' => '09:00:00',
                'image' => 'https://via.placeholder.com/150x150/3B82F6/FFFFFF?text=Headphones',
                'rating' => 4.5
            ],
            [
                'id' => '2',
                'name' => 'Smart Watch Series X',
                'price' => 399.00,
                'cost' => 200.00,
                'discount' => 10.0,
                'quantity' => 89,
                'createdDate' => '2024-02-20T11:15:00',
                'lastUpdate' => '2024-12-21T16:30:00',
                'nextReview' => '2025-03-01T10:00:00',
                'openTime' => '08:30:00',
                'image' => 'https://via.placeholder.com/150x150/10B981/FFFFFF?text=Watch',
                'rating' => 4.8
            ],
            [
                'id' => '3',
                'name' => 'Portable Bluetooth Speaker',
                'price' => 79.99,
                'cost' => 35.75,
                'discount' => 25.0,
                'quantity' => 250,
                'createdDate' => '2024-03-10T09:00:00',
                'lastUpdate' => '2024-12-19T13:20:00',
                'nextReview' => '2025-01-30T14:00:00',
                'openTime' => '10:00:00',
                'image' => 'https://via.placeholder.com/150x150/F59E0B/FFFFFF?text=Speaker',
                'rating' => 4.2
            ],
            [
                'id' => '4',
                'name' => 'Laptop Stand',
                'price' => 49.99,
                'cost' => 20.00,
                'discount' => 5.5,
                'quantity' => 500,
                'createdDate' => '2024-04-05T12:00:00',
                'lastUpdate' => '2024-12-18T10:15:00',
                'nextReview' => '2025-02-10T11:30:00',
                'openTime' => '09:30:00',
                'image' => 'https://via.placeholder.com/150x150/EF4444/FFFFFF?text=Stand',
                'rating' => 3.9
            ],
            [
                'id' => '5',
                'name' => 'USB-C Hub Multiport Adapter with HDMI and Ethernet',
                'price' => 89.99,
                'cost' => 40.00,
                'discount' => 20.0,
                'quantity' => 175,
                'createdDate' => '2024-05-12T14:30:00',
                'lastUpdate' => '2024-12-22T09:45:00',
                'nextReview' => '2025-03-15T15:00:00',
                'openTime' => '08:00:00',
                'image' => 'https://www.icentre.com.mt/wp-content/uploads/2025/01/EPICO-Spello-USB-C-Hub-9in1-space-grey-.jpg',
                'rating' => 4.6
            ]
        ];

        return response()->json($products);
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
