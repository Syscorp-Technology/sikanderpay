<?php

namespace App\Http\Controllers;

use App\Exports\ExpenseExport;
use App\Jobs\OurBankDetailBalanceJob;
use App\Models\Category;
use App\Models\ExpenseCategory;
use App\Models\IncomeAndExpense;
use App\Models\OurBankDetail;
use App\Models\PaymentMode;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;
use Maatwebsite\Excel\Facades\Excel;



class ExpenseController extends Controller
{
    public function index()
    {
        $expense = IncomeAndExpense::where('type', 'expense')->where('status', 0)->with('ourBankDetail', 'paymentMode', 'expenseCategory')->get();
        $ourbank = OurBankDetail::where('type','Bank')->where('status', 1)->get();
        // dd($expense);
        return view('expense.index', compact('expense', 'ourbank'));
    }
    public function create()
    {
        $categories = ExpenseCategory::where('status', 1)->get();
        $paymentModes = PaymentMode::where('status', 1)->get();
        $ourbank = OurBankDetail::where('type','Bank')->where('status', 1)->get();
        return view('expense.create', compact('categories', 'paymentModes', 'ourbank'));
    }
    public function store(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'title' => 'required|max:255',
            'date' => 'required',
            'expense_category_id' => 'required',
            'amount' => 'required|integer',
            'payment_mode_id' => 'required',
            // 'ref_no' => 'nullable',
            // 'our_bank_detail_id' => 'required',
            'note' => 'required',
        ], [
            'amount.integer' => 'Amount field must be an Number'
        ]);

        // dd('hello');
        IncomeAndExpense::create([
            'title' => $request->title,
            'date' => $request->date,
            'expense_category_id' => $request->expense_category_id,
            'amount' => $request->amount,
            // 'ref_no' => $request->ref_no,
            // 'our_bank_detail_id' => $request->our_bank_detail_id,
            'note' => $request->note,
            'payment_mode_id' => $request->payment_mode_id,
            'type' => 'expense',
            'created_by' => auth()->user()->id,
        ]);
        return redirect(route('expense.index'))->with('success', 'expense has created successfully');
    }
    public function edit(Request $request, $id)
    {
        $categories = ExpenseCategory::where('status', 1)->get();
        $paymentModes = PaymentMode::where('status', 1)->get();
        $ourbank = OurBankDetail::where('type','Bank')->where('status', 1)->get();
        $expense = IncomeAndExpense::find($id);
        return view('expense.edit', compact('categories', 'paymentModes', 'ourbank', 'expense'));
    }
    public function update(Request $request, $id)
    {
        // dd($request->all());

        $request->validate([
            'title' => ['required', 'max:255'],
            'date' => ['required'],
            'expense_category_id' => ['required'],
            'attachment' => ['nullable', 'mimes:png,jpg,jpeg'],
            'amount' => ['required', 'integer'],
            'payment_mode_id' => ['required'],
            'ref_no' => ['required', Rule::unique('income_and_expenses')->ignore($id), 'regex:/^[a-zA-Z0-9]+$/'],
            'our_bank_detail_id' => ['required'],
            'note' => ['nullable'],
        ], [
            'ref_no.integer' => 'The Ref No field must be an Number.',
            'amount.integer' => 'The Amount field must be an Number.',
            'ref_no.regex' => 'The Ref No field must contain only letters and numbers.',
        ]);

        $income = IncomeAndExpense::find($id);
        $oldAmount = $income->amount;
        $oldImage = $income->attachment;
        $oldBankAccount = $income->our_bank_detail_id;
        $path = '';
        if ($request->attachment) {
            if ($income->attachment) {
                Storage::disk('public')->delete($income->attachment);
            }
            $attachment = $request->file('attachment');
            $ext = $attachment->extension();
            $contents = file_get_contents($attachment);
            $fileName = Str::random(20);
            $path = "attachments/$fileName.$ext";
            Storage::disk('public')->put($path, $contents);
        }
        $income->update([
            'title' => $request->title,
            'date' => $request->date,
            'expense_category_id' => $request->expense_category_id,
            'amount' => $request->amount,
            'ref_no' => $request->ref_no,
            'attachment' => $path ? $path : $oldImage,
            'our_bank_detail_id' => $request->our_bank_detail_id,
            'note' => $request->note,
            'payment_mode_id' => $request->payment_mode_id,
            'type' => 'expense',
            'updated_by' => auth()->user()->id,
        ]);

        if ($income->banker_status == 'Verified' && $income->financier_status == 'Verified' && $income->operation_head_status == 'Verified' && $income->superviser_status == 'Verified') {
            if ($income->our_bank_detail_id != $oldBankAccount) {

                try {
                    $newAmount = '';

                    if ($oldAmount > $income->amount) {
                        $newAmount = $oldAmount - $income->amount;
                        //    $operation='Both';
                    } elseif ($oldAmount < $income->amount) {
                        $newAmount = $income->amount - $oldAmount;
                        //    $operation='Increase';
                    } elseif ($oldAmount == $income->amount) {
                        $newAmount = $income->amount;
                    }
                    $jobRequest = [
                        'req_amount' => $newAmount,
                        'type' => 'Expense Edit',
                        'new_amount' => $income->amount,
                        'operation' => "Bank Changed",
                        'old_amount' => $oldAmount,
                        'old_bank' => $oldBankAccount,
                        'new_bank' => $income->our_bank_detail_id,
                        // 'our_bank_detail_id'=>$income->our_bank_detail_id,
                        'created_by' => auth()->user()->id,
                    ];
                    OurBankDetailBalanceJob::dispatch($jobRequest);

                    // return response()->json(['message' => 'Income request queued successfully']);
                } catch (\Exception $e) {
                    return response()->json(['error' => 'Failed to queue Expense request: ' . $e->getMessage()], 500);
                }
            } elseif ($income->our_bank_detail_id == $oldBankAccount) {

                try {
                    $newAmount = '';
                    $operation = '';

                    if ($oldAmount > $income->amount) {
                        $newAmount = $oldAmount - $income->amount;
                        $operation = 'Increase';
                    } elseif ($oldAmount < $income->amount) {
                        $newAmount = $income->amount - $oldAmount;
                        $operation = 'Decrease';
                    }

                    $jobRequest = [
                        'req_amount' => $newAmount,
                        'type' => 'Expense Edit',
                        'operation' => $operation,
                        'our_bank_detail_id' => $income->our_bank_detail_id,
                        'created_by' => auth()->user()->id,
                    ];
                    OurBankDetailBalanceJob::dispatch($jobRequest);

                    // return response()->json(['message' => 'Income request queued successfully']);
                } catch (\Exception $e) {
                    return response()->json(['error' => 'Failed to queue income request: ' . $e->getMessage()], 500);
                }
            }
        }

        return redirect(route('dashboard'))->with('success', 'Income Updated Successfully');
    }
    public function expenseStatus(Request $request)
    {
        // dd($request->all());
        // $id = $request->expenseId;
        $AuthUserId = auth()->user()->id;
        $expense = IncomeAndExpense::where('id', $request->expenseId)
            ->lockForUpdate()
            ->first();

        if ($expense && $request->type === "financier_status" && $expense->financier_status == 'Pending') {
            if ($request->status == 'Verified') {
                // IncomeAndExpense::where('id', $id)
                // dd($expense);
                $expense->update([
                    'financier_status' => $request->status,
                    'updated_by' => $AuthUserId,
                ]);
                // dd($request->all(),$AuthUserId, $expense);
                // dd();
            } elseif ($request->status == 'Rejected') {
                // IncomeAndExpense::where('id', $id)
                $request->validate([
                    'note' => 'required'
                ]);
                $expense->update([
                    'financier_status' => $request->status,
                    'note' => $request->note,
                    'status' => 1,
                    'updated_by' => $AuthUserId,
                ]);
            }
        }

        if ($expense && $request->type === "operation_head_status" && $expense->operation_head_status == 'Pending') {
            // dd($request->all(), 'operation_head_status');
            if ($request->status == 'Verified') {
                // IncomeAndExpense::where('id', $id)
                $expense->update([
                    'operation_head_status' => $request->status,
                    'updated_by' => $AuthUserId,
                ]);
            } elseif ($request->status == 'Rejected') {
                // IncomeAndExpense::where('id', $id)
                $request->validate([
                    'note' => 'required'
                ]);
                $expense->update([
                    'operation_head_status' => $request->status,
                    'note' => $request->note,
                    'status' => 1,
                    'updated_by' => $AuthUserId,
                ]);
            }
        }


        if ( $expense && $request->type === "superviser_status" && $expense->superviser_status == 'Pending') {
            // dd($request->all(), 'superviser_status');

            if ($request->status == 'Verified') {
                // IncomeAndExpense::where('id', $id)
                $expense->update([
                    'superviser_status' => $request->status,
                    'updated_by' => $AuthUserId,
                ]);
            } elseif ($request->status == 'Rejected') {
                // IncomeAndExpense::where('id', $id)
                $request->validate([
                    'note' => 'required'
                ]);
                $expense->update([
                    'superviser_status' => $request->status,
                    'note' => $request->note,
                    'status' => 1,
                    'updated_by' => $AuthUserId,
                ]);
            }
        }

        if ($expense && $request->type === "banker_status" && $expense->banker_status == 'Pending') {
            // dd($request->all());

            if ($request->status == 'Verified') {

                $validator = Validator::make($request->all(), [
                    'our_bank_detail_id' => 'required',
                    'attachment' => 'required',
                    'ref_no' => 'required|unique:income_and_expenses|regex:/^[a-zA-Z0-9]+$/',
                ], [
                    'our_bank_detail_id.required' => 'The Bank field is required.',
                    'attachment.required' => 'The Attachment field is required.',
                    'ref_no.required' => 'The Ref No field is required.',
                    'ref_no.regex' => 'The Ref Number field must contain only letters and numbers.',

                ]);

                if ($validator->fails()) {
                    return response()->json(['errors' => $validator->errors()], 422);
                }
                // $expense = IncomeAndExpense::find($id);
                $ourBankDetail = OurBankDetail::find($request->our_bank_detail_id);
                if (($ourBankDetail->amount) >= ($expense->amount)) {
                    $attachment = $request->file('attachment');
                    $ext = $attachment->extension();
                    $contents = file_get_contents($attachment);
                    $fileName = Str::random(20);
                    $path = "attachments/$fileName.$ext";
                    Storage::disk('public')->put($path, $contents);
                    // $expense = IncomeAndExpense::find($id);
                    $expense->update([
                        'banker_status' => $request->status,
                        'our_bank_detail_id' => $request->our_bank_detail_id,
                        'attachment' => $path,
                        'ref_no' => $request->ref_no,
                        'status' => 1,
                        'updated_by' => $AuthUserId,
                    ]);


                    try {
                        $jobRequest = [
                            'req_amount' => $expense->amount,
                            'type' => 'Expense',
                            'operation' => 'Decrease',
                            'our_bank_detail_id' => $expense->our_bank_detail_id,
                            'created_by' => auth()->user()->id,
                        ];
                        OurBankDetailBalanceJob::dispatch($jobRequest);

                        // return response()->json(['message' => 'Income request queued successfully']);
                    } catch (\Exception $e) {
                        return response()->json(['error' => 'Failed to queue Income request: ' . $e->getMessage()], 500);
                    }


                    // $currentDate = now();
                    // $lastIncomeAndExpense = IncomeAndExpense::latest()->first();
                    // $lastDate = $lastIncomeAndExpense->created_at;
                    // if ($currentDate->lessThanOrEqualTo($lastDate)) {
                    //     OurBankDetail::query()->update(['count' => 0]);
                    // }
                    // // $ourBankDetail = OurBankDetail::find($expense->our_bank_detail_id);
                    // $totalAmount = ($ourBankDetail->amount) - ($expense->amount);
                    // $ourBankDetail->update([
                    //     'amount' => $totalAmount,
                    //     'count'=>$ourBankDetail->count+1,
                    // ]);
                } else {
                    return response()->json(['errors' => ['no_balance' => "Insufficient Balance in this account"]], 422);
                }
            } elseif ($request->status == 'Rejected') {
                $request->validate([
                    'note' => 'required'
                ]);
                // IncomeAndExpense::where('id', $id)
                $expense->update([
                    'banker_status' => $request->status,
                    'note' => $request->note,
                    'status' => 1,
                    'updated_by' => $AuthUserId,
                ]);
            }
        }

        return response()->json(['status_code' => 200, 'success' => 'status updated']);
    }
    public function expenseReport()
    {
        $user = auth()->user();
        $deposit = IncomeAndExpense::get();
        $created_by = User::all();
        return view('expense.reports', compact('user', 'created_by', 'deposit'));
    }

    public function allExpenseReports(Request $request)
    {
        // dd($request->all());
        $filters = $request->all();
        $expenseQuery = IncomeAndExpense::query()
            ->with(['expenseCategory', 'paymentMode', 'createdBy', 'ourBankDetail'])
            ->where('type', 'expense')
            ->where('status', 1);
        // ->where('financier_status','Verified')
        // ->where('operation_head_status','Verified')
        // ->where('superviser_status','Verified')
        // ->where('banker_status', 'Verified');

        if (isset($filters['start_date'])) {
            $expenseQuery->whereDate('date', '>=', $filters['start_date']);
        }

        if (isset($filters['end_date'])) {
            $expenseQuery->whereDate('date', '<=', $filters['end_date']);
        }

        if (isset($filters['created_by'])) {
            $expenseQuery->where('created_by', $filters['created_by']);
        }

        $expense = $expenseQuery->orderBy('created_at', 'desc')->get();
        $TotalExpenseRecords = $expense->count();
        $totalExpenseAmount = $expense->sum('amount');

        // \Log::info($income);
        // dd($income);our_bank_detail_id
        return DataTables::of($expense)
            ->addColumn('expense_category_name', function ($expense) {
                return $expense->ExpenseCategory->expense_category_name ?? '-';
            })
            ->addColumn('date', function ($income) {
                $carbonDate = Carbon::parse($income->date);
                return $carbonDate->format('Y F j') ?? '-';
            })
            ->addColumn('paymentMode', function ($expense) {
                return $expense->paymentMode->payment_mode_name ?? '-';
            })
            ->addColumn('ourBankDetail', function ($expense) {
                return $expense->ourBankDetail->bank_name ?? '-';
            })
            ->addColumn('created_by_name', function ($expense) {
                return $expense->createdBy->name ?? '-';
            })
            ->addColumn('image', function ($expense) {
                $imagePath = 'storage/' . $expense->attachment;
                $imageUrl = asset($imagePath);

                if (file_exists(public_path($imagePath))) {
                    return  $imageUrl;
                } else {
                    return 'No Image';
                }
            })
            ->addColumn('actions', function ($expense) {
                // Check if the user can delete the deposit
                if (Auth::user()->can('Expense Edit', $expense)) {
                    // Return the delete button HTML if the user has permission
                    return '<a href="' . route("expense.edit", $expense->id) . '"><i class="fa-solid fa-edit"></i></a>';
                } else {
                    // Optionally, return an empty string or a disabled button
                    return '';
                }
            })
            ->addColumn('createdBy', function ($expense) {
                return $expense->createdBy->name ?? '-';
            })
            ->with([
                'TotalExpenseRecords' => $TotalExpenseRecords,
                'totalExpenseAmount' => $totalExpenseAmount,
            ])
            ->rawColumns(['image', 'actions'])
            ->make(true);
    }
    public function exportAllExpenseResults(Request $request)
    {
        try {
            $filters = $request->all();
            $expenseQuery = IncomeAndExpense::query()
                ->select(
                    'id',
                    'title',
                    'note',
                    'expense_category_id',
                    'date',
                    'amount',
                    'payment_mode_id',
                    'ref_no',
                    'our_bank_detail_id',
                    'banker_status',
                    'status',
                    'created_by'
                )
                ->with(['expenseCategory', 'paymentMode', 'ourBankDetail', 'createdBy'])
                ->where('type', 'expense')
                // ->where('banker_status', 'Verified')
                ->where('status', 1);

            if (isset($filters['start_date'])) {
                $expenseQuery->whereDate('date', '>=', $filters['start_date']);
            }

            if (isset($filters['end_date'])) {
                $expenseQuery->whereDate('date', '<=', $filters['end_date']);
            }

            if (isset($filters['created_by'])) {
                $expenseQuery->where('created_by', $filters['created_by']);
            }

            $expense = $expenseQuery->orderBy('created_at', 'desc')->get();

            return Excel::download(new ExpenseExport($expense), 'expenseReport.xlsx');
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
