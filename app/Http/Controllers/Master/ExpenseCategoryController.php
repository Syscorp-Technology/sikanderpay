<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;


class ExpenseCategoryController extends Controller
{
    public function index()
    {
        $categories = ExpenseCategory::get();
        return view('Master.expense-categories.index', compact('categories'));
    }
    public function create()
    {
        return view('Master.expense-categories.create');
    }
    public function store(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'expense_category_name' => ['required', 'min:2', 'max:50', Rule::unique('expense_categories')],
        ]);
        ExpenseCategory::create([
            'expense_category_name' => $request->expense_category_name,
            'status' => $request->status,
            'created_by' => auth()->user()->id,
        ]);
        return redirect(route('expense.category.index'))->with('success', 'Category Created sucessfully');
    }
    public function edit($id)
    {
        // dd($category);

        $category = ExpenseCategory::find($id);

        return view('Master.expense-categories.edit', compact('category'));
    }
    public function update(Request $request, string $id)
    {
        $request->validate([
            'expense_category_name' => ['required', 'min:2', 'max:50', Rule::unique('expense_categories')->ignore($id)],
        ]);
        ExpenseCategory::where('id', $request->id)->update([
            'expense_category_name' => $request->expense_category_name,
            'status' => $request->status,
            'updated_by' => auth()->user()->id,
        ]);
        return redirect(route('expense.category.index'))->with('success', 'Category Updated sucessfully');
    }
}
