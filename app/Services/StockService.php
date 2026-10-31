<?php

namespace App\Services;

use App\Models\ProductStock;
use App\Models\StockMovement;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class StockService
{
    /**
     * Increase stock (Purchase)
     */
    public function increaseStock(
        int $productId,
        float $quantity,
        int $unitId,
        ?int $referenceId = null,
        string $referenceType = 'Purchase',
        ?string $note = null
    ): void {

        DB::transaction(function () use (
            $productId,
            $quantity,
            $unitId,
            $referenceId,
            $referenceType,
            $note
        ) {

            $product = Product::findOrFail($productId);

            $baseQty = $this->convertToBaseUnit($product, $quantity, $unitId);

            $baseUnitId = $product->base_unit_id ?? DEFAULT_UNIT;

            $stock = ProductStock::firstOrCreate(
                ['product_id' => $productId],
                [
                    'quantity' => 0,
                    'unit_id' => $baseUnitId
                ]
            );

            $stock->increment('quantity', $baseQty);

            // 🔥 FIX: ALWAYS STORE BASE UNIT
            StockMovement::create([
                'product_id' => $productId,
                'unit_id' => $baseUnitId, // ✅ FIXED
                'type' => 1,
                'quantity' => $baseQty, // base unit
                'reference_id' => $referenceId,
                'reference_type' => $referenceType,
                'note' => $note,
            ]);
        });
    }

    /**
     * Decrease stock (Sale)
     */
    public function decreaseStock(
        int $productId,
        float $quantity,
        int $unitId,
        ?int $referenceId = null,
        string $referenceType = 'Sale',
        ?string $note = null
    ): void {

        DB::transaction(function () use (
            $productId,
            $quantity,
            $unitId,
            $referenceId,
            $referenceType,
            $note
        ) {

            $product = Product::findOrFail($productId);

            $baseQty = $this->convertToBaseUnit($product, $quantity, $unitId);

            $baseUnitId = $product->base_unit_id ?? DEFAULT_UNIT;

            $stock = ProductStock::firstOrCreate(
                ['product_id' => $productId],
                [
                    'quantity' => 0,
                    'unit_id' => $baseUnitId
                ]
            );

            if ($stock->quantity < $baseQty) {
                throw new \Exception("Insufficient stock for product ID {$productId}");
            }

            $stock->decrement('quantity', $baseQty);

            // 🔥 FIXED
            StockMovement::create([
                'product_id' => $productId,
                'unit_id' => $baseUnitId,
                'type' => 2,
                'quantity' => $baseQty,
                'reference_id' => $referenceId,
                'reference_type' => $referenceType,
                'note' => $note,
            ]);
        });
    }

    /**
     * Adjust stock manually (BASE UNIT ONLY)
     */
    public function adjustStock(
        int $productId,
        float $quantity,
        int $unitId,
        ?int $referenceId = null,
        ?string $note = null
    ): void {

        DB::transaction(function () use (
            $productId,
            $quantity,
            $unitId,
            $referenceId,
            $note
        ) {

            $product = Product::findOrFail($productId);

            $baseQty = $this->convertToBaseUnit($product, $quantity, $unitId);

            $baseUnitId = $product->base_unit_id ?? DEFAULT_UNIT;

            $stock = ProductStock::firstOrCreate(
                ['product_id' => $productId],
                [
                    'quantity' => 0,
                    'unit_id' => $baseUnitId
                ]
            );

            $stock->update([
                'quantity' => $baseQty
            ]);

            // 🔥 FIXED
            StockMovement::create([
                'product_id' => $productId,
                'unit_id' => $baseUnitId,
                'type' => 4,
                'quantity' => $baseQty,
                'reference_id' => $referenceId,
                'reference_type' => 'Adjustment',
                'note' => $note,
            ]);
        });
    }

    /**
     * Convert any unit to BASE unit (Piece)
     */
    private function convertToBaseUnit(Product $product, float $qty, int $unitId): float
    {
        $baseUnit = $product->base_unit_id ?? DEFAULT_UNIT;

        if ($unitId == $baseUnit) {
            return $qty;
        }

        if ($unitId == $product->secondary_unit_id) {
            return $qty * $product->conversion_rate;
        }

        return $qty;
    }
}