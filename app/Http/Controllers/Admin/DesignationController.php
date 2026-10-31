<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Designation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DesignationController extends Controller
{
    /**
     * Display all designations.
     */
    public function index()
    {
        $designations = Designation::with('parent')
            ->latest()
            ->paginate(10);

        return view('admin.designations.index', compact('designations'));
    }


    /**
     * Show create form.
     */
    public function create()
    {
        $designations = Designation::where('status', 1)
            ->orderBy('name')
            ->get();

        return view('admin.designations.form', compact('designations'));
    }


    /**
     * Store new designation.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:designations,code',
            'description' => 'nullable|string',
            'status' => 'required|in:0,1',
            'parent_id' => 'nullable|exists:designations,id',
        ]);

        // Create designation
        Designation::create([
            'name' => $validated['name'],
            'code' => $validated['code'],
            'description' => $validated['description'] ?? null,
            'status' => $validated['status'],
            'parent_id' => $validated['parent_id'] ?? null,
            'created_by' => Auth::id(),
        ]);

        return redirect()
            ->route('designation.index')
            ->with('success', 'Designation created successfully.');
    }


    /**
     * Show edit form.
     */
    public function edit(Designation $designation)
    {

        $designations = Designation::where('status', 1)
            ->where('id', '!=', $designation->id)
            ->orderBy('name')
            ->get();

        return view(
            'admin.designations.form',
            compact('designation', 'designations')
        );
    }


    /**
     * Update designation.
     */
    public function update(Request $request, Designation $designation)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',

            'code' => 'required|string|max:255|unique:designations,code,' . $designation->id,

            'description' => 'nullable|string',

            'status' => 'required|in:0,1',

            'parent_id' => [
                'nullable',
                'exists:designations,id',
                function ($attribute, $value, $fail) use ($designation) {

                    // নিজের parent হতে পারবে না
                    if ($value == $designation->id) {
                        $fail('A designation cannot be its own parent.');
                    }

                    if ($value && $this->isChildOf($value, $designation->id)) {
                        $fail('You cannot select one of the child designations as parent.');
                    }
                },
            ],
        ]);

        $designation->update([
            'name' => $validated['name'],
            'code' => $validated['code'],
            'description' => $validated['description'] ?? null,
            'status' => $validated['status'],
            'parent_id' => $validated['parent_id'] ?? null,
        ]);

        return redirect()
            ->route('designation.index')
            ->with('success', 'Designation updated successfully.');
    }


    public function destroy(Designation $designation)
    {

        if ($designation->children()->exists()) {
            return redirect()
                ->route('designation.index')
                ->with('error', 'This designation cannot be deleted because it has child designations.');
        }

        $designation->delete();

        return redirect()
            ->route('designation.index')
            ->with('success', 'Designation deleted successfully.');
    }



    private function isChildOf($possibleChildId, $parentId)
    {
        $current = Designation::find($possibleChildId);

        while ($current && $current->parent_id) {

            if ($current->parent_id == $parentId) {
                return true;
            }

            $current = Designation::find($current->parent_id);
        }

        return false;
    }
}
