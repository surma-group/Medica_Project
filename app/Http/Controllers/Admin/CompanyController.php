<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\District;
use App\Models\Currency;
use App\Models\TimeZone;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CompanyController extends Controller
{
    // List all companies with pagination
    public function index()
    {
        $companies = Company::with(['district', 'currency', 'timezone'])
            ->orderBy('id', 'desc')
            ->paginate(10); // ✅ better for performance

        return view('admin.companies.index', compact('companies'));
    }

    // Show form to create a new company
    public function create()
    {
        $districts = District::all();
        $currencies = Currency::all();
        $timezones = TimeZone::all();

        // Load defaults from config
        $defaultCurrencyId = config('defaults.currency_id');
        $defaultTimezoneId = config('defaults.timezone_id');
        $defaultStatus = config('defaults.status');

        return view('admin.companies.create', compact(
            'districts',
            'currencies',
            'timezones',
            'defaultCurrencyId',  // <- pass to view
            'defaultTimezoneId',
            'defaultStatus'
        ));
    }

    // Store new company

    public function store(Request $request)
    {
        $request->validate([
            'company_code' => 'required|unique:companies,company_code',
            'company_name' => 'required|string|max:255',
            'district_id' => 'nullable|exists:districts,id',
            'currency_id' => 'nullable|exists:currencies,id',
            'timezone_id' => 'nullable|exists:time_zones,id',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:50',
            'mobile' => 'nullable|string|max:50',
            'website' => 'nullable|string|max:255',
            'logo' => 'nullable|image|mimes:png,jpg,jpeg,svg|max:2048',
            'favicon' => 'nullable|image|mimes:png,jpg,jpeg,ico|max:1024',
        ]);

        $data = $request->all();

        $data['created_by'] = auth()->id();
        $data['status'] = $request->status ?? 1;

        /* ===== LOGO UPLOAD ===== */
        if ($request->hasFile('logo')) {
            Storage::disk('public')->makeDirectory('logos');

            $data['logo'] = $request->file('logo')
                ->store('logos', 'public');
        }

        /* ===== FAVICON UPLOAD ===== */
        if ($request->hasFile('favicon')) {
            Storage::disk('public')->makeDirectory('favicons');

            $data['favicon'] = $request->file('favicon')
                ->store('favicons', 'public');
        }

        Company::create($data);

        return redirect()
            ->route('company.index')
            ->with('success', 'Company created successfully.');
    }


    // Show form to edit company
    public function edit(Company $company)
    {
        $districts = District::all();
        $currencies = Currency::all();
        $timezones = TimeZone::all();

        // Load defaults from config in case some values are null
        $defaultCurrencyId = config('defaults.currency_id');
        $defaultTimezoneId = config('defaults.timezone_id');
        $defaultStatus = config('defaults.status');

        return view('admin.companies.create', compact(
            'company',
            'districts',
            'currencies',
            'timezones',
            'defaultCurrencyId',
            'defaultTimezoneId',
            'defaultStatus'
        ));
    }

    // Update company

    public function update(Request $request, Company $company)
    {
        $request->validate([
            'company_code' => 'required|unique:companies,company_code,' . $company->id,
            'company_name' => 'required|string|max:255',
            'district_id' => 'nullable|exists:districts,id',
            'currency_id' => 'nullable|exists:currencies,id',
            'timezone_id' => 'nullable|exists:time_zones,id',
            'financial_year_start' => 'nullable|date',
            'financial_year_end' => 'nullable|date|after_or_equal:financial_year_start',
            'logo' => 'nullable|image|mimes:png,jpg,jpeg,svg|max:2048',
            'favicon' => 'nullable|image|mimes:png,jpg,jpeg,ico|max:1024',
        ]);

        $data = $request->except(['logo', 'favicon']);

        /* ===== LOGO UPDATE ===== */
        if ($request->hasFile('logo')) {

            // ensure directory exists
            Storage::disk('public')->makeDirectory('logos');

            // delete old logo
            if ($company->logo && Storage::disk('public')->exists($company->logo)) {
                Storage::disk('public')->delete($company->logo);
            }

            $data['logo'] = $request->file('logo')
                ->store('logos', 'public');
        }

        /* ===== FAVICON UPDATE ===== */
        if ($request->hasFile('favicon')) {

            Storage::disk('public')->makeDirectory('favicons');

            // delete old favicon
            if ($company->favicon && Storage::disk('public')->exists($company->favicon)) {
                Storage::disk('public')->delete($company->favicon);
            }

            $data['favicon'] = $request->file('favicon')
                ->store('favicons', 'public');
        }

        $company->update($data);

        return redirect()
            ->route('company.index')
            ->with('success', 'Company updated successfully.');
    }

    // Delete company
    public function destroy(Company $company)
    {
        // Delete logo if exists
        if ($company->logo && Storage::disk('public')->exists($company->logo)) {
            Storage::disk('public')->delete($company->logo);
        }

        // Delete favicon if exists
        if ($company->favicon && Storage::disk('public')->exists($company->favicon)) {
            Storage::disk('public')->delete($company->favicon);
        }

        // Delete company record
        $company->delete();

        return redirect()
            ->route('company.index')
            ->with('success', 'Company deleted successfully.');
    }
}
