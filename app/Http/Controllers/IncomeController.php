<?php

namespace App\Http\Controllers;

use App\Exports\IncomeExport;
use App\Jobs\OurBankDetailBalanceJob;
use App\Models\Category;
use App\Models\IncomeAndExpense;
use App\Models\OurBankDetail;
use App\Models\PaymentMode;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Yajra\DataTables\DataTables;
use Maatwebsite\Excel\Facades\Excel;


class IncomeController extends Controller
{

    public function index()
    {

        // $all = IncomeAndExpense::with(['category', 'paymentMode', 'createdBy'])->where('type', 'income')->get();
        // dd($all);
        $income = IncomeAndExpense::where('type', 'income')->where('status', 0)->with('ourBankDetail', 'paymentMode', 'category')->get();
        // dd($income);
        return view('income.index', compact('income'));
    }
    public function create()
    {
        $categories = Category::where('status', 1)->get();
        $paymentModes = PaymentMode::where('status', 1)->get();
        $ourbank = OurBankDetail::where('type','Bank')->where('status', 1)->get();
        return view('income.create', compact('categories', 'paymentModes', 'ourbank'));
    }
    public function store(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'title' => 'required|max:255',
            'date' => 'required',
            'category_id' => 'required',
            'attachment' => 'required|mimes:png,jpg,jpeg',
            'amount' => 'required|integer',
            'payment_mode_id' => 'required',
            'ref_no' => 'required|unique:income_and_expenses|regex:/^[a-zA-Z0-9]+$/',
            'our_bank_detail_id' => 'required',
            'note' => 'nullable',
        ], [
            // 'ref_no.integer' => 'The Ref No field must be an Number.',
            'amount.integer' => 'The Amount field must be an Number.',
            'ref_no.regex' => 'The Ref No field must contain only letters and numbers.',
        ]);
        $path = '';
        if ($request->attachment) {
            $attachment = $request->file('attachment');
            $ext = $attachment->extension();
            $contents = file_get_contents($attachment);
            $fileName = Str::random(20);
            $path = "attachments/$fileName.$ext";
            Storage::disk('public')->put($path, $contents);
        }

        IncomeAndExpense::create([
            'title' => $request->title,
            'date' => $request->date,
            'category_id' => $request->category_id,
            'amount' => $request->amount,
            'ref_no' => $request->ref_no,
            'attachment' => $path ? $path : '',
            'our_bank_detail_id' => $request->our_bank_detail_id,
            'note' => $request->note,
            'payment_mode_id' => $request->payment_mode_id,
            'type' => 'income',
            'created_by' => auth()->user()->id,
        ]);
        return redirect(route('income.index'))->with('success', 'Income has created successfully');
    }
    public function edit($id)
    {
        // dd($id);
        $income = IncomeAndExpense::find($id);
        $categories = Category::where('status', 1)->get();
        $paymentModes = PaymentMode::where('status', 1)->get();
        $ourbank = OurBankDetail::where('type','Bank')->where('status', 1)->get();
        return view('income.edit', compact('income', 'categories', 'paymentModes', 'ourbank'));
    }
    public function update(Request $request, $id)
    {
        // dd($id);
        $request->validate([
            'title' => ['required', 'max:255'],
            'date' => ['required'],
            'category_id' => ['required'],
            'attachment' => ['nullable', 'mimes:png,jpg,jpeg'],
            'amount' => ['required', 'integer'],
            'payment_mode_id' => ['required'],
            'ref_no' => ['required', Rule::unique('income_and_expenses')->ignore($id), 'regex:/^[a-zA-Z0-9]+$/'],
            'our_bank_detail_id' => ['required'],
            'note' => ['nullable'],
        ], [
            // 'ref_no.integer' => 'The Ref No field must be an Number.',
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
            'category_id' => $request->category_id,
            'amount' => $request->amount,
            'ref_no' => $request->ref_no,
            'attachment' => $path ? $path : $oldImage,
            'our_bank_detail_id' => $request->our_bank_detail_id,
            'note' => $request->note,
            'payment_mode_id' => $request->payment_mode_id,
            'type' => 'income',
            'updated_by' => auth()->user()->id,
        ]);

        if ($income->banker_status == 'Verified') {
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
                        'new_amount'=>$income->amount,
                        'type' => 'income Edit',
                        'operation' => "Bank Changed",
                        'old_amount' => $oldAmount,
                        'new_bank' => $income->our_bank_detail_id,
                        'old_bank' => $oldBankAccount,
                        // 'our_bank_detail_id'=>$income->our_bank_detail_id,
                        'created_by' => auth()->user()->id,
                    ];
                    OurBankDetailBalanceJob::dispatch($jobRequest);

                    // return response()->json(['message' => 'Income request queued successfully']);
                } catch (\Exception $e) {
                    return response()->json(['error' => 'Failed to queue income request: ' . $e->getMessage()], 500);
                }
            } elseif ($income->our_bank_detail_id == $oldBankAccount) {

                try {
                    $newAmount = '';
                    $operation = '';

                    if ($oldAmount > $income->amount) {
                        $newAmount = $oldAmount - $income->amount;
                        $operation = 'Decrease';
                    } elseif ($oldAmount < $income->amount) {
                        $newAmount = $income->amount - $oldAmount;
                        $operation = 'Increase';
                    }

                    $jobRequest = [
                        'req_amount' => $newAmount,
                        'type' => 'income Edit',
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
    public function bankerStatus(Request $request)
    {
        // dd($request->all());
        // $id = $request->incomeId;
        $income = IncomeAndExpense::where('id', $request->incomeId)
        ->lockForUpdate()
        ->first();

        if ($income && $income->banker_status=="Pending" && $request->status == 'Verified' ) {

            $income->update([
                'banker_status' => $request->status,
                'financier_status' => $request->status,
                'operation_head_status' => $request->status,
                'superviser_status' => $request->status,
                'status' => 1,
            ]);

            try {
                $jobRequest = [
                    'req_amount' => $income->amount,
                    'type' => 'income',
                    'operation' => 'Increase',
                    'our_bank_detail_id' => $income->our_bank_detail_id,
                    'created_by' => auth()->user()->id,
                ];
                OurBankDetailBalanceJob::dispatch($jobRequest);

                // return response()->json(['message' => 'Income request queued successfully']);
            } catch (\Exception $e) {
                return response()->json(['error' => 'Failed to queue Expense request: ' . $e->getMessage()], 500);
            }

            // $income = IncomeAndExpense::find($id);
            // $ourBankDetail = OurBankDetail::find($income->our_bank_detail_id);
            // $totalAmount = ($ourBankDetail->amount) + ($income->amount);
            // $ourBankDetail->update([
            //     'amount' => $totalAmount,
            // ]);
        } elseif ($income && $income->banker_status=="Pending" && $request->status == 'Rejected') {
            // IncomeAndExpense::where('id', $id)
            $request->validate(['note'=>'required']);
                $income->update([
                    'banker_status' => $request->status,
                    'financier_status' => $request->status,
                    'operation_head_status' => $request->status,
                    'superviser_status' => $request->status,
                    'note' => $request->note,
                    'status' => 1,
                ]);
        }
        return response()->json([
            "status_code" => 200,
            "status" => "Status Updated",
        ]);
    }

    public function incomeReport()
    {
        $user = auth()->user();
        $deposit = IncomeAndExpense::get();
        $created_by = User::all();
        return view('income.reports', compact('user', 'created_by', 'deposit'));
    }
    public function allIncomeReports(Request $request)
    {
        $filters = $request->all();
        $incomeQuery = IncomeAndExpense::query()
            ->with(['category', 'paymentMode', 'createdBy','ourBankDetail'])
            ->where('type', 'income')
            // ->where('banker_status', 'Verified')
            // ->where('banker_status','Rejected')
            ->where('status', 1);

        if (isset($filters['start_date'])) {
            $incomeQuery->whereDate('date', '>=', $filters['start_date']);
        }

        if (isset($filters['end_date'])) {
            $incomeQuery->whereDate('date', '<=', $filters['end_date']);
        }

        if (isset($filters['created_by'])) {
            $incomeQuery->where('created_by', $filters['created_by']);
        }
        $income = $incomeQuery->orderBy('created_at', 'desc')->get();
        $IncomeRecordsCount = $income->count();
        $TotalIncomeAmount = $income->sum('amount');

        // \Log::info($income);
        // dd($count, $amount);
        return DataTables::of($income)
            ->addColumn('category_name', function ($income) {
                return $income->category->category_name ?? '-';
            })
            ->addColumn('date', function ($income) {
                $carbonDate = Carbon::parse($income->date);
                return $carbonDate->format('Y F j') ?? '-';
            })
            ->addColumn('paymentMode', function ($income) {
                return $income->paymentMode->payment_mode_name ?? '-';
            })
            ->addColumn('ourBankDetail', function ($income) {
                return $income->ourBankDetail->bank_name ?? '-';
            })
            ->addColumn('created_by_name', function ($income) {
                return $income->createdBy->name ?? '-';
            })
            ->addColumn('image', function ($income) {
                $imagePath = 'storage/' . $income->attachment;
                $imageUrl = asset($imagePath);

                if (file_exists(public_path($imagePath))) {
                    return  $imageUrl;
                } else {
                    return 'No Image';
                }
            })
            ->addColumn('createdBy', function ($income) {
                return $income->createdBy->name ?? '-';
            })
            ->addColumn('actions', function ($income) {
                // Check if the user can delete the deposit
                if (Auth::user()->can('Income Edit', $income)) {
                // Return the delete button HTML if the user has permission
                return '<a href="' . route("income.edit", $income->id) . '"><i class="fa-solid fa-edit"></i></a>';
                } else {
                // Optionally, return an empty string or a disabled button
                    return '';
                }
            })
            ->with([
                'IncomerecordsCount' => $IncomeRecordsCount,
                'totalIncomeAmount' => $TotalIncomeAmount,
            ])
            ->rawColumns(['image', 'actions'])
            ->make(true);
    }

    public function exportAllIncomeResults(Request $request)
    {
        try {
            $filters = $request->all();
            $incomeQuery = IncomeAndExpense::query()
                ->select(
                    'id',
                    'title',
                    'note',
                    'category_id',
                    'date',
                    'amount',
                    'payment_mode_id',
                    'ref_no',
                    'our_bank_detail_id',
                    'banker_status',
                    'status',
                    'created_by'
                )
                ->with(['category', 'paymentMode', 'ourBankDetail', 'createdBy'])
                ->where('type', 'income')
                // ->where('banker_status', 'Verified')
                ->where('status', 1);

            if (isset($filters['start_date'])) {
                $incomeQuery->whereDate('date', '>=', $filters['start_date']);
            }

            if (isset($filters['end_date'])) {
                $incomeQuery->whereDate('date', '<=', $filters['end_date']);
            }

            if (isset($filters['created_by'])) {
                $incomeQuery->where('created_by', $filters['created_by']);
            }

            $income = $incomeQuery->orderBy('created_at', 'desc')->get();

            return Excel::download(new IncomeExport($income), 'incomeReport.xlsx');
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
