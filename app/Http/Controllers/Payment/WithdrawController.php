<?php

namespace App\Http\Controllers\Payment;

use App\Exports\DepositExport;
use App\Exports\WithdrawExport;
use App\Http\Controllers\Controller;
use App\Jobs\OurBankDetailBalanceJob;
use App\Models\ApprovalWorkTimeline;
use App\Models\bank_detail;
use App\Models\deposit;
use App\Models\GatewayCategory;
use App\Models\OurBankDetail;
use App\Models\PlatForm;
use App\Models\PlatformDetails;
use App\Models\User;
use App\Models\UserRegistration;
use App\Models\Withdraw;
use App\Models\WithdrawUtr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class WithdrawController extends Controller
{
    protected $client;
    protected $key;
    protected $secret;

    public function __construct()
    {
        $this->key = config('razorpay.key');
        $this->secret = config('razorpay.secret');
        $this->client = new Client([
            'base_uri' => 'https://api.razorpay.com/v1/',
            'auth' => [$this->key, $this->secret]
        ]);
    }
    public function index()
    {


        $withdraws = Withdraw::with('platformDetail.player', 'bank', 'employee')->get();
        $ourbank = OurBankDetail::all();

        // dd($withdraws->platformDetail[0]);
        // dd($withdraws);
        return view('withdraw.all_withdraw', compact('withdraws'));
    }

    public function allWithdrawDatas()
    {

        $withdraw = Withdraw::with(
            'platformDetail.player',
            'bank',
            'employee',
            'withdrawUtr',
            'approvalTimeLine',
            'approvalTimeLine.bankerUser',
            'approvalTimeLine.createdBy',
            'approvalTimeLine.adminUser',
            'approvalTimeLine.ccUser'
        )
            ->select([
                'withdraws.id',
                'withdraws.amount',
                'withdraws.d_chips',
                'withdraws.image',
                'withdraws.rolling_type',
                'withdraws.admin_status',
                'withdraws.banker_status',
                'withdraws.isInformed',
                'withdraws.created_by',
                'withdraws.platform_detail_id',
                'withdraws.bank_name_id',
            ])->orderBy('withdraws.created_at', 'desc');

        // dd($withdraw);
        return DataTables::of($withdraw)
            ->addColumn('player_name', function ($withdraw) {
                return $withdraw->platformDetail->platform_username ?? '-';
            })
            ->addColumn('withdraw_bank', function ($withdraw) {
                return $withdraw->withdrawUtr->ourBankDetail->bank_name ?? '-';
            })


            ->addColumn('account_number', function ($withdraw) {
                return $withdraw->bank->account_number ?? '-';
            })
            ->addColumn('bank_name', function ($withdraw) {
                return $withdraw->bank->bank_name ?? '-';
            })
            ->addColumn('withdraw_utr', function ($withdraw) {
                return $withdraw->withdrawUtr->utr ?? '-';
            })
            ->addColumn('platform_name', function ($withdraw) {
                return $withdraw->platformDetail->platform->name ?? '-';
            })
            ->addColumn('created_by', function ($withdraw) {
                return $withdraw->employee->name ?? '-';
            })
            ->addColumn('timeline', function ($deposit) {
                // dd($deposit->approvalTimeLine);

                return $deposit->approvalTimeLine ?? '-';
            })
            ->addColumn('image', function ($withdraw) {
                $imagePath = 'storage/' . $withdraw->image;
                $imageUrl = asset($imagePath);

                // Check if the image file exists
                if (file_exists(public_path($imagePath))) {
                    return $imageUrl;
                    // If the image exists, show it in a modal
                    // return '<a href="#" class="image-link" data-toggle="modal" data-target="#imageModal" data-image="' . $imageUrl . '"><img src="' . $imageUrl . '" alt="Image" style="max-width: 100px; max-height: 100px;"></a>';
                } else {
                    // If the image doesn't exist, show a placeholder text
                    return 'No Image';
                }
            })
            ->rawColumns(['image'])

            ->make(true);
    }

    public function allWithdrawReports(Request $request)
    {

        $filters = $request->all();
        $withdraws = Withdraw::with(
            'bank',
            'platForm',
            'user',
            'employee',
            'platformDetail',
            'withdrawUtr'
        )
            // ->where('admin_status', 'Verified')
            // ->where('banker_status', 'Verified')
            // ->where('isInformed', 1)
            // ->where('isInformed', 1)
            // ->get();
            // $withdraws = DB::table('withdraws')
            //     ->join('platform_details', 'withdraws.platform_detail_id', '=', 'platform_details.id')
            //     ->join('bank_details', 'withdraws.bank_name_id', '=', 'bank_details.id')
            //     ->leftJoin('users', 'withdraws.created_by', '=', 'users.id')
            //     ->leftJoin('withdraw_utrs', 'withdraws.id', '=', 'withdraw_utrs.withdraw_id')

            //     ->select([
            //         'withdraws.id',
            //         DB::raw('DATE(withdraws.created_at) as created_at'),
            //         'platform_details.platform_username',
            //         'bank_details.bank_name',
            //         'bank_details.account_number',
            //         'withdraws.amount',
            //         'withdraws.d_chips',
            //         'withdraws.image',
            //         'withdraws.rolling_type',
            //         'withdraws.admin_status',
            //         'withdraws.banker_status',
            //         'withdraws.isInformed',
            //         'users.name',
            //         'withdraw_utrs.utr',
            //     ])
            ->when(isset($filters['start_date']), function ($query) use ($filters) {
                return $query->whereDate('created_at', '>=', $filters['start_date']);
            })
            ->when(isset($filters['end_date']), function ($query) use ($filters) {
                return $query->whereDate('created_at', '<=', $filters['end_date']);
            })
            ->when(isset($filters['created_by']), function ($query) use ($filters) {
                return $query->where('created_by', $filters['created_by']);
            })
            ->when(isset($filters['platform']), function ($query) use ($filters) {
                // return $query->where('platform_details.platform_id', $filters['platform']);
                return $query->whereHas('platformDetail', function ($query) use ($filters) {
                    $query->where('platform_id', $filters['platform']);
                });
            })
            ->where('admin_status', 'Verified')
            ->where('banker_status', 'Verified')
            ->where('isInformed', 1)
            ->orderBy('created_at', 'desc')
            ->get();


        $deposits = deposit::with(['platformDetail', 'user', 'leadSource', 'ourBankDetail'])
            ->where('admin_status', 'Verified')
            ->where('banker_status', 'Verified')
            ->where('isInformed', 1)
            ->when(isset($filters['start_date']), function ($query) use ($filters) {
                return $query->whereDate('created_at', '>=', $filters['start_date']);
            })
            ->when(isset($filters['end_date']), function ($query) use ($filters) {
                return $query->whereDate('created_at', '<=', $filters['end_date']);
            })
            ->when(isset($filters['created_by']), function ($query) use ($filters) {
                return $query->where('created_by', $filters['created_by']);
            })
            ->when(isset($filters['platform']), function ($query) use ($filters) {
                return $query->whereHas('platformDetail', function ($query) use ($filters) {
                    $query->where('platform_id', $filters['platform']);
                });
            })
            ->orderBy('created_at', 'desc')
            ->get();

        // dd('helo');
        $totalBonusAmount = 0;
        foreach ($deposits as $deposit) {
            if ($deposit->admin_status == 'Verified' && $deposit->banker_status == 'Verified') {

                $deposit_amount =  $deposit->deposit_amount;
                $deposit_bonus = $deposit->bonus;
                $bonus_amount = ($deposit_bonus / 100) * $deposit_amount;

                // Accumulate bonus amounts
                $totalBonusAmount += $bonus_amount;
            }
        }
        $totalDepositAmount = $deposits->sum('deposit_amount');
        $totalDepositRecords = $deposits->count();
        $totalBonus = $deposits->sum('bonus');
        $totalTotalDepositAmount = $deposits->sum('total_deposit_amount');


        $totalWithdrawAmount = $withdraws->sum('amount');
        $totalRecords = $withdraws->count();
        return DataTables::of($withdraws)
            ->addColumn('player_name', function ($withdraw) {
                return $withdraw->platformDetail->platform_username ?? '-';
            })
            ->addColumn('bank_name', function ($withdraw) {
                return $withdraw->bank->bank_name ?? '-';
            })
            ->addColumn('account_number', function ($withdraw) {
                return $withdraw->bank->account_number ?? '-';
            })
            ->addColumn('created_by', function ($withdraw) {
                return $withdraw->employee->name ?? '-';
            })
            ->addColumn('withdraw_utr', function ($withdraw) {
                return $withdraw->withdrawUtr->utr ?? '-';
            })
            ->addColumn('withdraw_bank', function ($withdraw) {
                return $withdraw->withdrawUtr->ourBankDetail->bank_name ?? '-';
            })

            ->addColumn('image', function ($withdraw) {
                $imagePath = 'storage/' . $withdraw->image;
                $imageUrl = asset($imagePath);

                // Check if the image file exists
                if (file_exists(public_path($imagePath))) {
                    // If the image exists, show it in a modal
                    return $imageUrl;

                    // return '<a href="#" class="image-link" data-toggle="modal" data-target="#imageModal" data-image="' . $imageUrl . '"><img src="' . $imageUrl . '" alt="Image" style="max-width: 100px; max-height: 100px;"></a>';
                } else {
                    // If the image doesn't exist, show a placeholder text
                    return 'No Image';
                }
            })
            ->rawColumns(['image'])
            ->with([
                'totalWithdrawAmount' => $totalWithdrawAmount,
                'totalRecords' => $totalRecords,
                'totalDepositAmount' => $totalDepositAmount,
                'totalBonus' => $totalBonus,
                'totalTotalDepositAmount' => $totalTotalDepositAmount,
                'totalDepositRecords' => $totalDepositRecords,
                'totalBonusAmount' => $totalBonusAmount,
            ])
            ->make(true);
    }

    public function exportAllWithdrawResults(Request $request)
    {
        try {
            $filters = $request->all();
            $withdraws = Withdraw::query()
                ->with(['platformDetail', 'bank', 'withdrawUtr', 'approvalTimeLine'])
                // $withdraws = DB::table('withdraws')
                //     ->join('platform_details', 'withdraws.platform_detail_id', '=', 'platform_details.id')
                //     ->join('user_registrations', 'platform_details.player_id', '=', 'user_registrations.id')
                //     ->join('bank_details', 'withdraws.bank_name_id', '=', 'bank_details.id')
                //     ->leftJoin('users', 'withdraws.created_by', '=', 'users.id')
                //     ->leftJoin('withdraw_utrs', 'withdraws.id', '=', 'withdraw_utrs.withdraw_id')
                //     ->leftJoin('our_bank_details', 'withdraw_utrs.our_bank_detail', '=', 'our_bank_details.id')

                //     ->select([
                //         'withdraws.id',
                //         DB::raw('DATE(withdraws.created_at) as created_at'),
                //         'platform_details.platform_username',
                //         'bank_details.bank_name',
                //         'bank_details.account_number',
                //         'withdraw_utrs.utr',
                //         'withdraws.amount',
                //         'withdraws.d_chips',
                //         'withdraws.rolling_type',
                //         'withdraws.admin_status',
                //         'withdraws.banker_status',
                //         'withdraws.isInformed',
                //         'users.name',
                //         'user_registrations.mobile',
                //         'our_bank_details.bank_name as our_bank_name',

                //     ])
                ->when(isset($filters['start_date']), function ($query) use ($filters) {
                    return $query->whereDate('withdraws.created_at', '>=', $filters['start_date']);
                })
                ->when(isset($filters['end_date']), function ($query) use ($filters) {
                    return $query->whereDate('withdraws.created_at', '<=', $filters['end_date']);
                })
                ->when(isset($filters['created_by']), function ($query) use ($filters) {
                    return $query->where('withdraws.created_by', $filters['created_by']);
                })
                ->when(isset($filters['platform']), function ($query) use ($filters) {
                    return $query->where('platform_details.platform_id', $filters['platform']);
                })
                ->where('admin_status', 'Verified')
                ->where('banker_status', 'Verified')
                ->where('isInformed', 1)
                ->orderBy('created_at', 'desc')
                ->get();

            foreach ($withdraws as $withdraw) {
                $withdraw->isInformed = $withdraw->isInformed ? 'Yes' : 'No';
            }
            return Excel::download(new WithdrawExport($withdraws), 'Withdraw.xlsx');
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function withdrawPending()
    {

        // dd(UserRegistration::with('platformDetails', 'platformDetails.platform', 'platformDetails.deposit')->get());
        $user = auth()->user();
        $pendingCount = Withdraw::with('approvalTimeLine')->where('status', 'Gateway Process')->where('banker_status', 'processing')->where('isInformed', 0)->count();
        if ($user->roles[0]->hasPermissionTo('CC DPending') && $user->roles[0]->hasPermissionTo('CC WPending')) {
            $withdraws = Withdraw::with('approvalTimeLine')->where('status', 'On Process')->get();
        } else {
            if ($user->roles[0]->hasPermissionTo('Withdraw Banker Enable')) {
                $withdraws = Withdraw::with('approvalTimeLine')->where('banker_status', 'pending')->get();
            }
            if ($user->roles[0]->hasPermissionTo('Withdraw Admin Enable')) {
                $withdraws = Withdraw::with('approvalTimeLine')->where('admin_status', 'pending')->get();
            }
        }
        $ourbank = OurBankDetail::where('type', 'Bank')->where('status', 1)->get();
        // $gatewayBank = OurBankDetail::where('type', 'Gateway')->where('status', 1)->get();
        $gatewayCategories = GatewayCategory::where('status', 1)->get();


        // dd($withdraws);
        return view('withdraw.index', compact('withdraws', 'ourbank', 'gatewayCategories', 'pendingCount'));
    }

    public function withdrawRazorpayPending()
    {

        $user = auth()->user();
        $withdraws = '';
        if ($user->roles[0]->hasPermissionTo('Withdraw Banker Enable')) {
            $withdraws = Withdraw::with('approvalTimeLine')->where('status', 'Gateway Process')->where('banker_status', 'processing')->where('isInformed', 0)->get();
        }

        $ourbank = OurBankDetail::where('type', 'Bank')->where('status', 1)->get();
        // $gatewayBank = OurBankDetail::where('type', 'Gateway')->where('status', 1)->get();
        $gatewayCategories = GatewayCategory::where('status', 1)->get();
        $pendingCount = $withdraws->count();

        // dd($withdraws);
        return view('withdraw.razorpay', compact('withdraws', 'ourbank', 'gatewayCategories', 'pendingCount'));
    }

    public function withdrawPendingcc()
    {

        $user = auth()->user();
        $withdraws = Withdraw::with('approvalTimeLine', 'withdrawUtr')->where('status', 'Completed')->where('isInformed', 0)->get();

        return view('withdraw.cc', compact('withdraws'));
    }
    public function withdraw_status(Request $request)
    {
        // dd($request->all());
        $auth_id = Auth::user()->id;

        $new_status = $request['selectedValue'];
        $userid = $request['userid'];
        $type = $request['type'];
        $platformId = $request['platform_id'];
        $userId = $request['userId'];
        $userPassword = $request['userPassword'];
        $rollover = $request['rollover'];
        $banker_status = "";
        $image_path = $request['imagePath'];
        $remark = $request['remark'];
        $d_chips = $request['d_chips'];
        $up_amt = $request['withdraw_amt'];
        // Get the current year and month
        $year = Carbon::now()->year;
        $month = Carbon::now()->format('m');

        // $user = User::where('id', $userid)->update(['status' => $new_status]);
        $withdrawDetail = Withdraw::find($userid);
        if ($withdrawDetail->assigned_to == $auth_id) {
            DB::beginTransaction();
            try {
                if ($type == 'withdraw_admin') {
                    if ($new_status == "Verified") {
                        $banker_status = "Pending";

                        $withdraw = Withdraw::where('id', $userid)->update([
                            'admin_status' => $new_status,
                            'banker_status' => $banker_status,
                            'rolling_type' => $rollover,
                            'd_chips' => $d_chips,
                            "assigned_to" => null,
                            // 'amount' =>$up_amt

                        ]);
                    }
                    if ($new_status == "Rejected") {
                        $withdraw = Withdraw::where('id', $userid)->update([
                            'admin_status' => $new_status,
                            'banker_status' => "Not Verified",
                            'status' => "Completed",
                            'remark' => $remark,
                            'isInformed' => "0",
                            "assigned_to" => null,

                        ]);
                    }
                    if ($new_status == "Pending") {
                        $banker_status = "Not Verified";
                    }
                    ApprovalWorkTimeline::where('withdraw_id', $userid)->update([
                        'admin_id' => $auth_id,
                        'admin_status_at' => now()->toDateTimeString(),
                        'updated_by' => $auth_id,

                    ]);
                }

                if ($type == 'withdraw_banker') {
                    if ($new_status == "Verified") {
                        if ($request->paymentMode == 'Razorpay') {

                            $request->validate([
                                'gateway_bank' => ['required'],
                            ], [
                                'gateway_bank.required' => 'Gateway Bank required',
                            ]);

                            $withdraw = Withdraw::find($userid);

                            $ourBank = OurBankDetail::find($request->input('gateway_bank'));

                            //balanc validate
                            if ($ourBank->amount >= $withdraw->amount) {
                                //razorpay payout function params
                                $razorpayDetails = [
                                    'amount' => $withdraw->amount,
                                    'account_details_id' => $withdraw->bank_name_id,
                                    'ourBankDetail' => $request->gateway_bank,
                                ];

                                //razorpay payout function call
                                $razorpayresponse = $this->createPayout($razorpayDetails);
                                if ($razorpayresponse['success']==false) {
                                    // Handle the successful payout creation
                                     return response()->json([
                                        'flag'=>3,
                                        'g_error' => $razorpayresponse['error']
                                    ], Response::HTTP_BAD_REQUEST);
                                }

                                //! status related details commented
                                $withdraw->update([
                                    'banker_status' => 'Processing',
                                    'isInformed' => "0",
                                    'status' => "Gateway Process",
                                    // "assigned_to" => null,
                                    "tax" => $razorpayresponse['tax'],
                                    "fees" => $razorpayresponse['fees'],
                                ]);
                                //utr and bank id store
                                $withdraw_utr = WithdrawUtr::create([
                                    'withdraw_id' => $withdraw->id,
                                    'our_bank_detail' => $request->input('gateway_bank'),
                                    'payout_id' => $razorpayresponse['pOutId'],
                                    'utr' => $withdraw->id,
                                    // 'utr' => $utrFetch['utr'] ? $utrFetch['utr'] :'no',
                                ]);
                                //admin status = verified means go next step (this inside the banker method)
                            } else {
                                $result = [
                                    "status_code" => 402,
                                    "status" => "Insufficient Funds",
                                ];
                                return response()->json([($result)]);
                            }

                            //manual payment method
                        } elseif ($request->paymentMode == 'Manual') {
                            $request->validate([
                                'ourBankDetail' => ['required'],
                                'withdrawUtr' => ['required'],
                                'image' => ['required', 'mimes:png,jpg,jpeg'],
                            ], [
                                'ourBankDetail.required' => 'Withdraw Bank required',
                                'withdrawUtr.required' => 'UTR Number required',
                                'image.required' => 'image required',
                                'image.mimes' => 'The image field must be a file of type: png, jpg, jpeg.'
                            ]);
                            $withdraw = Withdraw::find($userid);
                            //*feth bank
                            $ourBank = OurBankDetail::find($request->input('ourBankDetail'));

                            if ($ourBank->amount >= $withdraw->amount) {
                                //*Check the utr is exist
                                $withdrawUtr = WithdrawUtr::where('utr', $request->input('withdrawUtr'))->get();
                                // :
                                if ($withdrawUtr->isEmpty()) {
                                    if ($request->hasFile('image')) {
                                        // Get the uploaded file
                                        $image = $request->file('image');
                                        // $imagePath = $image->store("withdraw_image/{$year}/{$month}", 'public');
                                        // Store the image and get its path (you can customize the path as needed)
                                        $imagePath = $image->store('withdraw_image', 'public');
                                    }
                                    //*update withdraw datas
                                    $withdraw->update([
                                        'banker_status' => $new_status,
                                        'image' => $imagePath,
                                        'isInformed' => "0",
                                        'status' => "Completed",
                                        "assigned_to" => null,

                                    ]);

                                    $withdraw_utr = WithdrawUtr::create([
                                        'withdraw_id' => $withdraw->id,
                                        'our_bank_detail' => $request->input('ourBankDetail'),
                                        'utr' => $request->input('withdrawUtr'),

                                    ]);

                                    if ($withdraw->admin_status == "Verified") {
                                        try {

                                            $jobRequest = [
                                                'req_amount' => $withdraw->amount,
                                                'type' => 'Withdraw',
                                                'operation' => 'Decrease',
                                                'our_bank_detail_id' => $withdraw->withdrawUtr->our_bank_detail,
                                                'created_by' => auth()->user()->id,
                                            ];
                                            OurBankDetailBalanceJob::dispatch($jobRequest);

                                            // return response()->json(['message' => 'Income request queued successfully']);
                                        } catch (\Exception $e) {
                                            DB::rollBack();
                                            return response()->json(['error' => 'Failed to queue Withdraw request: ' . $e->getMessage()], 500);
                                        }
                                    }
                                } else {
                                    $result = [
                                        "flag" => 0,
                                        "status" => "UTR Exist",
                                    ];

                                    return response()->json([($result)]);
                                }
                            } else {
                                $result = [
                                    "status_code" => 402,
                                    "status" => "Insufficient Funds",
                                ];
                                return response()->json([($result)]);
                            }
                        }
                        //if end
                    }
                    if ($new_status == "Rejected") {
                        $withdraw = Withdraw::where('id', $userid)->update([
                            'banker_status' => $new_status,
                            'status' => "Completed",
                            'remark' => $remark,
                            'isInformed' => "0",
                            "assigned_to" => null,

                        ]);
                    }
                    if ($new_status == "Pending") {
                        $withdraw = Withdraw::find($userid);
                        $withdraw->update(['banker_status' => $new_status, 'status' => 'On Process']);
                        if ($withdraw->isInformed == 1) {
                            ApprovalWorkTimeline::where('withdraw_id', $userid)->update([
                                'cc_id' => null,
                                'cc_status_at' => null,
                                'stopped_at' => null,
                                'updated_by' => $auth_id,
                                'status' => 'pending'
                            ]);
                        }
                    }
                    $data = ApprovalWorkTimeline::where('withdraw_id', $userid)->update([
                        'banker_id' => $auth_id,
                        'banker_status_at' => now()->toDateTimeString(),
                        'updated_by' => $auth_id,
                    ]);
                }
                if ($type == 'withdraw_cc') {
                    $withdraw = Withdraw::find($userid);
                    if ($withdraw->status != 'On Process' && $withdraw->banker_status != 'Pending') {
                        $withdraw->update([
                            'isInformed' => 1,
                            "assigned_to" => null,
                        ]);
                        //timeline
                        ApprovalWorkTimeline::where('withdraw_id', $userid)->update([
                            'cc_id' => $auth_id,
                            'cc_status_at' => now()->toDateTimeString(),
                            'stopped_at' => now()->toDateTimeString(),
                            'updated_by' => $auth_id,
                            'status' => 'Completed'
                        ]);
                    }
                    // DB::rollBack();
                }

                $result = [
                    "flag" => 1,
                    "status" => "Status Updated",
                    "withdraw_status" => $banker_status
                ];
                DB::commit();
                return response()->json([($result)]);
            } catch (\Exception $e) {
                DB::rollBack();
                if ($e instanceof ValidationException) {
                    $errors = $e->validator->errors(); // Get the error messages
                    // dd($errors->messages()); // Dump and die the messages for debugging

                    return response()->json([
                        'errors' => $errors->messages()
                    ], 422); // Return the error messages as JSON with 422 status
                }
                return response()->json(['error' => 'Transaction failed: ' . $e->getMessage()], 500);
            }
        } else {
            $result = [
                "flag" => 2,
                "status" => "status Not Updated",
            ];
            return response()->json([($result)]);
        }
    }

    public function create()
    {
        $gameusernamedetails = PlatformDetails::where('status', 'Active')->get();
        // dd($gameusernamedetails);
        $data = UserRegistration::get();

        $platform = PlatForm::get();
        return view('withdraw.create', compact('data', 'platform', 'gameusernamedetails'));
    }


    public function fetchBanks(Request $request)
    {


        $platform_details_id = $request->country_id;
        $platform_details = PlatformDetails::where('id', $platform_details_id)->first();
        $data['states'] = bank_detail::where("player_id", $platform_details->player_id)
            ->get(["bank_name", "id"]);




        return response()->json($data);
    }

    public function fetchBankDetails(Request $request)
    {


        $bankId = $request->input('bank_id');

        $bankDetails = bank_detail::where('id', $bankId)->with('player')->first();
        // dd($bankDetails);
        // dd($bankDetails);
        if ($bankDetails) {
            // Return bank details as JSON response
            return response()->json([
                'name' => $bankDetails->player->name,
                'mobile' => $bankDetails->player->mobile,
                // 'name' => $bankDetails->player->name,
                'account_id' => $bankDetails->account_number,
                'account_holder_name' => $bankDetails->account_name,
                'ifsc_code' => $bankDetails->ifsc_code,

            ]);
        } else {
            // Handle the case where bank details are not found
            return response()->json(['error' => 'Bank details not found'], 404);
        }
    }
    public function store(Request $request)
    {
        // dd($request->all());
        // 4563
        $auth_id = Auth::user()->id;

        $request->validate([
            'user_id' => 'required',
            'bank_name_id' => 'required',
            'amount' => 'required',

        ]);
        $userSelection = $request->input('user_id');


        // Split the "id" and "name" values using the delimiter (comma)
        // $data =  list($userId, $userName) = explode(',', $userSelection);
        // $value1 = $data[0];
        // $value2 = $data[1];

        // $platform_details_id = PlatformDetails::where('player_id', $request->input('user_id'))->where('platform_id', $value2)->first();


        $platform_details_id = PlatformDetails::where('id', $request->input('user_id'))->first();
        //   dd($platform_details_id);
        $playerIds = Withdraw::with('platformDetail')
            ->whereDate('created_at', Carbon::today())
            ->get()->pluck('platformDetail.player_id')->flatten()->unique();
        $matched = $playerIds->contains((string)$platform_details_id->player_id);

        // dd('hello');
        $withdraw = Withdraw::create([
            'platform_detail_id' => $request->input('user_id'),
            'bank_name_id' => $request->input('bank_name_id'),
            'amount' => $request->input('amount'),
            'is_bonus_eligible' => $matched ? 0 : 1,
            'rolling_type' => "No",
            'admin_status' => "Pending",
            'banker_status' => "Not Verified",
            'status' => "On Process",
            'created_by' => $auth_id
        ]);
        ApprovalWorkTimeline::create([
            'type' => 'withdraw',
            'withdraw_id' => $withdraw->id,
            'created_by' => $auth_id,
        ]);
        return redirect()->route('withdraw.pending');
    }

    public function edit($id)
    {

        $data = withdraw::find($id);
        $platform = PlatForm::all();
        return view('withdraw.edit', compact('data', 'platform',));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'user_id' => 'required',
            'platform' => 'required',
            'bank_name_id' => 'required',
            'amount' => 'required',
            'rolling_type' => 'required',
        ]);

        $withdrawal = Withdraw::findOrFail($id);
        $withdrawal->user_id = $request->input('user_id');
        $withdrawal->platform_id = $request->input('platform');
        $withdrawal->bank_name_id = $request->input('bank_name_id');
        $withdrawal->amount = $request->input('amount');
        $withdrawal->rolling_type = $request->input('rolling_type');

        $withdrawal->save();

        return redirect()->route('withdraw.index');
    }



    public function delete(string $id)
    {
        $withdraw = Withdraw::find($id);
        $withdraw->delete();

        if ($withdraw) {
            return redirect()->route('withdraw.index')
                ->with('success', 'User deleted successfully');
        }

        return back()->with('failure', 'Please try again');
    }

    public function report()
    {
        $withdrawal = Withdraw::where('status', 'Completed')->where('admin_status', 'Verified')->where('banker_status', 'Verified')->where('isInformed', "1")->get();
        $withdrawCount = $withdrawal->count();
        $totalAmount = $withdrawal->sum('amount');
        $created_by = User::all();
        $platforms = PlatForm::all();
        return view('Report.withdraw_report', [
            'withdrawal' => $withdrawal,
            'withdrawal_count' => $withdrawCount,
            'total_amount' => $totalAmount,
            'created_by' => $created_by,
            'platforms' => $platforms
        ]);
    }

    public function filter(Request $request)
    {
        // Get Dates
        $isInformed = $request['isinformed'];
        $adminStatus = $request['admin_status'];

        $toDate = $request['to_date'];
        $isInformedValue = "";
        if ($isInformed == "Verified") {
            $isInformedValue = 1;
        } else {
            $isInformedValue = 0;
        }
        // Query the database based on the filter criteria
        // Eloquent to fetch the filtered data
        $query = Deposit::query();

        // Add conditions based on filter criteria
        if ($request->has('isinformed')) {
            $query->where('isInformed', $isInformedValue);
        }

        if ($request->has('banker_status')) {
            $query->where('banker_status', $request->input('banker_status'));
        }
        if ($request->has('admin_status')) {
            $query->where('admin_status', $request->input('admin_status'));
        }

        if ($request->filled('from_date')) {
            $fromDate = $request['from_date'];
            $query->whereDate('created_at', '>=', $fromDate);
        }

        if ($request->filled('to_date')) {
            $toDate = $request->input('to_date');
            $query->whereDate('created_at', '<=', $toDate);
        }

        // Execute the query and get the filtered data
        $filteredData = $query->with('platformDetail.player', 'ourBankDetail')->get();
        // $filteredData = Deposit::where(...)->get();

        return response()->json($filteredData);
    }
    public function submitForm(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
            'account_name' => 'required',
            'account_number' => 'required',
            'ifsc_code' => 'required',
            'bank_name' => 'required',
        ]);


        $platformtableid = $request->user_id;
        $platformdetailid = PlatformDetails::where('id', $platformtableid)->first();
        $playerid = $platformdetailid->player_id;


        bank_detail::create([
            'player_id' => $playerid,
            'account_name' => $request->account_name,
            'account_number' => $request->account_number,
            'ifsc_code' => $request->ifsc_code,
            'bank_name' => $request->bank_name,
        ]);

        // Redirect back or wherever you want after submission
        return redirect()->back()->with('success', 'Form submitted successfully!');
    }

    public function submitutrForm(Request $request)
    {
        // dd($request);
        $request->validate([
            'user_id' => 'required',
            'upi' => 'required',
        ]);

        $platformtableid = $request->user_id;
        $platformdetailid = PlatformDetails::where('id', $platformtableid)->first();
        $playerid = $platformdetailid->player_id;

        bank_detail::create([
            'player_id' => $playerid,
            'upi' => $request->upi,
            'bank_name' => $request->upi,

        ]);

        return redirect()->back()->with('success', 'Form submitted successfully!');
    }
    public function assignedTo(Request $request)
    {
        // dd($request->all());
        $withdraw = Withdraw::find($request->withdrawId);
        // dd($withdraw);
        if ($request->type == "Admin") {
            // dd('helo');
            if ($withdraw->admin_status == 'Pending') {
                if ($withdraw->assigned_to == auth()->user()->id) {

                    $withdraw->update([
                        'assigned_to' => null,
                    ]);
                    return response()->json([
                        'status' => 200,
                        'action' => 'Assign Removed',
                    ]);
                } else if ($withdraw->assigned_to != '' && $withdraw->assigned_to != auth()->user()->id) {

                    return response()->json([
                        'status' => 400,
                        'action' => 'Assign To Someone',
                    ]);
                }
                if ($withdraw->assigned_to == '') {
                    if ($request->selectedValue == 'true') {
                        $withdraw->update([
                            'assigned_to' => auth()->user()->id,
                        ]);
                        return response()->json([
                            'status' => 200,
                            'action' => 'Assigned',
                        ]);
                    }
                }
            } else {
                return response()->json([
                    'status' => 400,
                    'action' => 'Not Assign',
                ]);
            }
        } elseif ($request->type == "Banker") {
            if ($withdraw->banker_status == 'Pending') {
                if ($withdraw->assigned_to == auth()->user()->id) {

                    $withdraw->update([
                        'assigned_to' => null,
                    ]);
                    return response()->json([
                        'status' => 200,
                        'action' => 'Assign Removed',
                    ]);
                } else if ($withdraw->assigned_to != '' && $withdraw->assigned_to != auth()->user()->id) {

                    return response()->json([
                        'status' => 400,
                        'action' => 'Assign To Someone',
                    ]);
                }
                if ($withdraw->assigned_to == '') {
                    if ($request->selectedValue == 'true') {
                        $withdraw->update([
                            'assigned_to' => auth()->user()->id,
                        ]);
                        return response()->json([
                            'status' => 200,
                            'action' => 'Assigned',
                        ]);
                    }
                }
            } else {
                return response()->json([
                    'status' => 400,
                    'action' => 'Not Assign',
                ]);
            }
        } elseif ($request->type == "CC") {
            if ($withdraw->isInformed == 0) {
                if ($withdraw->assigned_to == auth()->user()->id) {

                    $withdraw->update([
                        'assigned_to' => null,
                    ]);
                    return response()->json([
                        'status' => 200,
                        'action' => 'Assign Removed',
                    ]);
                } else if ($withdraw->assigned_to != '' && $withdraw->assigned_to != auth()->user()->id) {

                    return response()->json([
                        'status' => 400,
                        'action' => 'Assign To Someone',
                    ]);
                }
                if ($withdraw->assigned_to == '') {
                    if ($request->selectedValue == 'true') {
                        $withdraw->update([
                            'assigned_to' => auth()->user()->id,
                        ]);
                        return response()->json([
                            'status' => 200,
                            'action' => 'Assigned',
                        ]);
                    }
                }
            } else {
                return response()->json([
                    'status' => 400,
                    'action' => 'Not Assign',
                ]);
            }
        }
    }
    public function createPayout($razorpayDetails)
    {
        // Fetch user and bank details
        $userDetails = bank_detail::find($razorpayDetails['account_details_id']);
        if (!$userDetails) {
            return response()->json(['error' => 'User bank details not found'], 404);
        }

        $userRegDetails = UserRegistration::find($userDetails->player_id);
        if (!$userRegDetails) {
            return response()->json(['error' => 'User registration details not found'], 404);
        }

        $ourBankDetails = OurBankDetail::find($razorpayDetails['ourBankDetail']);
        if (!$ourBankDetails) {
            return response()->json(['error' => 'Our bank details not found'], 404);
        }

        // Initialize payout data with common fields
        $payoutData = [
            'account_number' => $ourBankDetails['account_number'],
            'amount' => $razorpayDetails['amount'] * 100, // amount in paise
            'currency' => 'INR',
            'mode' => 'IMPS',
            'purpose' => 'payout',
            'queue_if_low_balance' => true
        ];

        try {
            // Condition 1: No contact ID and no fund account ID
            if (empty($userRegDetails->r_pay_contact_id) && empty($userDetails->r_pay_fund_account_id)) {
                $payoutData['fund_account'] = [
                    'account_type' => 'bank_account',
                    'bank_account' => [
                        'name' => $userDetails['account_name'],
                        'ifsc' => $userDetails['ifsc_code'],
                        'account_number' => $userDetails['account_number'],
                    ],
                    'contact' => [
                        'name' => $userRegDetails->name,
                        'contact' => $userRegDetails->mobile,
                    ],
                ];
            }
            // Condition 2: Have contact ID but no fund account ID
            elseif (!empty($userRegDetails->r_pay_contact_id) && empty($userDetails->r_pay_fund_account_id)) {
                // Create fund account first
                $fundAccountData = [
                    'account_type' => 'bank_account',
                    'bank_account' => [
                        'name' => $userDetails['account_name'],
                        'ifsc' => $userDetails['ifsc_code'],
                        'account_number' => $userDetails['account_number'],
                    ],
                    'contact_id' => $userRegDetails->r_pay_contact_id,
                ];

                $fundAccountResponse = $this->client->post('fund_accounts', [
                    'json' => $fundAccountData
                ]);
                $fundAccountBody = $fundAccountResponse->getBody();
                $fundAccount = json_decode($fundAccountBody, true);

                if (!isset($fundAccount['id'])) {
                    return response()->json(['error' => 'Failed to create fund account'], 500);
                }
                $userDetails->update([
                    'r_pay_fund_account_id'=>$fundAccount['id'],
                ]);
             // dd($fundAccount);
                $payoutData['fund_account_id'] = $fundAccount['id'];
            }
            // Condition 3: Have fund account ID (with or without contact ID)
            elseif (!empty($userDetails->r_pay_fund_account_id)) {
                $payoutData['fund_account_id'] = $userDetails->r_pay_fund_account_id;
            }

            // Send the payout request to Razorpay
            $response = $this->client->post('payouts', [
                'json' => $payoutData
            ]);
            $body = $response->getBody();
            $payout = json_decode($body, true);

            if (empty($userRegDetails->r_pay_contact_id)) {
                $userRegDetails->update(['r_pay_contact_id' => $payout['fund_account']['contact_id']]);
            }
            if(empty($userDetails->r_pay_fund_account_id)){
                $userDetails->update([
                    'r_pay_fund_account_id'=>$payout['fund_account_id'],
                ]);
            }

            // Return payout details
            $tax = $payout['tax'] ?? 0;
            return [
                'success' => true,
                'fees' => $payout['fees'] / 100,
                'pOutId' => $payout['id'],
                'tax' => $tax / 100
            ];

        }catch (ClientException $e) {
            $response = $e->getResponse();
            $responseBody = json_decode($response->getBody()->getContents(), true);
            $errorDescription = $responseBody['error']['description'] ?? 'An error occurred';

            return [
                'success' => false,
                'error' => $errorDescription
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => 'An internal server error occurred'
            ];
        }
    }



    public function getGatewayBanks(Request $request)
    {
        // dd($request->all());
        $gatewayBanks = OurBankDetail::where('type', 'Gateway')->where('category', $request->gatewayId)->get();
        return response()->json($gatewayBanks);
    }
    public function getUtr(Request $request)
    {
        try {
            // $auth_id = auth()->user()->id;
            $gatewayWithdraw = Withdraw::where('status', 'Gateway Process')->where('banker_status', 'Processing')->get();

            if (count($gatewayWithdraw) > 0) {
                foreach ($gatewayWithdraw as $gateway) {
                    $withdrawUtr = WithdrawUtr::where('withdraw_id', $gateway->id)->first();
                    $approvalTimeline = ApprovalWorkTimeline::where('withdraw_id', $gateway->id)->first();

                    $response = $this->client->request('GET', "payouts/{$withdrawUtr->payout_id}");
                    $statusCode = $response->getStatusCode();

                    if ($statusCode == 200) {
                        $body = $response->getBody()->getContents();
                        $payoutData = json_decode($body, true);

                        if (in_array($payoutData['status'], ['failed', 'rejected', 'reversed', 'cancelled'])) {
                            $reason = $payoutData['failure_reason'] ?? 'Unknown reason';

                            $gateway->update([
                                'banker_status' => 'Rejected',
                                'status' => "Completed",
                                "assigned_to" => null,
                                'isInformed' => "0",
                                'remark'=> $reason,
                            ]);

                            $approvalTimeline->update([
                                // 'banker_id' => $approvalTimeline->banker_id,
                                'banker_status_at' => now()->toDateTimeString(),
                                // 'updated_by' => $approvalTimeline->banker_id,
                            ]);

                            // return response()->json([
                            //     'code' => 0,
                            //     'payment_status' => $payoutData['status'],
                            //     'failure_reason' => $reason,
                            //     'utr' => $payoutData['status']
                            // ]);
                        } elseif ($payoutData['status'] == 'processed') {
                            if (!empty($payoutData['utr'])) {
                                $withdrawUtr->update([
                                    'utr' => $payoutData['utr'],
                                ]);

                                if ($gateway->admin_status == "Verified") {
                                    try {

                                        $jobRequest = [
                                            'req_amount' => $gateway->amount,
                                            'type' => 'Withdraw',
                                            'operation' => 'Decrease',
                                            'our_bank_detail_id' => $withdrawUtr->our_bank_detail,
                                            'created_by' => $approvalTimeline->banker_id,
                                        ];
                                        OurBankDetailBalanceJob::dispatch($jobRequest);
                                    } catch (\Exception $e) {
                                        return response()->json(['error' => 'Failed to queue Withdraw request: ' . $e->getMessage()], 500);
                                    }
                                }

                                $gateway->update([
                                    'banker_status' => 'Verified',
                                    'status' => "Completed",
                                    "assigned_to" => null,
                                    'isInformed' => "0",
                                ]);

                                $approvalTimeline->update([
                                    // 'banker_id' => $approvalTimeline->banker_id,
                                    'banker_status_at' => now()->toDateTimeString(),
                                    // 'updated_by' => $approvalTimeline->banker_id,
                                ]);

                                // return response()->json([
                                //     'utr' => $payoutData['utr'],
                                //     'code' => 1,
                                //     'payment_status' => $payoutData['status']
                                // ]);
                            }
                        }
                    }
                }
                 return response()->json([
                     'code' => 1,
                     'status' =>'record updated successfully',
                    ],200);
            } else {
                return response()->json(['error' => 'No records'], 400);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    public function razorpayStatus(Request $request)
    {
        try {
            $auth_id = auth()->user()->id;
            $withdrawUtr = WithdrawUtr::where('withdraw_id', $request->withdrawId)->first();
            $withdraw = Withdraw::where('id', $request->withdrawId)->first();

            if ($withdrawUtr && $withdraw->banker_status == 'Processing' && $withdraw->status == 'Gateway Process') {

                $response = $this->client->request('GET', "payouts/{$withdrawUtr->payout_id}");
                $statusCode = $response->getStatusCode();

                if ($statusCode == 200) {
                    $body = $response->getBody()->getContents();
                    $payoutData = json_decode($body, true);

                    //dummy value for testing make sure remove this
                    // $payoutData['status']='failed';
                    //  $reason=,
                    if (in_array($payoutData['status'], ['failed', 'rejected', 'reversed', 'cancelled'])) {
                        $reason = $payoutData['failure_reason'] ?? 'Unknown reason';
                        return response()->json([
                            'code' => 0,
                            'payment_status' => $payoutData['status'],
                            'failure_reason' => $reason,
                            'utr' => $payoutData['status']
                        ]);
                    } elseif ($payoutData['status'] == 'processed') {
                        if (!empty($payoutData['utr'])) {

                            return response()->json([
                                'utr' => $payoutData['utr'],
                                'code' => 1,
                                'payment_status' => $payoutData['status']
                            ]);
                        }
                    } else {
                        return response()->json([
                            'code' => 0,
                            'payment_status' => $payoutData['status'],
                            'utr' => 'No UTR'
                        ]);
                    }
                } else {
                    return response()->json(['error' => 'Failed to fetch payout details'], $statusCode);
                }
            } else {
                return response()->json(['error' => 'Invalid withdraw ID'], 400);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
