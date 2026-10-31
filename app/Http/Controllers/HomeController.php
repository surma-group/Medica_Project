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
    public function index(Request $request)
    {
        $brands = Brand::where('status', 1)->get();

        // ================= CATEGORIES =================
        $categories = Category::where('status', 1)
            ->whereHas('products', function ($q) {
                $q->where('status', 1);
            })
            ->withCount(['products' => function ($q) {
                $q->where('status', 1);
            }])
            ->get();

        // ================= PRODUCTS =================
        $products = Product::with([
            'category',
            'generic',
            'prices' => function ($q) {
                $q->where('user_type_id', USER_TYPE_CUSTOMER)
                    ->where('status', 1);
            }
        ])
            ->where('status', 1)

            ->when($request->category, function ($q) use ($request) {
                $q->where('category_id', $request->category);
            })

            ->latest()
            ->take(20)
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
        $search = $request->search;

        $products = Product::with([
            'category',
            'generic',
            'company',
            'prices' => function ($q) {
                $q->where('user_type_id', USER_TYPE_CUSTOMER)
                    ->where('status', 1);
            }
        ])
            ->where('status', 1)

            // 🔍 SEARCH FILTER (NEW)
            ->when($search, function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('name', 'like', "%{$search}%")
                        ->orWhere('strength', 'like', "%{$search}%")
                        ->orWhereHas('company', function ($c) use ($search) {
                            $c->where('company_name', 'like', "%{$search}%");
                        })
                        ->orWhereHas('generic', function ($g) use ($search) {
                            $g->where('name', 'like', "%{$search}%");
                        });
                });
            })

            // 🔥 Category filter
            ->when($request->category, function ($q) use ($request) {
                $q->where('category_id', $request->category);
            })

            // 🔥 Company filter
            ->when($request->company, function ($q) use ($request) {
                $q->where('company_id', $request->company);
            })

            ->latest()
            ->paginate(36)
            ->withQueryString();

        // ================= CATEGORIES =================
        $categories = Category::where('status', 1)
            ->whereHas('products', function ($q) {
                $q->where('status', 1);
            })
            ->withCount(['products' => function ($q) {
                $q->where('status', 1);
            }])
            ->get();

        // ================= COMPANIES =================
        $companies = ProductCompany::whereHas('products', function ($q) {
            $q->where('status', 1);
        })
            ->withCount(['products' => function ($q) {
                $q->where('status', 1);
            }])
            ->orderBy('products_count', 'desc')
            ->get();

        return view('frontend.shop', compact('products', 'categories', 'companies'));
    }

    public function orderRequest()
    {
        $units = Unit::where('status', 1)->get();

        return view('frontend.order-request', compact('units'));
    }
}
