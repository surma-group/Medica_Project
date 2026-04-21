<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductCompany;
use App\Models\Unit;

class HomeController extends Controller
{
    public function index()
    {
        $brands = Brand::where('status', 1)->get();
        $categories = Category::where('status', 1)->get();

        // ✅ Add products for home page
        $products = Product::with(['category', 'generic'])
            ->where('status', 1)
            ->latest()
            ->take(20) // home page limited products
            ->get();

        return view('frontend.home', compact('brands', 'categories', 'products'));
    }

    public function contact()
    {
        return view('frontend.contact');
    }

    public function about()
    {
        return view('frontend.about');
    }

    public function vision()
    {
        return view('frontend.vision');
    }

    public function mission()
    {
        return view('frontend.mission');
    }

    public function coreValues()
    {
        return view('frontend.core-values');
    }

    public function career()
    {
        return view('frontend.career');
    }

    public function terms()
    {
        return view('frontend.terms');
    }

    public function shop(Request $request)
    {
        $products = Product::with(['category', 'generic'])
            ->where('status', 1)

            // 🔥 Category filter
            ->when($request->category, function ($q) use ($request) {
                $q->where('category_id', $request->category);
            })

            // 🔥 Company filter (IMPORTANT)
            ->when($request->company, function ($q) use ($request) {
                $q->where('company_id', $request->company);
            })

            ->latest()
            ->paginate(36) // 57 is too much for UX, better 12/24/36
            ->withQueryString();

        // ================= CATEGORIES =================
        $categories = Category::withCount(['products' => function ($q) {
            $q->where('status', 1);
        }])
            ->where('status', 1)
            ->having('products_count', '>', 0)
            ->get();

        // ================= COMPANIES =================
        $companies = ProductCompany::whereHas('products', function ($q) {
            $q->where('status', 1);
        })
            ->withCount(['products' => function ($q) {
                $q->where('status', 1);
            }])
            ->get();

        return view('frontend.shop', compact('products', 'categories', 'companies'));
    }

    public function orderRequest()
    {
        $units = Unit::where('status', 1)->get();

        return view('frontend.order-request', compact('units'));
    }
}
