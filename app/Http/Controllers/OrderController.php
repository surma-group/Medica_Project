<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Customer;
use App\Models\OrderRequest;
use App\Models\OrderRequestItem;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:general,prescription',
            'customer' => 'required|json',
            'cart' => 'nullable|json',
            'prescription' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        DB::beginTransaction();

        try {

            /* ================= DECODE JSON ================= */
            $customerData = json_decode($request->customer, true);
            $cart = $request->cart ? json_decode($request->cart, true) : [];

            /* ================= BASIC VALIDATION ================= */
            if (empty($customerData['name']) || empty($customerData['phone'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Name and phone required'
                ]);
            }

            /* ================= CUSTOMER ================= */
            $customer = Customer::firstOrCreate(
                ['phone' => $customerData['phone']],
                [
                    'name' => $customerData['name'],
                    'email' => $customerData['email'] ?? null,
                    'address' => $customerData['address'] ?? null,
                    'status' => 0,
                ]
            );

            /* ================= ORDER ================= */
            $order = OrderRequest::create([
                'customer_id' => $customer->id,
                'order_number' => 'ORD-' . strtoupper(uniqid()),
                'type' => $request->type === 'general' ? 1 : 2,
                'prescription_description' => $request->description ?? null,
                'status' => 0,
            ]);

            /* ================= PRESCRIPTION FILE ================= */
            if ($request->hasFile('prescription')) {
                $filePath = $request->file('prescription')->store('prescriptions', 'public');

                $order->update([
                    'prescription_file' => $filePath
                ]);
            }

            /* ================= ORDER ITEMS ================= */
            if ($request->type === 'general') {

                if (empty($cart)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Cart is empty'
                    ]);
                }

                foreach ($cart as $item) {

                    OrderRequestItem::create([
                        'order_request_id' => $order->id,
                        'product_name' => $item['name'],
                        'strength' => $item['strength'] ?? null,
                        'company' => $item['company'] ?? null, // ✅ NEW FIELD
                        'unit_id' => $item['unit'],
                        'quantity' => $item['qty'],
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order request submitted successfully',
                'order_id' => $order->id
            ]);

        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage() // optional (dev mode)
            ]);
        }
    }
}