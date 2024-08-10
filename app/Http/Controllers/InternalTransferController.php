<?php

namespace App\Http\Controllers;

use App\Exports\InternalTransferExport;
use App\Jobs\OurBankDetailBalanceJob;
use App\Models\InternalTransfer;
use App\Models\OurBankDetail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\DataTables;

class InternalTransferController extends Controller
{
    public function index()
    {
        $internalTransfer = InternalTransfer::where('status', 0)->get();
        $ourbank = OurBankDetail::where('type','Bank')->where('status', 1)->get();

        return view('internalTransfer.index', compact('internalTransfer', 'ourbank'));
    }

    public function create()
    {
        $banks = OurBankDetail::where('status', 1)->get();
        //    $category::Category
        return view('internalTransfer.create', compact('banks'));
    }
    public function store(Request $request)
    {
        // dd($request->all());
        $request->validate(
            [
                'title' => ['required', 'max:100'],
                'date' => ['required'],
                'bank_from' => ['required', 'different:bank_to'],
                'bank_to' => ['required', 'different:bank_from'],
                'amount' => ['required', 'integer'],
                'remark' => ['required'],
            ],
            [
                'amount.integer' => 'The amount field must be an Number.',
                'bank_from.different' => 'The from Bank field and To Bank  must be different.',
                'bank_to.different' => 'The from Bank field and To Bank  must be different.',
            ]
        );

        InternalTransfer::create([
            'title' => $request->title,
            'date' => $request->date,
            'bank_from' => $request->bank_from,
            'bank_to' => $request->bank_to,
            'amount' => $request->amount,
            'remark' => $request->remark,
            'created_by' => auth()->user()->id,
        ]);
        return redirect(route('InternalTransfer.index'))->with('success', 'Transfer request has created successfully');
    }
    public function edit($id)
    {
        $banks = OurBankDetail::where('status', 1)->get();
        $internalTransfer = InternalTransfer::find($id);
        // dd($internalTransfer);
        //    $category::Category
        return view('internalTransfer.edit', compact('banks', 'internalTransfer'));
    }
    public function update(Request $request, $id)
    {

        // dd($request->all());
        $request->validate([
            'title' => ['required', 'max:255'],
            'date' => ['required'],
            'bank_from' => ['required', 'different:bank_to',],
            'bank_to' => ['required', 'different:bank_from'],
            'attachment' => ['nullable', 'mimes:png,jpg,jpeg'],
            'amount' => ['required', 'integer'],
            // 'payment_mode_id' => ['required'],
            'utr' => ['required', Rule::unique('internal_transfers')->ignore($id), 'regex:/^[a-zA-Z0-9]+$/'],
            // 'our_bank_detail_id' => ['required'],
            'remark' => ['required'],
        ], [
            'utr.integer' => 'The UTR Number field must be an Number.',
            'amount.integer' => 'The Amount field must be an Number.',
            'ref_no.regex' => 'The Ref No field must contain only letters and numbers.',
            'bank_from.different' => 'The from Bank field and To Bank  must be different.',
            'bank_to.different' => 'The from Bank field and To Bank  must be different.',

        ]);


        $internalTransfer = InternalTransfer::find($id);
        // dd($request->all(),$internalTransfer);
        $oldAmount = $internalTransfer->amount;
        $oldImage = $internalTransfer->attachment;
        $oldFromBank = $internalTransfer->bank_from;
        $oldToBank = $internalTransfer->bank_to;
        $path = '';
        if ($request->attachment) {
            if ($internalTransfer->attachment) {
                Storage::disk('public')->delete($internalTransfer->attachment);
            }
            $attachment = $request->file('attachment');
            $ext = $attachment->extension();
            $contents = file_get_contents($attachment);
            $fileName = Str::random(20);
            $path = "attachments/$fileName.$ext";
            Storage::disk('public')->put($path, $contents);
        }
        $internalTransfer->update([
            'title' => $request->title,
            'date' => $request->date,
            'expense_category_id' => $request->expense_category_id,
            'amount' => $request->amount,
            'utr' => $request->utr,
            'attachment' => $path ? $path : $oldImage,
            'bank_to' => $request->bank_to,
            'bank_from' => $request->bank_from,

            'remark' => $request->remark,
            // 'payment_mode_id' => $request->payment_mode_id,
            // 'type' => '',
            'updated_by' => auth()->user()->id,
        ]);
        if ($internalTransfer->superviser_status == "Verified" && $internalTransfer->banker_status == "Verified") {


            // if ($internalTransfer->bank_from != $oldFromBank || $internalTransfer->bank_to != $oldToBank ) {
            try {
                $newAmount = '';
                $operation = '';

                if ($oldAmount > $internalTransfer->amount && $oldAmount != $internalTransfer->amount) {
                    $newAmount = $oldAmount - $internalTransfer->amount;
                    $operation = 'Decrease';
                } elseif ($oldAmount < $internalTransfer->amount && $oldAmount != $internalTransfer->amount) {
                    $newAmount = $internalTransfer->amount - $oldAmount;
                    $operation = 'Increase';
                } elseif ($oldAmount == $internalTransfer->amount) {
                    $newAmount = $internalTransfer->amount;
                }

                $jobRequest = [
                    'req_amount' => $newAmount,
                    'type' => 'InternalTransfer Edit',
                    'operation' => 'Both Operation',
                    'old_amount' => $oldAmount,
                    'new_amount' => $internalTransfer->amount,
                    'it_operation' => $operation,
                    'old_from_bank' => $oldFromBank,
                    'old_to_bank' => $oldToBank,
                    'new_from_bank' => $internalTransfer->bank_from,
                    'new_to_bank' => $internalTransfer->bank_to,
                    'created_by' => auth()->user()->id,
                ];

                OurBankDetailBalanceJob::dispatch($jobRequest);

                // return response()->json(['message' => 'internalTransfer request queued successfully']);
            } catch (\Exception $e) {
                return response()->json(['error' => 'Failed to queue InternalTransfer request: ' . $e->getMessage()], 500);
            }
            // }
        }

        return redirect(route('dashboard'))->with('success', 'InternalTransfer Updated sucessfully');
    }

    public function transferStatus(Request $request)
    {
        // dd($request->all());
        $id = $request->transferId;
        $AuthUserId = auth()->user()->id;
        $internalTransfer = InternalTransfer::where('id', $request->transferId)
            ->lockForUpdate()
            ->first();
        if ($internalTransfer && $request->type == "superviser_status" && $internalTransfer->superviser_status == 'Pending') {
            // dd($request->all(), 'superviser_status');

            if ($request->status == 'Verified') {
                // InternalTransfer::where('id', $id)
                $internalTransfer->update([
                    'superviser_status' => $request->status,
                    'updated_by' => $AuthUserId,
                ]);
            } elseif ($request->status == 'Rejected') {
                $request->validate([
                    'remark' => 'required'
                ]);
                // InternalTransfer::where('id', $id)
                $internalTransfer->update([
                    'superviser_status' => $request->status,
                    'remark' => $request->remark,
                    'status' => 1,
                    'updated_by' => $AuthUserId,
                ]);
            }
        }

        if ($internalTransfer && $request->type === "banker_status" && $internalTransfer->banker_status == 'Pending') {
            // dd($request->all());

            if ($request->status === 'Verified') {

                $validator = Validator::make(
                    $request->all(),
                    [
                        'attachment' => 'required',
                        'utr' => 'required|unique:internal_transfers|regex:/^[a-zA-Z0-9]+$/',
                    ],
                    [
                        'attachment.required' => 'The Attachment is required.',
                        'utr.required' => 'The UTR Number is required.',
                        'utr.regex' => 'The UTR Number field must contain only letters and numbers.',
                        'utr.unique' => 'The UTR Number Already exists.',
                    ]
                );

                if ($validator->fails()) {
                    return response()->json(['errors' => $validator->errors()], 422);
                }

                // $internalTransfer = InternalTransfer::find($id);
                $fromBank = OurBankDetail::find($internalTransfer->bank_from);
                if (($fromBank->amount) >= ($internalTransfer->amount)) {
                    $attachment = $request->file('attachment');
                    $ext = $attachment->extension();
                    $contents = file_get_contents($attachment);
                    $fileName = Str::random(20);
                    $path = "attachments/$fileName.$ext";
                    Storage::disk('public')->put($path, $contents);


                    $internalTransfer->update([
                        'banker_status' => $request->status,
                        'attachment' => $path,
                        'utr' => $request->utr,
                        'status' => 1,
                        'updated_by' => $AuthUserId,
                    ]);

                    try {
                        $jobRequest = [
                            'req_amount' => $internalTransfer->amount,
                            'type' => 'InternalTransfer',
                            'operation' => 'Both',
                            'from_bank' => $internalTransfer->bank_from,
                            'to_bank' => $internalTransfer->bank_to,
                            'created_by' => auth()->user()->id,
                        ];
                        OurBankDetailBalanceJob::dispatch($jobRequest);

                        // return response()->json(['message' => 'Income request queued successfully']);
                    } catch (\Exception $e) {
                        return response()->json(['error' => 'Failed to queue Income request: ' . $e->getMessage()], 500);
                    }

                    // $currentDate = now();
                    // $lastInternalTransfer = InternalTransfer::latest()->first();
                    // $lastDate = $lastInternalTransfer->created_at;
                    // if ($currentDate->lessThanOrEqualTo($lastDate)) {
                    //     OurBankDetail::query()->update(['count' => 0]);
                    // }
                    // // $toBank = OurBankDetail::find($internalTransfer->bank_to);
                    // // $TotalFromBank = ($fromBank->amount) - ($internalTransfer->amount);
                    // // $TotalToBank = ($toBank->amount) + ($internalTransfer->amount);
                    // // $fromBank->update([
                    // //     'amount' => $TotalFromBank,
                    // //     'count'=>$fromBank->count+1,
                    // // ]);
                    // // $toBank->update([
                    // //     'amount' => $TotalToBank,
                    // // ]);
                } else {
                    return response()->json(['errors' => ['no_balance' => "Insufficient Balance in this account"]], 422);
                }
            } elseif ($request->status == 'Rejected') {
                // InternalTransfer::where('id', $id)
                $request->validate([
                    'remark' => 'required'
                ]);
                $internalTransfer->update([
                    'banker_status' => $request->status,
                    'remark' => $request->remark,
                    'status' => 1,
                    'updated_by' => $AuthUserId,
                ]);
            }
        }


        return response()->json(['status_code' => 200, 'success' => 'status updated']);
    }
    public function internalTransferReport()
    {
        // dd('hello');
        $user = auth()->user();
        $created_by = User::all();
        $internalTransfer = InternalTransfer::get();
        return view('internalTransfer.reports', compact('internalTransfer', 'user', 'created_by'));
    }
    public function allInternalTransferReports(Request $request)
    {
        // dd('hai');
        $filters = $request->all();
        $internalTransferQuery = InternalTransfer::query()
            ->with(['bankFrom', 'bankTo', 'createdBy'])
            ->where('status', 1);


        if (isset($filters['start_date'])) {
            $internalTransferQuery->whereDate('date', '>=', $filters['start_date']);
        }

        if (isset($filters['end_date'])) {
            $internalTransferQuery->whereDate('date', '<=', $filters['end_date']);
        }

        if (isset($filters['created_by'])) {
            $internalTransferQuery->where('created_by', $filters['created_by']);
        }

        $internalTransfer = $internalTransferQuery->orderBy('created_at', 'desc')->get();
        $totalTransferCount = $internalTransfer->count();
        $totalTransferAmount = $internalTransfer->sum('amount');
        // dd($internalTransfer);
        return DataTables::of($internalTransfer)
            ->addColumn('bankFrom', function ($internalTransfer) {
                return $internalTransfer->bankFrom->bank_name ?? '-';
            })
            ->addColumn('date', function ($income) {
                $carbonDate = Carbon::parse($income->date);
                return $carbonDate->format('Y F j') ?? '-';
            })
            ->addColumn('bankTo', function ($internalTransfer) {
                return $internalTransfer->bankTo->bank_name ?? '-';
            })
            ->addColumn('image', function ($internalTransfer) {
                $imagePath = 'storage/' . $internalTransfer->attachment;
                $imageUrl = asset($imagePath);

                if (file_exists(public_path($imagePath))) {
                    return $imageUrl;
                } else {
                    return 'No Image';
                }
            })
            ->addColumn('createdBy', function ($internalTransfer) {
                return $internalTransfer->createdBy->name ?? '-';
            })
            ->addColumn('actions', function ($internalTransfer) {
                // Check if the user can delete the deposit
                if (Auth::user()->can('Internal Transfer Edit', $internalTransfer)) {
                    // Return the delete button HTML if the user has permission
                    return '<a href="' . route("internalTransfer.edit", $internalTransfer->id) . '"><i class="fa-solid fa-edit"></i></a>';
                } else {
                    // Optionally, return an empty string or a disabled button
                    return '';
                }
            })
            ->with([
                "totalTransferCount" => $totalTransferCount,
                "totalTransferAmount" => $totalTransferAmount,
            ])
            ->rawColumns(['image', 'actions'])
            ->make(true);
    }
    public function exportAllInternalTransferResults(Request $request)
    {
        // dd('hai');
        try {
            $filters = $request->all();
            $internalTransferQuery = InternalTransfer::query()
                ->select(
                    'id',
                    'title',
                    'remark',
                    'date',
                    'amount',
                    'utr',
                    'bank_from',
                    'bank_to',
                    'banker_status',
                    'status',
                    'created_by'
                )
                ->with(['bankFrom', 'bankTo', 'createdBy'])
                // ->where('banker_status', 'Verified')
                ->where('status', 1);

            if (isset($filters['start_date'])) {
                $internalTransferQuery->whereDate('date', '>=', $filters['start_date']);
            }

            if (isset($filters['end_date'])) {
                $internalTransferQuery->whereDate('date', '<=', $filters['end_date']);
            }

            if (isset($filters['created_by'])) {
                $internalTransferQuery->where('created_by', $filters['created_by']);
            }

            $internalTransfer = $internalTransferQuery->orderBy('created_at', 'desc')->get();

            return Excel::download(new InternalTransferExport($internalTransfer), 'internalTransferReport.xlsx');
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
