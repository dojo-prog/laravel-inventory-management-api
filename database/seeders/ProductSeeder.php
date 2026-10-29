<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductSupplier;
use App\Models\Supplier;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Seed the application's technology products and their supplier links.
     */
    public function run(): void
    {
        $products = [
            [
                'category' => 'Laptops',
                'sku' => 'LAP-MAC-0001',
                'name' => 'MacBook Pro 14" M3 Pro',
                'description' => '14-inch MacBook Pro with M3 Pro chip, 18GB unified memory, 512GB SSD.',
                'unit' => 'pcs',
                'unit_cost' => '1899.00',
                'stock_quantity' => 14,
                'reorder_level' => 5,
                'status' => 'active',
                'suppliers' => [
                    ['name' => 'TechSource Distribution', 'supplier_sku' => 'TSD-10001', 'unit_cost' => '1849.00'],
                    ['name' => 'Global Compute Supply', 'supplier_sku' => 'GCS-10001', 'unit_cost' => '1879.00'],
                ],
            ],
            [
                'category' => 'Laptops',
                'sku' => 'LAP-THK-0002',
                'name' => 'ThinkPad X1 Carbon Gen 12',
                'description' => '14-inch business ultrabook with Intel Core Ultra 7, 32GB RAM, 1TB SSD.',
                'unit' => 'pcs',
                'unit_cost' => '1599.00',
                'stock_quantity' => 9,
                'reorder_level' => 4,
                'status' => 'active',
                'suppliers' => [
                    ['name' => 'Global Compute Supply', 'supplier_sku' => 'GCS-10002', 'unit_cost' => '1585.00'],
                ],
            ],
            [
                'category' => 'Smartphones',
                'sku' => 'SMT-IPH-0003',
                'name' => 'iPhone 15 Pro 256GB',
                'description' => '6.1-inch flagship smartphone with A17 Pro chip, titanium frame, 256GB storage.',
                'unit' => 'pcs',
                'unit_cost' => '999.00',
                'stock_quantity' => 22,
                'reorder_level' => 8,
                'status' => 'active',
                'suppliers' => [
                    ['name' => 'TechSource Distribution', 'supplier_sku' => 'TSD-10002', 'unit_cost' => '949.00'],
                    ['name' => 'Global Compute Supply', 'supplier_sku' => 'GCS-10003', 'unit_cost' => '962.00'],
                ],
            ],
            [
                'category' => 'Smartphones',
                'sku' => 'SMT-SAM-0004',
                'name' => 'Galaxy S24 Ultra 512GB',
                'description' => '6.8-inch flagship smartphone with Snapdragon 8 Gen 3, S Pen, 512GB storage.',
                'unit' => 'pcs',
                'unit_cost' => '1199.00',
                'stock_quantity' => 3,
                'reorder_level' => 6,
                'status' => 'active',
                'suppliers' => [
                    ['name' => 'TechSource Distribution', 'supplier_sku' => 'TSD-10003', 'unit_cost' => '1142.00'],
                ],
            ],
            [
                'category' => 'Monitors & Displays',
                'sku' => 'MON-DEL-0005',
                'name' => 'Dell UltraSharp U2723QE 27"',
                'description' => '27-inch 4K USB-C hub monitor with IPS Black technology.',
                'unit' => 'pcs',
                'unit_cost' => '579.00',
                'stock_quantity' => 11,
                'reorder_level' => 4,
                'status' => 'active',
                'suppliers' => [
                    ['name' => 'ScreenCraft Ltd.', 'supplier_sku' => 'SCL-10001', 'unit_cost' => '552.00'],
                ],
            ],
            [
                'category' => 'Monitors & Displays',
                'sku' => 'MON-LGP-0006',
                'name' => 'LG UltraGear 27GP950-B 27"',
                'description' => '27-inch 4K 144Hz gaming monitor with HDMI 2.1.',
                'unit' => 'pcs',
                'unit_cost' => '799.00',
                'stock_quantity' => 0,
                'reorder_level' => 3,
                'status' => 'inactive',
                'suppliers' => [
                    ['name' => 'ScreenCraft Ltd.', 'supplier_sku' => 'SCL-10002', 'unit_cost' => '759.00'],
                ],
            ],
            [
                'category' => 'Peripherals',
                'sku' => 'PER-LOG-0007',
                'name' => 'Logitech MX Master 3S',
                'description' => 'Wireless performance mouse with 8K DPI tracking and quiet clicks.',
                'unit' => 'pcs',
                'unit_cost' => '99.00',
                'stock_quantity' => 45,
                'reorder_level' => 15,
                'status' => 'active',
                'suppliers' => [
                    ['name' => 'TechSource Distribution', 'supplier_sku' => 'TSD-10004', 'unit_cost' => '86.00'],
                ],
            ],
            [
                'category' => 'Peripherals',
                'sku' => 'PER-KEY-0008',
                'name' => 'Keychron K8 Pro',
                'description' => 'Hot-swappable wireless mechanical keyboard with QMK/VIA support.',
                'unit' => 'pcs',
                'unit_cost' => '189.00',
                'stock_quantity' => 18,
                'reorder_level' => 6,
                'status' => 'active',
                'suppliers' => [
                    ['name' => 'Global Compute Supply', 'supplier_sku' => 'GCS-10004', 'unit_cost' => '178.00'],
                ],
            ],
            [
                'category' => 'Audio',
                'sku' => 'AUD-SNY-0009',
                'name' => 'Sony WH-1000XM5',
                'description' => 'Wireless noise-cancelling over-ear headphones with 30-hour battery life.',
                'unit' => 'pcs',
                'unit_cost' => '399.00',
                'stock_quantity' => 25,
                'reorder_level' => 8,
                'status' => 'active',
                'suppliers' => [
                    ['name' => 'AudioHub Wholesale', 'supplier_sku' => 'AHB-10001', 'unit_cost' => '371.00'],
                ],
            ],
            [
                'category' => 'Audio',
                'sku' => 'AUD-APP-0010',
                'name' => 'AirPods Pro 2nd Gen',
                'description' => 'Wireless earbuds with active noise cancellation and USB-C charging case.',
                'unit' => 'pcs',
                'unit_cost' => '249.00',
                'stock_quantity' => 12,
                'reorder_level' => 10,
                'status' => 'active',
                'suppliers' => [
                    ['name' => 'AudioHub Wholesale', 'supplier_sku' => 'AHB-10002', 'unit_cost' => '236.00'],
                ],
            ],
            [
                'category' => 'Storage & Networking',
                'sku' => 'STN-SAM-0011',
                'name' => 'Samsung 990 Pro 1TB NVMe SSD',
                'description' => 'PCIe 4.0 NVMe SSD with up to 7,450 MB/s sequential read speeds.',
                'unit' => 'pcs',
                'unit_cost' => '179.00',
                'stock_quantity' => 29,
                'reorder_level' => 10,
                'status' => 'active',
                'suppliers' => [
                    ['name' => 'NetLink Systems', 'supplier_sku' => 'NLS-10001', 'unit_cost' => '168.00'],
                    ['name' => 'TechSource Distribution', 'supplier_sku' => 'TSD-10005', 'unit_cost' => '172.00'],
                ],
            ],
            [
                'category' => 'Storage & Networking',
                'sku' => 'STN-TPL-0012',
                'name' => 'TP-Link Archer AX55',
                'description' => 'AX3000 dual-band Wi-Fi 6 router with gigabit ports.',
                'unit' => 'pcs',
                'unit_cost' => '129.00',
                'stock_quantity' => 16,
                'reorder_level' => 5,
                'status' => 'active',
                'suppliers' => [
                    ['name' => 'NetLink Systems', 'supplier_sku' => 'NLS-10002', 'unit_cost' => '118.00'],
                ],
            ],
        ];

        $categoryNames = collect($products)
            ->pluck('category')
            ->unique()
            ->values();

        $categories = Category::whereIn('name', $categoryNames)
            ->pluck('id', 'name');

        $supplierNames = collect($products)
            ->pluck('suppliers')
            ->flatten(1)
            ->pluck('name')
            ->unique();

        $suppliers = Supplier::whereIn('name', $supplierNames)
            ->pluck('id', 'name');

        foreach ($products as $data) {
            $product = Product::create([
                'category_id' => $categories[$data['category']],
                'sku' => $data['sku'],
                'name' => $data['name'],
                'description' => $data['description'],
                'unit' => $data['unit'],
                'unit_cost' => $data['unit_cost'],
                'stock_quantity' => $data['stock_quantity'],
                'reorder_level' => $data['reorder_level'],
                'status' => $data['status'],
            ]);

            foreach ($data['suppliers'] as $supplierLink) {
                ProductSupplier::create([
                    'product_id' => $product->id,
                    'supplier_id' => $suppliers[$supplierLink['name']],
                    'supplier_sku' => $supplierLink['supplier_sku'],
                    'unit_cost' => $supplierLink['unit_cost'],
                ]);
            }
        }
    }
}
