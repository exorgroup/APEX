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

    // DD20250712-1930 BEGIN - Add products with orders for row expansion demo
    /**
     * Get products with nested orders data for row expansion demo
     */
    public function withOrders(): JsonResponse
    {
        return response()->json($this->getProductsWithOrdersData());
    }
    // DD20250712-1930 END

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
                'cost' => 150.75,
                'discount' => 15.5,
                'stock' => 45,
                'rating' => 4.8,
                'releaseDate' => '2023-01-15',
                'lastUpdated' => '2024-01-15 14:30:00',
                'featured' => true,
                'category' => 'Electronics'
            ],
            [
                'id' => '2',
                'name' => 'Ergonomic Office Chair',
                'price' => 549.00,
                'cost' => 275.50,
                'discount' => 10.0,
                'stock' => 12,
                'rating' => 4.6,
                'releaseDate' => '2023-03-20',
                'lastUpdated' => '2024-01-10 09:15:00',
                'featured' => false,
                'category' => 'Furniture'
            ],
            [
                'id' => '3',
                'name' => 'Smart Fitness Tracker',
                'price' => 199.99,
                'cost' => 89.25,
                'discount' => 20.0,
                'stock' => 78,
                'rating' => 4.4,
                'releaseDate' => '2023-05-10',
                'lastUpdated' => '2024-01-12 16:45:00',
                'featured' => true,
                'category' => 'Electronics'
            ]
        ];

        return response()->json($products);
    }

    // DD20250712-1930 BEGIN - Add products with orders data structure
    /**
     * Get products with nested orders for row expansion
     */
    private function getProductsWithOrdersData(): array
    {
        return [
            [
                'id' => '1000',
                'code' => 'f230fh0g3',
                'name' => 'Bamboo Watch',
                'description' => 'Premium bamboo watch with eco-friendly design',
                'image' => 'bamboo-watch.jpg',
                'price' => 65,
                'category' => 'Accessories',
                'quantity' => 24,
                'inventoryStatus' => 'INSTOCK',
                'rating' => 5,
                'orders' => [
                    [
                        'id' => '1000-0',
                        'productCode' => 'f230fh0g3',
                        'date' => '2020-09-13',
                        'amount' => 65,
                        'quantity' => 1,
                        'customer' => 'David James',
                        'status' => 'PENDING'
                    ],
                    [
                        'id' => '1000-1',
                        'productCode' => 'f230fh0g3',
                        'date' => '2020-05-14',
                        'amount' => 130,
                        'quantity' => 2,
                        'customer' => 'Leon Rodrigues',
                        'status' => 'DELIVERED'
                    ],
                    [
                        'id' => '1000-2',
                        'productCode' => 'f230fh0g3',
                        'date' => '2021-02-08',
                        'amount' => 195,
                        'quantity' => 3,
                        'customer' => 'Anna Chen',
                        'status' => 'CANCELLED'
                    ]
                ]
            ],
            [
                'id' => '1001',
                'code' => 'nvklal433',
                'name' => 'Black Watch',
                'description' => 'Sleek black watch with modern design',
                'image' => 'black-watch.jpg',
                'price' => 72,
                'category' => 'Accessories',
                'quantity' => 61,
                'inventoryStatus' => 'INSTOCK',
                'rating' => 4,
                'orders' => [
                    [
                        'id' => '1001-0',
                        'productCode' => 'nvklal433',
                        'date' => '2021-01-20',
                        'amount' => 144,
                        'quantity' => 2,
                        'customer' => 'Maria Garcia',
                        'status' => 'DELIVERED'
                    ],
                    [
                        'id' => '1001-1',
                        'productCode' => 'nvklal433',
                        'date' => '2021-03-15',
                        'amount' => 72,
                        'quantity' => 1,
                        'customer' => 'John Smith',
                        'status' => 'PENDING'
                    ]
                ]
            ],
            [
                'id' => '1002',
                'code' => 'zz21cz3c1',
                'name' => 'Blue Band',
                'description' => 'Comfortable blue fitness band',
                'image' => 'blue-band.jpg',
                'price' => 79,
                'category' => 'Fitness',
                'quantity' => 2,
                'inventoryStatus' => 'LOWSTOCK',
                'rating' => 3,
                'orders' => [
                    [
                        'id' => '1002-0',
                        'productCode' => 'zz21cz3c1',
                        'date' => '2021-04-10',
                        'amount' => 158,
                        'quantity' => 2,
                        'customer' => 'Sarah Wilson',
                        'status' => 'DELIVERED'
                    ]
                ]
            ],
            [
                'id' => '1003',
                'code' => '244wgerg2',
                'name' => 'Blue T-Shirt',
                'description' => 'Comfortable cotton blue t-shirt',
                'image' => 'blue-t-shirt.jpg',
                'price' => 29,
                'category' => 'Clothing',
                'quantity' => 25,
                'inventoryStatus' => 'INSTOCK',
                'rating' => 5,
                'orders' => [
                    [
                        'id' => '1003-0',
                        'productCode' => '244wgerg2',
                        'date' => '2021-05-22',
                        'amount' => 87,
                        'quantity' => 3,
                        'customer' => 'Michael Brown',
                        'status' => 'DELIVERED'
                    ],
                    [
                        'id' => '1003-1',
                        'productCode' => '244wgerg2',
                        'date' => '2021-06-05',
                        'amount' => 58,
                        'quantity' => 2,
                        'customer' => 'Emma Davis',
                        'status' => 'PENDING'
                    ],
                    [
                        'id' => '1003-2',
                        'productCode' => '244wgerg2',
                        'date' => '2021-06-18',
                        'amount' => 29,
                        'quantity' => 1,
                        'customer' => 'Robert Taylor',
                        'status' => 'DELIVERED'
                    ],
                    [
                        'id' => '1003-3',
                        'productCode' => '244wgerg2',
                        'date' => '2021-07-02',
                        'amount' => 116,
                        'quantity' => 4,
                        'customer' => 'Lisa Anderson',
                        'status' => 'CANCELLED'
                    ]
                ]
            ],
            [
                'id' => '1004',
                'code' => 'h456wer53',
                'name' => 'Bracelet',
                'description' => 'Elegant silver bracelet',
                'image' => 'bracelet.jpg',
                'price' => 15,
                'category' => 'Accessories',
                'quantity' => 73,
                'inventoryStatus' => 'INSTOCK',
                'rating' => 4,
                'orders' => [
                    [
                        'id' => '1004-0',
                        'productCode' => 'h456wer53',
                        'date' => '2021-08-12',
                        'amount' => 45,
                        'quantity' => 3,
                        'customer' => 'Jennifer White',
                        'status' => 'DELIVERED'
                    ],
                    [
                        'id' => '1004-1',
                        'productCode' => 'h456wer53',
                        'date' => '2021-09-01',
                        'amount' => 30,
                        'quantity' => 2,
                        'customer' => 'Kevin Johnson',
                        'status' => 'PENDING'
                    ]
                ]
            ]
        ];
    }
    // DD20250712-1930 END

    /**
     * Get basic product data
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
                'code' => '356fgerg3',
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
            ]
        ];
    }
}
