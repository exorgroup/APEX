<?php
// App\Http\Controllers\Api\ProductController.php

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
     * DD20250710-1240 - Enhanced with conditional styling test data
     */
    public function datatypes(): JsonResponse
    {
        $products = [
            [
                'id' => '1',
                'name' => 'Premium Wireless Headphones with Noise Cancellation',
                'code' => 'PWH001',
                'category' => 'Electronics',
                'price' => 299.99,
                'cost' => 180.50,
                'discount' => 15.0,
                'quantity' => 0, // Out of stock - should trigger red styling
                'inventoryStatus' => 'OUTOFSTOCK',
                'rating' => 4.5,
                'image' => 'headphones.jpg'
            ],
            [
                'id' => '2',
                'name' => 'Smart Fitness Watch Pro',
                'code' => 'SFW002',
                'category' => 'Wearables',
                'price' => 450.00, // Expensive - should trigger blue styling
                'cost' => 280.75,
                'discount' => 10.0,
                'quantity' => 25,
                'inventoryStatus' => 'INSTOCK',
                'rating' => 4.8,
                'image' => 'smartwatch.jpg'
            ],
            [
                'id' => '3',
                'name' => 'Bluetooth Gaming Mouse',
                'code' => 'BGM003',
                'category' => 'Gaming',
                'price' => 89.99,
                'cost' => 45.20,
                'discount' => 5.0,
                'quantity' => 3, // Very low quantity - should trigger yellow styling
                'inventoryStatus' => 'LOWSTOCK',
                'rating' => 4.2,
                'image' => 'gaming-mouse.jpg'
            ],
            [
                'id' => '4',
                'name' => 'Mechanical Keyboard RGB',
                'code' => 'MKR004',
                'category' => 'Gaming',
                'price' => 150.00, // Expensive - should trigger blue styling
                'cost' => 92.30,
                'discount' => 8.0,
                'quantity' => 15,
                'inventoryStatus' => 'INSTOCK',
                'rating' => 4.7,
                'image' => 'mechanical-keyboard.jpg'
            ],
            [
                'id' => '5',
                'name' => 'Portable Phone Charger',
                'code' => 'PPC005',
                'category' => 'Accessories',
                'price' => 25.99,
                'cost' => 12.50,
                'discount' => 0.0,
                'quantity' => 8, // Low stock - should trigger orange styling
                'inventoryStatus' => 'LOWSTOCK',
                'rating' => 4.0,
                'image' => 'phone-charger.jpg'
            ],
            [
                'id' => '6',
                'name' => 'Wireless Speaker Bluetooth',
                'code' => 'WSB006',
                'category' => 'Audio',
                'price' => 75.50,
                'cost' => 38.95,
                'discount' => 12.0,
                'quantity' => 50,
                'inventoryStatus' => 'INSTOCK',
                'rating' => 4.3,
                'image' => 'bluetooth-speaker.jpg'
            ],
            [
                'id' => '7',
                'name' => 'USB-C Hub Multi-Port',
                'code' => 'UCH007',
                'category' => 'Accessories',
                'price' => 45.00,
                'cost' => 22.75,
                'discount' => 0.0,
                'quantity' => 0, // Out of stock - should trigger red styling
                'inventoryStatus' => 'OUTOFSTOCK',
                'rating' => 4.1,
                'image' => 'usb-hub.jpg'
            ],
            [
                'id' => '8',
                'name' => 'Gaming Laptop Stand',
                'code' => 'GLS008',
                'category' => 'Gaming',
                'price' => 65.99,
                'cost' => 31.20,
                'discount' => 20.0,
                'quantity' => 2, // Very low quantity - should trigger yellow styling
                'inventoryStatus' => 'LOWSTOCK',
                'rating' => 4.4,
                'image' => 'laptop-stand.jpg'
            ],
            [
                'id' => '9',
                'name' => 'Smartphone Camera Lens Kit',
                'code' => 'SCL009',
                'category' => 'Photography',
                'price' => 120.00, // Expensive - should trigger blue styling
                'cost' => 68.40,
                'discount' => 15.0,
                'quantity' => 12,
                'inventoryStatus' => 'INSTOCK',
                'rating' => 4.6,
                'image' => 'camera-lens.jpg'
            ],
            [
                'id' => '10',
                'name' => 'Desk Organizer with Wireless Charging',
                'code' => 'DOW010',
                'category' => 'Office',
                'price' => 85.00,
                'cost' => 42.50,
                'discount' => 5.0,
                'quantity' => 1, // Very low quantity - should trigger yellow styling
                'inventoryStatus' => 'LOWSTOCK',
                'rating' => 4.2,
                'image' => 'desk-organizer.jpg'
            ],
            [
                'id' => '11',
                'name' => 'Ergonomic Office Chair',
                'code' => 'EOC011',
                'category' => 'Furniture',
                'price' => 320.00, // Expensive - should trigger blue styling
                'cost' => 198.40,
                'discount' => 10.0,
                'quantity' => 5,
                'inventoryStatus' => 'INSTOCK',
                'rating' => 4.9,
                'image' => 'office-chair.jpg'
            ],
            [
                'id' => '12',
                'name' => 'LED Desk Lamp with Timer',
                'code' => 'LDL012',
                'category' => 'Lighting',
                'price' => 42.99,
                'cost' => 21.50,
                'discount' => 0.0,
                'quantity' => 35,
                'inventoryStatus' => 'INSTOCK',
                'rating' => 4.1,
                'image' => 'desk-lamp.jpg'
            ],
            [
                'id' => '13',
                'name' => 'Tablet Stand Adjustable',
                'code' => 'TSA013',
                'category' => 'Accessories',
                'price' => 28.50,
                'cost' => 14.25,
                'discount' => 0.0,
                'quantity' => 0, // Out of stock - should trigger red styling
                'inventoryStatus' => 'OUTOFSTOCK',
                'rating' => 3.8,
                'image' => 'tablet-stand.jpg'
            ],
            [
                'id' => '14',
                'name' => 'Wireless Earbuds Pro',
                'code' => 'WEP014',
                'category' => 'Audio',
                'price' => 180.00, // Expensive - should trigger blue styling
                'cost' => 108.00,
                'discount' => 25.0,
                'quantity' => 18,
                'inventoryStatus' => 'INSTOCK',
                'rating' => 4.7,
                'image' => 'wireless-earbuds.jpg'
            ],
            [
                'id' => '15',
                'name' => 'Monitor Screen Cleaner Kit',
                'code' => 'MSC015',
                'category' => 'Cleaning',
                'price' => 15.99,
                'cost' => 8.00,
                'discount' => 0.0,
                'quantity' => 4, // Very low quantity - should trigger yellow styling
                'inventoryStatus' => 'LOWSTOCK',
                'rating' => 4.0,
                'image' => 'screen-cleaner.jpg'
            ]
        ];

        return response()->json($products);
    }

    /**
     * Get base product data
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
            [
                'id' => '1010',
                'code' => 'plb34234v',
                'name' => 'Gold Phone Case',
                'description' => 'Product Description',
                'image' => 'gold-phone-case.jpg',
                'price' => 24,
                'category' => 'Accessories',
                'quantity' => 0,
                'inventoryStatus' => 'OUTOFSTOCK',
                'rating' => 4
            ],
            [
                'id' => '1011',
                'code' => '4920nnc2d',
                'name' => 'Green Earbuds',
                'description' => 'Product Description',
                'image' => 'green-earbuds.jpg',
                'price' => 89,
                'category' => 'Electronics',
                'quantity' => 23,
                'inventoryStatus' => 'INSTOCK',
                'rating' => 4
            ],
            [
                'id' => '1012',
                'code' => 'cx11jjwiw',
                'name' => 'Green T-Shirt',
                'description' => 'Product Description',
                'image' => 'green-t-shirt.jpg',
                'price' => 49,
                'category' => 'Clothing',
                'quantity' => 74,
                'inventoryStatus' => 'INSTOCK',
                'rating' => 5
            ],
            [
                'id' => '1013',
                'code' => 'iosomz4js',
                'name' => 'Grey T-Shirt',
                'description' => 'Product Description',
                'image' => 'grey-t-shirt.jpg',
                'price' => 48,
                'category' => 'Clothing',
                'quantity' => 0,
                'inventoryStatus' => 'OUTOFSTOCK',
                'rating' => 3
            ],
            [
                'id' => '1014',
                'code' => 'k8l6j58jl',
                'name' => 'Headphones',
                'description' => 'Product Description',
                'image' => 'headphones.jpg',
                'price' => 175,
                'category' => 'Electronics',
                'quantity' => 8,
                'inventoryStatus' => 'LOWSTOCK',
                'rating' => 5
            ],
            [
                'id' => '1015',
                'code' => 'v435nn85n',
                'name' => 'Light Green T-Shirt',
                'description' => 'Product Description',
                'image' => 'light-green-t-shirt.jpg',
                'price' => 49,
                'category' => 'Clothing',
                'quantity' => 34,
                'inventoryStatus' => 'INSTOCK',
                'rating' => 4
            ],
            [
                'id' => '1016',
                'code' => 'k8l6j58jl',
                'name' => 'Lime Band',
                'description' => 'Product Description',
                'image' => 'lime-band.jpg',
                'price' => 79,
                'category' => 'Fitness',
                'quantity' => 12,
                'inventoryStatus' => 'INSTOCK',
                'rating' => 3
            ],
            [
                'id' => '1017',
                'code' => 'nbm5mv45n',
                'name' => 'Mini Speakers',
                'description' => 'Product Description',
                'image' => 'mini-speakers.jpg',
                'price' => 85,
                'category' => 'Clothing',
                'quantity' => 42,
                'inventoryStatus' => 'INSTOCK',
                'rating' => 4
            ],
            [
                'id' => '1018',
                'code' => 'pxpzczo23',
                'name' => 'Painted Phone Case',
                'description' => 'Product Description',
                'image' => 'painted-phone-case.jpg',
                'price' => 56,
                'category' => 'Accessories',
                'quantity' => 41,
                'inventoryStatus' => 'INSTOCK',
                'rating' => 5
            ],
            [
                'id' => '1019',
                'code' => 'mnb5mb2m5',
                'name' => 'Pink Band',
                'description' => 'Product Description',
                'image' => 'pink-band.jpg',
                'price' => 79,
                'category' => 'Fitness',
                'quantity' => 63,
                'inventoryStatus' => 'INSTOCK',
                'rating' => 4
            ],
            [
                'id' => '1020',
                'code' => 'r23fwf2w3',
                'name' => 'Pink Purse',
                'description' => 'Product Description',
                'image' => 'pink-purse.jpg',
                'price' => 110,
                'category' => 'Accessories',
                'quantity' => 0,
                'inventoryStatus' => 'OUTOFSTOCK',
                'rating' => 4
            ],
            [
                'id' => '1021',
                'code' => 'pxpzczo23',
                'name' => 'Purple Band',
                'description' => 'Product Description',
                'image' => 'purple-band.jpg',
                'price' => 79,
                'category' => 'Fitness',
                'quantity' => 6,
                'inventoryStatus' => 'LOWSTOCK',
                'rating' => 3
            ],
            [
                'id' => '1022',
                'code' => '2c42cb5cb',
                'name' => 'Purple Gemstone Necklace',
                'description' => 'Product Description',
                'image' => 'purple-gemstone-necklace.jpg',
                'price' => 45,
                'category' => 'Accessories',
                'quantity' => 62,
                'inventoryStatus' => 'INSTOCK',
                'rating' => 4
            ],
            [
                'id' => '1023',
                'code' => '5k43kkk23',
                'name' => 'Purple T-Shirt',
                'description' => 'Product Description',
                'image' => 'purple-t-shirt.jpg',
                'price' => 49,
                'category' => 'Clothing',
                'quantity' => 2,
                'inventoryStatus' => 'LOWSTOCK',
                'rating' => 5
            ],
            [
                'id' => '1024',
                'code' => 'lm2tny2k4',
                'name' => 'Shoes',
                'description' => 'Product Description',
                'image' => 'shoes.jpg',
                'price' => 64,
                'category' => 'Clothing',
                'quantity' => 0,
                'inventoryStatus' => 'INSTOCK',
                'rating' => 4
            ],
            [
                'id' => '1025',
                'code' => 'nbm5mv45n',
                'name' => 'Sneakers',
                'description' => 'Product Description',
                'image' => 'sneakers.jpg',
                'price' => 78,
                'category' => 'Clothing',
                'quantity' => 52,
                'inventoryStatus' => 'INSTOCK',
                'rating' => 4
            ],
            [
                'id' => '1026',
                'code' => 'zx4k5l2jl',
                'name' => 'Teal T-Shirt',
                'description' => 'Product Description',
                'image' => 'teal-t-shirt.jpg',
                'price' => 49,
                'category' => 'Clothing',
                'quantity' => 3,
                'inventoryStatus' => 'LOWSTOCK',
                'rating' => 3
            ],
            [
                'id' => '1027',
                'code' => 'acvx872gc',
                'name' => 'Yellow Earbuds',
                'description' => 'Product Description',
                'image' => 'yellow-earbuds.jpg',
                'price' => 89,
                'category' => 'Electronics',
                'quantity' => 35,
                'inventoryStatus' => 'INSTOCK',
                'rating' => 3
            ],
            [
                'id' => '1028',
                'code' => 'tx125ck42',
                'name' => 'Yoga Mat',
                'description' => 'Product Description',
                'image' => 'yoga-mat.jpg',
                'price' => 20,
                'category' => 'Fitness',
                'quantity' => 15,
                'inventoryStatus' => 'INSTOCK',
                'rating' => 5
            ],
            [
                'id' => '1029',
                'code' => 'gwuby345v',
                'name' => 'Yoga Set',
                'description' => 'Product Description',
                'image' => 'yoga-set.jpg',
                'price' => 20,
                'category' => 'Fitness',
                'quantity' => 25,
                'inventoryStatus' => 'INSTOCK',
                'rating' => 8
            ]
        ];
    }
}
