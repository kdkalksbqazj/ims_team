<?php

namespace App\Modules\Organization;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $branches = Branch::all();
        return view('modules.organization.branches.index', compact('branches'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('modules.organization.branches.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            'location' => 'nullable',
        ]);

        Branch::create($validated);

        return redirect()->route('branches.index')->with('success', 'Branch created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Branch $branch)
    {
        return view('modules.organization.branches.show', compact('branch'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Branch $branch)
    {
        return view('modules.organization.branches.edit', compact('branch'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Branch $branch)
    {
        $validated = $request->validate([
            'name' => 'required',
            'location' => 'nullable',
        ]);

        $branch->update($validated);

        return redirect()->route('branches.index')->with('success', 'Branch updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Branch $branch)
    {
        $branch->delete();

        return redirect()->route('branches.index')->with('success', 'Branch deleted successfully.');
    }
}
