<?php

namespace App\Http\Controllers;

use App\Models\GatewayCategory;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class GatewayCategoryController extends Controller
{

    public function index()
    {
        $gatewayCategories = GatewayCategory::get();
        return view('gateway-categories.index', compact('gatewayCategories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('gateway-categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'gateway_name' => ['required', 'min:2', 'max:50', Rule::unique('gateway_categories')],
        ]);
        GatewayCategory::create([
            'gateway_name' => $request->gateway_name,
            'status' => $request->status,
            'created_by' => auth()->user()->id,
        ]);
        return redirect(route('gateway-category.index'))->with('success', 'Gateway Created sucessfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        // dd($category);

        $gatewayCategories = GatewayCategory::find($id);

        return view('gateway-categories.edit', compact('gatewayCategories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'gateway_name' => ['required', 'min:2', 'max:50', Rule::unique('gateway_categories')->ignore($id)],
        ]);
        GatewayCategory::where('id', $request->id)->update([
            'gateway_name' => $request->gateway_name,
            'status' => $request->status,
            'updated_by' => auth()->user()->id,
        ]);
        return redirect(route('gateway-category.index'))->with('success', 'Gateway Updated sucessfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
