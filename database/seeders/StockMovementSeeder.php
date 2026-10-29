<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\StockMovement;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StockMovementSeeder extends Seeder
{
    /**
     * Seed a consistent stock movement history that reconciles
     * with the seeded stock_quantity of each product.
     */
    public function run(): void
    {
        $admin = User::where('email', 'admin@dojo.com')->firstOrFail();
        $staff = User::where('email', 'staff@dojo.com')->firstOrFail();

        $purchaseOrder = 0;
        $salesOrder = 0;

        // [sku, stock_in, stock_out, adjustment (new_quantity or null)]
        $movements = [
            ['sku' => 'LAP-MAC-0001', 'stock_in' => 20, 'stock_out' => 6, 'adjustment' => null],
            ['sku' => 'LAP-THK-0002', 'stock_in' => 12, 'stock_out' => 3, 'adjustment' => null],
            ['sku' => 'SMT-IPH-0003', 'stock_in' => 30, 'stock_out' => 8, 'adjustment' => null],
            ['sku' => 'SMT-SAM-0004', 'stock_in' => 10, 'stock_out' => 7, 'adjustment' => null],
            ['sku' => 'MON-DEL-0005', 'stock_in' => 15, 'stock_out' => 4, 'adjustment' => null],
            ['sku' => 'MON-LGP-0006', 'stock_in' => 8, 'stock_out' => 8, 'adjustment' => null],
            ['sku' => 'PER-LOG-0007', 'stock_in' => 60, 'stock_out' => 15, 'adjustment' => null],
            ['sku' => 'PER-KEY-0008', 'stock_in' => 24, 'stock_out' => 6, 'adjustment' => null],
            ['sku' => 'AUD-SNY-0009', 'stock_in' => 30, 'stock_out' => 5, 'adjustment' => null],
            ['sku' => 'AUD-APP-0010', 'stock_in' => 20, 'stock_out' => 8, 'adjustment' => null],
            ['sku' => 'STN-SAM-0011', 'stock_in' => 40, 'stock_out' => 10, 'adjustment' => 29],
            ['sku' => 'STN-TPL-0012', 'stock_in' => 20, 'stock_out' => 4, 'adjustment' => null],
        ];

        foreach ($movements as $index => $movement) {
            DB::transaction(function () use ($index, $movement, $admin, $staff, &$purchaseOrder, &$salesOrder) {
                $product = Product::where('sku', $movement['sku'])->firstOrFail();

                $day = now()->startOfDay()->subDays(14)->addDays($index);
                $recordedAt = [
                    'stock-in' => $day->copy()->setTime(9, 0),
                    'stock-out' => $day->copy()->setTime(15, 0),
                    'adjustment' => $day->copy()->setTime(17, 0),
                ];

                $purchaseOrder++;
                $this->record(
                    product: $product,
                    userId: $admin->id,
                    type: 'stock-in',
                    quantity: $movement['stock_in'],
                    previousQuantity: 0,
                    newQuantity: $movement['stock_in'],
                    reason: 'Initial stock receive',
                    reference: sprintf('PO-2026-%03d', $purchaseOrder),
                    recordedAt: $recordedAt['stock-in'],
                );

                $salesOrder++;
                $this->record(
                    product: $product,
                    userId: $staff->id,
                    type: 'stock-out',
                    quantity: $movement['stock_out'],
                    previousQuantity: $movement['stock_in'],
                    newQuantity: $movement['stock_in'] - $movement['stock_out'],
                    reason: 'Order fulfilled',
                    reference: sprintf('SO-2026-%03d', $salesOrder),
                    recordedAt: $recordedAt['stock-out'],
                );

                if ($movement['adjustment'] !== null) {
                    $this->record(
                        product: $product,
                        userId: $staff->id,
                        type: 'adjustment',
                        quantity: $movement['adjustment'],
                        previousQuantity: $movement['stock_in'] - $movement['stock_out'],
                        newQuantity: $movement['adjustment'],
                        reason: 'Damaged unit found during stock count',
                        reference: 'ADJ-2026-001',
                        recordedAt: $recordedAt['adjustment'],
                    );
                }

                $finalQuantity = ($movement['adjustment'] !== null)
                    ? $movement['adjustment']
                    : $movement['stock_in'] - $movement['stock_out'];

                $product->update(['stock_quantity' => $finalQuantity]);
            });
        }
    }

    /**
     * Persist a stock movement with an explicitly recorded timestamp.
     *
     * @param  array-key  $type
     */
    private function record(Product $product, string $userId, string $type, int $quantity, int $previousQuantity, int $newQuantity, string $reason, string $reference, CarbonInterface $recordedAt): StockMovement
    {
        $stockMovement = new StockMovement;
        $stockMovement->fill([
            'product_id' => $product->id,
            'user_id' => $userId,
            'type' => $type,
            'quantity' => $quantity,
            'previous_quantity' => $previousQuantity,
            'new_quantity' => $newQuantity,
            'reason' => $reason,
            'reference' => $reference,
        ]);
        $stockMovement->created_at = $recordedAt;
        $stockMovement->updated_at = $recordedAt;
        $stockMovement->save();

        return $stockMovement;
    }
}
