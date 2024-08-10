<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;


class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::get();
        return view('Master.categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('Master.categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'category_name' => ['required', 'min:2', 'max:50', Rule::unique('categories')],
        ]);
        Category::create([
            'category_name' => $request->category_name,
            'status' => $request->status,
            'created_by' => auth()->user()->id,
        ]);
        return redirect(route('category.index'))->with('success', 'Category Created sucessfully');
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

        $category = Category::find($id);

        return view('Master.categories.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'category_name' => ['required', 'min:2', 'max:50', Rule::unique('categories')->ignore($id)],
        ]);
        Category::where('id', $request->id)->update([
            'category_name' => $request->category_name,
            'status' => $request->status,
            'updated_by' => auth()->user()->id,
        ]);
        return redirect(route('category.index'))->with('success', 'Category Updated sucessfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
