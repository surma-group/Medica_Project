<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;

class HomeController extends Controller
{
    public function index()
    {
        $brands = Brand::where('status', 1)->get();
        $categories = Category::where('status', 1)->get();

        return view('frontend.home', compact('brands', 'categories'));
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
}
