<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Partner;
use App\Models\District;

class PartnerController extends Controller
{
    // ===================================
    // COMMON VALIDATION
    // ===================================
    private function validatePartner(Request $request)
    {
        return $request->validate([
            'name'        => 'required|string|max:255',
            'phone'       => 'required|string|max:20',
            'email'       => 'nullable|email|max:255',
            'address'     => 'nullable|string',
            'district_id' => 'nullable|integer|exists:districts,id',
            'postcode'    => 'nullable|string|max:20',
            'status'      => 'required|in:0,1',
        ]);
    }

    // ===================================
    // DEALER (type = USER_TYPE_DEALER)
    // ===================================

    public function dealerIndex(Request $request)
    {
        $partners = Partner::where('type', USER_TYPE_DEALER)
            ->when($request->filled('status'), function ($q) use ($request) {
                $q->where('status', (int) $request->status);
            })
            ->latest()
            ->paginate(10);

        return view('admin.partners.dealer.index', compact('partners'));
    }

    public function dealerEdit($id)
    {
        $partner = Partner::where('type', USER_TYPE_DEALER)
            ->findOrFail($id);

        $districts = District::all();

        return view('admin.partners.dealer.form', compact('partner', 'districts'));
    }

    public function dealerUpdate(Request $request, $id)
    {
        $partner = Partner::where('type', USER_TYPE_DEALER)
            ->findOrFail($id);

        $data = $this->validatePartner($request);

        $partner->update($data);

        return redirect()
            ->route('partners.dealer.index')
            ->with('success', 'Dealer updated successfully.');
    }

    public function dealerDestroy($id)
    {
        $partner = Partner::where('type', USER_TYPE_DEALER)
            ->findOrFail($id);

        $partner->delete();

        return redirect()
            ->route('partners.dealer.index')
            ->with('success', 'Dealer deleted successfully.');
    }

    // ===================================
    // AGENT (type = USER_TYPE_AGENT)
    // ===================================

    public function agentIndex(Request $request)
    {
        $partners = Partner::where('type', USER_TYPE_AGENT)
            ->when($request->filled('status'), function ($q) use ($request) {
                $q->where('status', (int) $request->status);
            })
            ->latest()
            ->paginate(10);

        return view('admin.partners.agent.index', compact('partners'));
    }

    public function agentEdit($id)
    {
        $partner = Partner::where('type', USER_TYPE_AGENT)
            ->findOrFail($id);

        $districts = District::all();

        return view('admin.partners.agent.form', compact('partner', 'districts'));
    }

    public function agentUpdate(Request $request, $id)
    {
        $partner = Partner::where('type', USER_TYPE_AGENT)
            ->findOrFail($id);

        $data = $this->validatePartner($request);

        $partner->update($data);

        return redirect()
            ->route('partners.agent.index')
            ->with('success', 'Agent updated successfully.');
    }

    public function agentDestroy($id)
    {
        $partner = Partner::where('type', USER_TYPE_AGENT)
            ->findOrFail($id);

        $partner->delete();

        return redirect()
            ->route('partners.agent.index')
            ->with('success', 'Agent deleted successfully.');
    }
}