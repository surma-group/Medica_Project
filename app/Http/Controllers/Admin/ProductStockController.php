<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ProductStock;
use App\Models\StockMovement;
use App\Models\Unit;
use App\Models\PurchaseItem;

class ProductStockController extends Controller
{

    public function index(Request $request)
    {
        // ==========================
        // STOCK QUERY
        // ==========================
        $query = ProductStock::with([
            'product.baseUnit',
            'product.secondaryUnit',
            'product.company'
        ]);

        // ==========================
        // PRODUCT NAME SEARCH
        // ==========================
        if ($request->filled('product_name')) {

            $query->whereHas('product', function ($q) use ($request) {

                $q->where('name', 'like', '%' . $request->product_name . '%');
            });
        }

        // ==========================
        // STOCK STATUS FILTER
        // ==========================
        if ($request->filled('stock_status')) {

            if ($request->stock_status === 'in_stock') {

                $query->where('quantity', '>', 0);
            } elseif ($request->stock_status === 'out_of_stock') {

                $query->where('quantity', '<=', 0);
            }
        }

        // ==========================
        // STOCK LIST
        // ==========================
        $stocks = $query
            ->orderBy('product_id')
            ->paginate(20)
            ->withQueryString();

        // ==========================
        // COST PRICE MAP
        // ==========================
        $costMap = \App\Models\PurchaseItem::select(
            'product_id',
            'price'
        )
            ->orderByDesc('id')
            ->get()
            ->unique('product_id')
            ->pluck('price', 'product_id');

        // ==========================
        // TRANSFORM STOCK DATA
        // ==========================
        $stocks->getCollection()->transform(function ($stock) use ($costMap) {

            $product = $stock->product;

            // ==========================
            // COST PRICE
            // ==========================
            $costPrice = $costMap[$stock->product_id] ?? 0;

            $stock->cost_price = $costPrice;

            // ==========================
            // UNIT INFO
            // ==========================
            $baseUnitModel = $product?->baseUnit
                ?? \App\Models\Unit::find(DEFAULT_UNIT);

            $secondaryUnitModel = $product?->secondaryUnit;

            $baseUnitName = $baseUnitModel?->title ?? 'Unit';
            $secondaryUnitName = $secondaryUnitModel?->title ?? null;

            $ratio = $product?->conversion_rate ?? 0;

            // ==========================
            // STOCK FORMAT
            // ==========================
            $qty = $stock->quantity;

            $baseQty = $qty;
            $secondaryQty = null;

            if ($ratio > 0 && $secondaryUnitName) {

                $secondaryQty = intdiv($qty, $ratio);
                $baseQty = $qty % $ratio;
            }

            $parts = [];

            if ($secondaryQty > 0 && $secondaryUnitName) {
                $parts[] = $secondaryQty . ' ' . $secondaryUnitName;
            }

            if ($baseQty > 0 && $baseUnitName) {
                $parts[] = $baseQty . ' ' . $baseUnitName;
            }

            $stock->formatted_stock = [
                'text' => count($parts)
                    ? implode(' + ', $parts)
                    : '0 ' . $baseUnitName
            ];

            // ==========================
            // UNIT COST
            // ==========================
            $unitCosts = [];

            $unitCosts[$baseUnitName] = $costPrice;

            if ($ratio > 0 && $secondaryUnitName) {

                $unitCosts[$secondaryUnitName] =
                    $costPrice * $ratio;
            }

            $stock->setAttribute(
                'unit_costs',
                $unitCosts
            );

            return $stock;
        });

        return view(
            'admin.product_stock.index',
            compact('stocks')
        );
    }

    public function history($id)
    {
        $logs = StockMovement::with([
            'product.baseUnit',
            'product.secondaryUnit',
            'unit'
        ])
            ->where('product_id', $id)
            ->latest()
            ->get()
            ->map(function ($log) {

                $product = $log->product;

                $baseQty = $log->quantity;

                // 🔥 SAFE DEFAULT UNIT HANDLING
                $baseUnit = $product?->baseUnit
                    ?? Unit::find(DEFAULT_UNIT);

                $secondaryUnit = $product?->secondaryUnit;

                $baseUnitName = $baseUnit?->title;
                $secondaryUnitName = $secondaryUnit?->title;

                $ratio = $product?->conversion_rate ?? 0;

                $mainQty = $baseQty;
                $secondaryQty = null;

                if ($ratio > 0 && $secondaryUnitName) {

                    $secondaryQty = intdiv($baseQty, $ratio);
                    $mainQty = $baseQty % $ratio;
                }

                $parts = [];

                if ($secondaryQty !== null && $secondaryQty > 0 && $secondaryUnitName) {
                    $parts[] = $secondaryQty . ' ' . $secondaryUnitName;
                }

                if ($mainQty > 0 && $baseUnitName) {
                    $parts[] = $mainQty . ' ' . $baseUnitName;
                }

                $log->formatted_quantity = count($parts)
                    ? implode(' + ', $parts)
                    : ($baseUnitName ? '0 ' . $baseUnitName : '0');

                return $log;
            });

        return response()->json($logs);
    }
}
