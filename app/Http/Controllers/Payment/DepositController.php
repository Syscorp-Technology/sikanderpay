<?php

namespace App\Http\Controllers\Payment;

use App\Exports\DepositExport;
use App\Http\Controllers\Controller;
use App\Jobs\OurBankDetailBalanceJob;
use App\Models\ApprovalWorkTimeline;
use App\Models\PlatForm;
use App\Models\deposit;
use App\Models\IncomeAndExpense;
use App\Models\OurBankDetail;
use App\Models\PlatformDetails;
use App\Models\User;
use App\Models\UserRegistration;
use App\Models\Withdraw;
use Aws\Rekognition\RekognitionClient;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\DataTables;

class DepositController extends Controller
{
    public function index()
    {

        // dd(UserRegistration::with('platformDetails', 'platformDetails.platform', 'platformDetails.deposit')->get());
        $user = auth()->user();
        $deposit = deposit::get();
        // dd($deposit);
        return view('deposit.all_deposit', compact('deposit'));
    }
    public function allDepositDatas()
    {

        $deposits = deposit::with('platformDetail.player', 'user', 'approvalTimeLine','ourBankDetail',  'approvalTimeLine.bankerUser',
        'approvalTimeLine.createdBy',
        'approvalTimeLine.adminUser',
        'approvalTimeLine.ccUser',)
            ->select([
                'deposits.id',
                'deposits.utr',
                'deposits.deposit_amount',
                'deposits.bonus',
                'deposits.image',
                'deposits.total_deposit_amount',
                'deposits.admin_status',
                'deposits.banker_status',
                'deposits.isInformed',
                'deposits.created_by',
                'deposits.platform_detail_id',
                'deposits.our_bank_detail_id',

            ])->orderBy('created_at', 'desc');

        // dd($deposits);
        return DataTables::of($deposits)
            ->addColumn('player_name', function ($deposit) {
                return $deposit->platformDetail->platform_username ?? '-';
            })
            ->addColumn('name', function ($deposit) {
                return $deposit->platformDetail->player->name ?? '-';
            })
            ->addColumn('deposit_bank', function ($deposit) {
                return $deposit->ourBankDetail->bank_name ?? '-';
            })
            ->addColumn('timeline', function ($deposit) {
                // dd($deposit->approvalTimeLine);

                return $deposit->approvalTimeLine ?? '-';
            })
            ->addColumn('created_by', function ($withdraw) {
                return $withdraw->user->name ?? '-';
            })
            ->addColumn('image', function ($deposit) {
                $imagePath = 'storage/' . $deposit->image;
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
            ->addColumn('actions', function ($deposit) {
                // Check if the user can delete the deposit
                if (Auth::user()->can('Deposit Delete', $deposit)) {
                    // Return the delete button HTML if the user has permission
                    return '<button class="action-btn delete-btn" data-id="' . $deposit->id . '"><i class="fa-solid fa-trash"></i></button>';
                } else {
                    // Optionally, return an empty string or a disabled button
                    return '';
                }
            })
            ->rawColumns(['image', 'actions'])

            ->make(true);
    }

    public function deleteItem($id)
    {
        // Find the item by its ID
        $item = deposit::find($id);
        // dd($item->platformDetail->platform_username);

        // dd("out");
        // Check if the item exists
        if (!$item) {
            // Handle the case where the item doesn't exist
            return response()->json(['message' => 'Item not found'], 404);
        }

        // Perform the deletion
        $item->forceDelete();
        if ($item->platformDetail->platform_username == null) {
            $item->platformDetail->forceDelete();
        }
        // You can return a success response if needed
        return response()->json(['message' => 'Item deleted successfully'], 200);
    }

    public function depositPending()
    {

        // dd(UserRegistration::with('platformDetails', 'platformDetails.platform', 'platformDetails.deposit')->get());
        $user = auth()->user();
        if ($user->roles[0]->hasPermissionTo('CC DPending') && $user->roles[0]->hasPermissionTo('CC WPending')) {
            $deposit = deposit::with('approvalTimeLine', 'platformDetail')->where('status', 'On Process')->get();
        } else {
            if ($user->roles[0]->hasPermissionTo('Deposit Banker Enable')) {
                $deposit = deposit::with('approvalTimeLine', 'platformDetail')->where('banker_status', 'pending')->get();
            }
            if ($user->roles[0]->hasPermissionTo('Deposit Admin Enable')) {
                $deposit = deposit::with('approvalTimeLine', 'platformDetail')->where('admin_status', 'pending')->get();
            }
        }

        // dd($deposit);
        return view('deposit.index', compact('deposit'));
    }
    public function depositPendingcc()
    {

        $user = auth()->user();
        $deposit = deposit::with('approvalTimeLine')->where('status', 'Completed')->where('isInformed', 0)->get();

        return view('deposit.cc', compact('deposit'));
    }

    public function depositCompleted()
    {
        // dd(UserRegistration::with('platformDetails', 'platformDetails.platform', 'platformDetails.deposit')->get());
        $user = auth()->user();

        $deposit = deposit::with('approvalTimeLine')->where('status', 'Completed')->where('isInformed', 0)->get();

        // dd($deposit);
        return view('payment.index', compact('deposit'));
    }

    public function admin_status(Request $request)
    {
        // dd($request);

        $auth_id = Auth::user()->id;
        $new_status = $request['selectedValue'];
        $userid = $request['userid'];
        $type = $request['type'];
        $deposit_type = $request['deposit_type'];
        $remark = $request['remark'];
        $bonus = $request['bonus'];
        $totalDepositAmount = $request['total_deposit_amount'];

        $platformId = $request['platform_id'];
        $userId = $request['userId'];

        $userPassword = $request['userPassword'];
        $admin_status = "";

        // $user = User::where('id', $userid)->update(['status' => $new_status]);

        $depositDetails=deposit::find($userid);
    if($depositDetails->assigned_to == $auth_id){

        if ($type == 'deposit_banker') {

            if ($new_status == "Verified") {
                $admin_status = "Pending";
                $deposit = deposit::where('id', $userid)->update([
                    'banker_status' => $new_status,
                    'admin_status' => $admin_status,
                    "assigned_to"=>null,

                ]);
            }
            if ($new_status == "Pending") {
                $admin_status = "Not Verified";
                $deposit = deposit::where('id', $userid)->update([
                    'banker_status' => $new_status,
                    'admin_status' => $admin_status,
                    'status'=>'On Process'
                ]);
                ApprovalWorkTimeline::where('deposit_id', $userid)->update([
                    'admin_id' =>null,
                    'admin_status_at' => null,
                    'cc_id'=>null,
                    'cc_status_at'=>null,
                    'stopped_at' => null,
                    'updated_by' => $auth_id,
                ]);
            }
            if ($new_status == "Rejected") {
                $admin_status = "Not Verified";
                $deposit = deposit::where('id', $userid)->update([
                    'banker_status' => $new_status,
                    'admin_status' => $admin_status,
                    'status' => "Completed",
                    'remark' => $remark,
                    'isInformed' => "0",
                    "assigned_to"=>null,

                ]);
            }
            //timeline
            $data = ApprovalWorkTimeline::where('deposit_id', $userid)->update([
                'banker_id' => $auth_id,
                'banker_status_at' => now()->toDateTimeString(),
                'updated_by' => $auth_id,
            ]);
        }
        if ($type == 'deposit_admin') {
            if ($new_status == "Verified") {
                if ($deposit_type == "Existing") {
                    $deposit = deposit::where('id', $userid)->update([
                        'admin_status' => $new_status,
                        'status' => "Completed",
                        'isInformed' => 0,
                        'remark' => $remark,
                        'bonus' => $bonus,
                        'total_deposit_amount' => $totalDepositAmount,
                        'updated_by' => $auth_id,
                        "assigned_to"=>null,


                    ]);
                } elseif ($deposit_type == "New Deposit") {

                    $deposit = deposit::where('id', $userid)->update([
                        'admin_status' => $new_status,
                        'status' => "Completed",
                        'isInformed' => 0,
                        'remark' => $remark,
                        'bonus' => $bonus,
                        'total_deposit_amount' => $totalDepositAmount,
                        'updated_by' => $auth_id,
                        "assigned_to"=>null,

                    ]);
                    $platform = PlatformDetails::where('id', $platformId)->update([
                        'platform_username' => $userId,
                        'platform_password' => $userPassword,
                        'status' => "Active"
                    ]);
                }
            }
            if ($new_status == "Rejected") {
                $deposit = deposit::where('id', $userid)->update([
                    'admin_status' => $new_status,
                    'status' => "Completed",
                    'remark' => $remark,
                    'isInformed' => "0",
                    "assigned_to"=>null,

                ]);
            }
            if ($new_status == "Pending") {
                $deposit = deposit::where('id', $userid)->update(['admin_status' => $new_status]);
            }
            //timeline
            ApprovalWorkTimeline::where('deposit_id', $userid)->update([
                'admin_id' => $auth_id,
                'admin_status_at' => now()->toDateTimeString(),
                'updated_by' => $auth_id,

            ]);
        }

        if ($type == 'deposit_cc') {
            $deposit = deposit::find($userid);
            if($deposit->status != 'On Process' && $deposit->banker_status != 'Pending') {

            $deposit->update([
                'isInformed' => 1,
                "assigned_to"=>null,

            ]);
              //timeline
              ApprovalWorkTimeline::where('deposit_id', $userid)->update([
                'cc_id' => $auth_id,
                'cc_status_at' => now()->toDateTimeString(),
                'stopped_at' => now()->toDateTimeString(),
                'updated_by' => $auth_id,
                'status' => 'Completed'
            ]);
        }
            // our bank balanc update
          if($deposit->admin_status=="Verified" && $deposit->banker_status=="Verified"){
            try {
                $jobRequest =[
                    'req_amount'=>$deposit->deposit_amount,
                    'type'=>'Deposit',
                    'operation'=>'Increase',
                    'our_bank_detail_id'=>$deposit->our_bank_detail_id,
                    'created_by'=>auth()->user()->id,
                ];
                OurBankDetailBalanceJob::dispatch($jobRequest );

                // return response()->json(['message' => 'Income request queued successfully']);
            } catch (\Exception $e) {
                return response()->json(['error' => 'Failed to queue Depost request: ' . $e->getMessage()], 500);
            }
              //timeline
              ApprovalWorkTimeline::where('deposit_id', $userid)->update([
                'cc_id' => $auth_id,
                'cc_status_at' => now()->toDateTimeString(),
                'stopped_at' => now()->toDateTimeString(),
                'updated_by' => $auth_id,
                'status' => 'Completed'
            ]);
        }

            // dd($deposit, $depositBank, $totalAmount, $data);
        }


        $result = [
            "flag" => 1,
            "status" => "Status Updated",
            "admin_status" => $admin_status
        ];

        return response()->json([($result)]);
    }else{
         $result=[
            "flag"=>0,
            "status"=>"status Not Updated",
        ];
        return response()->json([($result)]);
    }
    }

    public function platform_detail_active(Request $request)
    {
        $platform_detail = PlatformDetails::where('id', $request['platformDetailId'])->update([
            'status' => "Active"
        ]);

        $result = [
            "flag" => 1,
        ];

        return response()->json([($result)]);
    }
    public function check_platform_detail_exist(Request $request)
    {
        $deposit = deposit::where('id', $request['userid'])->first();
        $platform_detail = PlatformDetails::where('id', $deposit->platform_detail_id)->first();
        $result = [
            "flag" => 1,
            "platformDetail" => $platform_detail,
        ];

        return response()->json([($result)]);
    }




    public function get_user_info(Request $request, $userId)
    {

        $platform_detail = PlatformDetails::where('id', $userId)->first();
        $player = UserRegistration::where('id', $platform_detail->player_id)->first();
        // dd($player);

        return response()->json(['player' => $player]);
    }

    public function status_update(Request $request, $id)
    {

        $request->validate([
            'remark' => 'required',
            // You can customize the validation rules
        ]);

        $deposit = Deposit::find($id);

        // dd($deposit);

        $deposit->remark = $request->input('remark');

        $deposit->banker_status = 'Rejected';
        $deposit->status = 'Completed';
        $deposit->isInformed = 0;

        $deposit->save();

        return redirect()->route('deposit.index')->with('success', 'Deposit updated successfully.');
    }



    public function create()
    {
        $ourbank = OurBankDetail::where('type','Bank')->where('status', 1)->get();

        $data = UserRegistration::get();
        $platform = PlatForm::get();
        return view('deposit.create', compact('data', 'platform', 'ourbank'));
    }

    public function store(Request $request)
    {
        // dd($request->all());
        $auth_id = Auth::user()->id;
        // Get the current year and month
        $year = Carbon::now()->year;
        $month = Carbon::now()->format('m');
        $data = $request->validate([
            'user_id' => 'required',
            'platform' => 'required',
            'image'=>'required',
            'utr' => 'required|unique:deposits',
            'deposit_amount' => 'required',
            'bonus' => 'required',
            'total_deposit_amount' => 'required',
            'our_bank_detail' => 'required',
        ]);
        $user_id = $request->user_id;

        if ($request->input('platform_type') == "New Platform") {

            $playerIds = Withdraw::with('platformDetail')
                ->whereDate('created_at', Carbon::today())
                ->get()->pluck('platformDetail.player_id')->flatten()->unique();
            $matched = $playerIds->contains((string)$user_id);

            $platformDetails = [
                'player_id' => $request->input('user_id'),
                'platform_id' => $request->input('platform'),
                'status' => 'InActive'
            ];

            $platform_details = PlatformDetails::create($platformDetails);
            $depositData = [

                'platform_detail_id' => $platform_details->id,
                'utr' => $request->input('utr'),
                'our_bank_detail_id' => $request->input('our_bank_detail'),
                'deposit_amount' => $request->input('deposit_amount'),
                'is_bonus_eligible' => $matched ? 0 : 1,
                'bonus' => $request->input('bonus'),
                'total_deposit_amount' => $request->input('total_deposit_amount'),
                'admin_status' => "Not Verified",
                'banker_status' => "Pending",
                'status' => "On Process",
                'created_by' => $auth_id,
                'updated_by' => $auth_id
            ];

            if ($request->hasFile('image')) {
                // $imagePath = $request->file('image')->store("public/images/{$year}/{$month}");
                // $depositData['image'] = str_replace('public/', '', $imagePath);
                $imagePath = $request->file('image')->store('public/images');
                // Update the image path in the database without the 'public' prefix
                $depositData['image'] = str_replace('public/', '', $imagePath);
            }

            $deposit = deposit::create($depositData);
            ApprovalWorkTimeline::create([
                'type' => 'deposit',
                'deposit_id' => $deposit->id,
                'created_by' => $auth_id,
            ]);
        }
        if ($request->input('platform_type') == "Existing") {


            $platform_details_id = PlatformDetails::where('id', $request->input('user_id'))->where('platform_id', $request->input('platform'))->first();
            $playerIds = Withdraw::with('platformDetail')
                ->whereDate('created_at', Carbon::today())
                ->get()->pluck('platformDetail.player_id')->flatten()->unique();
            $matched = $playerIds->contains((string)$platform_details_id->player_id);


            $depositData = [
                'platform_detail_id' => $request->input('user_id'),
                'utr' => $request->input('utr'),
                'our_bank_detail_id' => $request->input('our_bank_detail'),
                'deposit_amount' => $request->input('deposit_amount'),
                'is_bonus_eligible' => $matched ? 0 : 1,
                'bonus' => $request->input('bonus'),
                'total_deposit_amount' => $request->input('total_deposit_amount'),
                'admin_status' => "Not Verified",
                'banker_status' => "Pending",
                'status' => "On Process",
                'created_by' => $auth_id,
                'updated_by' => $auth_id
            ];


            if ($request->hasFile('image')) {
                // $imagePath = $request->file('image')->store("public/images/{$year}/{$month}");
                // $depositData['image'] = str_replace('public/', '', $imagePath);
                $imagePath = $request->file('image')->store('public/images');
                // Update the image path in the database without the 'public' prefix
                $depositData['image'] = str_replace('public/', '', $imagePath);
            }

            $deposit = deposit::create($depositData);
            ApprovalWorkTimeline::create([
                'type' => 'deposit',
                'deposit_id' => $deposit->id,
                'created_by' => $auth_id,
            ]);
        }

        return redirect()->route('deposit.index');
    }

    public function edit($id)
    {

        $data = deposit::with('platformDetail.player')->find($id);
        // dd($data);
        $platform = PlatForm::all();
        return view('deposit.edit', compact('data', 'platform',));
    }

    public function update(Request $request, $id)
    {
        // Get the current year and month
        $year = Carbon::now()->year;
        $month = Carbon::now()->format('m');

        $data = $request->validate([
            'user_id' => 'required',
            'platform' => 'required',
            'utr' => 'required',
            'deposit_amount' => 'required',
            'bonus' => 'required',
            'total_deposit_amount' => 'required',
            // 'image' => 'image|mimes:jpeg,png,jpg,gif',
        ]);
        $deposit = deposit::findOrFail($id);
        $deposit->update($data);

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('public/images');
            // $imagePath = $request->file('image')->store("public/images/{$year}/{$month}");
            $deposit->image = str_replace('public/', '', $imagePath);

            // $depositData['image'] = str_replace('public/', '', $imagePath);
            $deposit->save();
        }

        return redirect()->route('deposit.index')->with('success', 'Deposit updated successfully');
    }

    public function delete(string $id)
    {
        $deposit = deposit::find($id);
        $deposit->delete();

        if ($deposit) {
            return redirect()->route('deposit.index')
                ->with('success', 'User deleted successfully');
        }

        return back()->with('failure', 'Please try again');
    }

    public function show($id)
    {

        $show = deposit::find($id);

        return view('deposit.show', compact('show'));
    }
    // existing platforms

    public function getMergedPlatforms($user)
    {

        $allPlatforms = Platform::pluck('name', 'id');
        $userPlatformIds = PlatformDetails::where('player_id', $user)->distinct()->pluck('platform_id');
        $mergedPlatforms = [];
        foreach ($userPlatformIds as $platformId) {
            $mergedPlatforms[$platformId] = $allPlatforms[$platformId];
        }

        return response()->json(['mergedPlatforms' => $mergedPlatforms]);
    }

    //Get Existing Platform Details
    public function getPlatformDetails(Request $request)
    {
        $searchTerm = $request->input('search');
        $selectedType = $request->input('selectedType');
        $from = $request->input('from');
        $platform_details = PlatformDetails::where('status', 'Active');
        // dd($selectedType);
        if ($selectedType == "Existing") {

            $platform_details = PlatformDetails::where('status', 'Active')->where('platform_username', 'like', '%' . $searchTerm . '%')
                ->get(['id', 'platform_username', 'player_id']);

            // Transform the data to match the desired JSON structure
            $results = $platform_details->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => $item->platform_username,
                ];
            });
        } elseif ($selectedType == "New Platform") {
            $platform_details = PlatformDetails::where('status', 'Active')->whereHas('player', function ($query) use ($searchTerm) {
                $query->where('name', 'like', '%' . $searchTerm . '%')
                    ->orWhere('mobile', 'like', '%' . $searchTerm . '%');
            })
                ->get(['id', 'platform_username', 'player_id']);

            // Transform the data to match the desired JSON structure
            $results = $platform_details->map(function ($item) {
                return [
                    'id' => $item->player_id,
                    'text' => $item->player->name . ' - ' . $item->player->mobile,
                ];
            });
        }
        if ($from == "Withdraw") {

            $platform_details = PlatformDetails::where('status', 'Active')->where('platform_username', 'like', '%' . $searchTerm . '%')
                ->get(['id', 'platform_username', 'player_id']);

            // Transform the data to match the desired JSON structure
            $results = $platform_details->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => $item->platform_username,
                ];
            });
        }

        $response = [
            'results' => $results,
            // You can adjust the "more" value as needed
        ];

        return response()->json($response);
        // return response()->json(['platformDetails' => $platform_details]);
    }

    //Get Existing Platform Details
    public function getPlatformUserDetails(Request $request)
    {
        $type = $request->input('0');
        if ($type == "Existing") {
            $platformDetailId = $request->input('1');
            $platformDetails = PlatformDetails::where('id', $platformDetailId)->with('player', 'platform')->first();
            return response()->json(['platformDetails' => $platformDetails]);
        } elseif ($type == "New Platform") {
            $userDetailId = $request->input('1');
            // $allPlatforms = Platform::pluck('name', 'id');
            // $userPlatformIds = PlatformDetails::where('player_id', $user)->distinct()->pluck('platform_id');
            // $mergedPlatforms = [];
            // foreach ($userPlatformIds as $platformId) {
            //     $mergedPlatforms[$platformId] = $allPlatforms[$platformId];

            // }
            $platform_details = PlatForm::all();
            return response()->json(['platformDetails' => $platform_details]);
        }
    }

    // new platforms

    public function getAllPlatforms()
    {
        $userDetails = UserRegistration::all();
        $allPlatforms = Platform::pluck('name', 'id');

        return response()->json([
            'allPlatforms' => $allPlatforms,
            'userDetails' => $userDetails,
        ]);
    }
    public function checkUtr(Request $request)
    {
        $utr = $request->input('utr');
        $deposit = deposit::where('utr', $utr)->exists();
        $income = IncomeAndExpense::where('ref_no', $utr)->exists();
        if ($deposit || $income) {
            $exists = true;
        } else {
            $exists = false;
        }
        return response()->json(['exists' => $exists]);
    }

    public function report()
    {
        $user = auth()->user();
        $deposit = deposit::get();
        $created_by = User::all();
        $platforms = PlatForm::all();
        return view('Report.deposit_report', compact('deposit', 'created_by', 'platforms'));
    }

    public function allDepositReports(Request $request)
    {
        $filters = $request->all();

        $deposits = DB::table('deposits')
            ->join('platform_details', 'deposits.platform_detail_id', '=', 'platform_details.id')
            ->join('our_bank_details', 'deposits.our_bank_detail_id', '=', 'our_bank_details.id')
            ->leftJoin('users', 'deposits.created_by', '=', 'users.id')
            ->select([
                'deposits.id',
                DB::raw('DATE(deposits.created_at) as created_at'),
                'deposits.utr',
                'platform_details.platform_username',
                'our_bank_details.bank_name',
                'deposits.deposit_amount',
                'deposits.bonus',
                'deposits.image',
                'deposits.total_deposit_amount',
                'deposits.admin_status',
                'deposits.banker_status',
                'deposits.isInformed',
                'users.name',
            ])
            ->when(isset($filters['start_date']), function ($query) use ($filters) {
                return $query->whereDate('deposits.created_at', '>=', $filters['start_date']);
            })
            ->when(isset($filters['end_date']), function ($query) use ($filters) {
                return $query->whereDate('deposits.created_at', '<=', $filters['end_date']);
            })
            ->when(isset($filters['created_by']), function ($query) use ($filters) {
                return $query->where('deposits.created_by', $filters['created_by']);
            })
            ->when(isset($filters['platform']), function ($query) use ($filters) {
                return $query->where('platform_details.platform_id', $filters['platform']);
            })
            ->where('admin_status', 'Verified')
            ->where('banker_status', 'Verified')
            ->where('isInformed', 1)
            ->orderBy('deposits.created_at', 'desc')
            ->get();


        $totalWithdrawAmount = Withdraw::when(isset($filters['start_date']), function ($query) use ($filters) {
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
            ->where('admin_status', 'Verified')
            ->where('banker_status', 'Verified')
            ->where('isInformed', 1)
            ->sum('amount');

        $totalWithdrawRecords = Withdraw::when(isset($filters['start_date']), function ($query) use ($filters) {
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
            ->where('admin_status', 'Verified')
            ->where('banker_status', 'Verified')
            ->where('isInformed', 1)
            ->count();

        $totalBonusAmount = 0;
        foreach ($deposits as $deposit) {
            $deposit_amount =  $deposit->deposit_amount;
            $deposit_bonus = $deposit->bonus;
            $bonus_amount = ($deposit_bonus / 100) * $deposit_amount;

            // Accumulate bonus amounts
            $totalBonusAmount += $bonus_amount;
        }


        // Calculate the total sum for the filtered data
        $totalDepositAmount = $deposits->sum('deposit_amount');
        $totalRecords = $deposits->count();
        $totalBonus = $deposits->sum('bonus');
        $totalTotalDepositAmount = $deposits->sum('total_deposit_amount');
        return DataTables::of($deposits)
            ->addColumn('player_name', function ($deposit) {
                return $deposit->platform_username ?? '-';
            })
            ->addColumn('created_by', function ($deposit) {
                return $deposit->name ?? '-';
            })
            ->addColumn('deposit_bank', function ($deposit) {
                return $deposit->bank_name?? '-';
            })
            ->addColumn('image', function ($deposit) {
                $imagePath = 'storage/' . $deposit->image;
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
                'totalDepositAmount' => $totalDepositAmount,
                'totalBonus' => $totalBonus,
                'totalTotalDepositAmount' => $totalTotalDepositAmount,
                'totalRecords' => $totalRecords,
                'totalBonusAmount' => $totalBonusAmount,
                'totalWithdrawAmount' => $totalWithdrawAmount,
                'totalWithdrawRecords' => $totalWithdrawRecords
            ])
            ->make(true);
    }

    public function exportAllSearchResults(Request $request)
    {
        try {
            $filters = $request->all();

            $deposits = DB::table('deposits')
                ->join('platform_details', 'deposits.platform_detail_id', '=', 'platform_details.id')
                ->join('user_registrations', 'platform_details.player_id', '=', 'user_registrations.id')
                ->join('our_bank_details', 'deposits.our_bank_detail_id', '=', 'our_bank_details.id')
                ->leftJoin('users', 'deposits.created_by', '=', 'users.id')
                ->select([
                    'deposits.id',
                    DB::raw('DATE(deposits.created_at) as created_at'),
                    'deposits.utr',
                    'platform_details.platform_username',
                    'our_bank_details.bank_name',
                    'deposits.deposit_amount',
                    'deposits.bonus',
                    'deposits.total_deposit_amount',
                    'deposits.admin_status',
                    'deposits.banker_status',
                    'deposits.isInformed',
                    'users.name',
                    'user_registrations.mobile'

                ])
                ->when(isset($filters['start_date']), function ($query) use ($filters) {
                    return $query->whereDate('deposits.created_at', '>=', $filters['start_date']);
                })
                ->when(isset($filters['end_date']), function ($query) use ($filters) {
                    return $query->whereDate('deposits.created_at', '<=', $filters['end_date']);
                })
                ->when(isset($filters['created_by']), function ($query) use ($filters) {
                    return $query->where('deposits.created_by', $filters['created_by']);
                })
                ->when(isset($filters['platform']), function ($query) use ($filters) {
                    return $query->where('platform_details.platform_id', $filters['platform']);
                })
                ->where('admin_status', 'Verified')
                ->where('banker_status', 'Verified')
                ->where('isInformed', 1)
                ->orderBy('deposits.created_at', 'desc')
                ->get();
            foreach ($deposits as $deposit) {
                $deposit->isInformed = $deposit->isInformed ? 'Yes' : 'No';
            }
            // dd($deposits);
            return Excel::download(new DepositExport($deposits), 'Deposit.xlsx');
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
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
    public function convert(Request $request)
    {
        $image = $request->file('image');

        $rekognition = new RekognitionClient([
            'version' => 'latest',
            'region'  => Config::get('services.rekognition.region'),
            'credentials' => [
                'key'    => Config::get('services.rekognition.key'),
                'secret' => Config::get('services.rekognition.secret'),
            ],
        ]);

        $result = $rekognition->detectText([
            'Image' => [
                'Bytes' => file_get_contents($image->path())
            ],
        ]);


        $detectedText = '';
        $amount = '';

        foreach ($result['TextDetections'] as $textDetection) {
            $detectedText .= $textDetection['DetectedText'] . PHP_EOL;
        }

        $matches1 = [];

        $patterns = array(
            '/Paid\s+to\s+\w+\.\s*(\d[\d,.]*)/', // Pattern for "Paid to" format
            '/R\s+(\d[\d,.]*)/', // Pattern for "R" format
            '/(?<!\S)\d[\d,.]*(?!\S)/', // Pattern for generic format
            '/Amount\s+(\d[\d,.]*)/', // Pattern for "Amount" format in Paytm screenshot
            '/To\s+\w+\s+\+?\d{2,3}\s+\d{5}\s+\d{5}\s+(\d[\d,.]*)/', // Pattern for amount following "To" name and mobile number
            '/(\d[\d,.]*)\s*Paid\s*to/', // Pattern for capturing the number before "Paid to"
        );

        // Loop through each pattern
        foreach ($patterns as $pattern) {
            // Try matching the pattern
            if (preg_match($pattern, $detectedText, $matches1)) {
                // Check if a match is found
                if (isset($matches1[1])) {
                    $amount = $matches1[1];
                    // Break the loop once amount is found
                    break;
                }
            }
        }

        // dd
        $matches = [];
        preg_match_all('/\b\d(?: *\d){11}\b/', $detectedText, $matches);


        $utr = array_unique($matches[0]);
        $cleanedString = str_replace(' ', '', $utr);
        return response()->json(['utr' => $cleanedString, 'amount' => $amount]);
    }
    public function assignedTo(Request $request){
        // dd($request->all());
        $deposit= deposit::find($request->depositId);
        if ($request->type == "Admin") {
            // dd('helo');
            if ($deposit->admin_status == 'Pending') {
                if ($deposit->assigned_to == auth()->user()->id) {

                    $deposit->update([
                        'assigned_to' => null,
                    ]);
                    return response()->json([
                        'status' => 200,
                        'action' => 'Assign Removed',
                    ]);
                } else if ($deposit->assigned_to != '' && $deposit->assigned_to != auth()->user()->id) {

                    return response()->json([
                        'status' => 400,
                        'action' => 'Assign To Someone',
                    ]);
                }
                if ($deposit->assigned_to == '') {
                    if ($request->selectedValue == 'true') {
                        $deposit->update([
                            'assigned_to' => auth()->user()->id,
                        ]);
                        return response()->json([
                            'status' => 200,
                            'action' => 'Assigned',
                        ]);
                    }
                }

            }else{
                return response()->json([
                    'status' => 400,
                    'action' => 'Not Assign',
                ]);
            }
        } elseif ($request->type == "Banker") {
            if ($deposit->banker_status == 'Pending') {
                if ($deposit->assigned_to == auth()->user()->id) {

                    $deposit->update([
                        'assigned_to' => null,
                    ]);
                    return response()->json([
                        'status' => 200,
                        'action' => 'Assign Removed',
                    ]);
                } else if ($deposit->assigned_to != '' && $deposit->assigned_to != auth()->user()->id) {

                    return response()->json([
                        'status' => 400,
                        'action' => 'Assign To Someone',
                    ]);
                }
                if ($deposit->assigned_to == '') {
                    if ($request->selectedValue == 'true') {
                        $deposit->update([
                            'assigned_to' => auth()->user()->id,
                        ]);
                        return response()->json([
                            'status' => 200,
                            'action' => 'Assigned',
                        ]);
                    }
                }

            }else{
                return response()->json([
                    'status' => 400,
                    'action' => 'Not Assign',
                ]);
            }
        }elseif ($request->type == "CC") {
            if ($deposit->isInformed == 0) {
                if ($deposit->assigned_to == auth()->user()->id) {

                    $deposit->update([
                        'assigned_to' => null,
                    ]);
                    return response()->json([
                        'status' => 200,
                        'action' => 'Assign Removed',
                    ]);
                } else if ($deposit->assigned_to != '' && $deposit->assigned_to != auth()->user()->id) {

                    return response()->json([
                        'status' => 400,
                        'action' => 'Assign To Someone',
                    ]);
                }
                if ($deposit->assigned_to == '') {
                    if ($request->selectedValue == 'true') {
                        $deposit->update([
                            'assigned_to' => auth()->user()->id,
                        ]);
                        return response()->json([
                            'status' => 200,
                            'action' => 'Assigned',
                        ]);
                    }
                }
            }else{
                return response()->json([
                    'status' => 400,
                    'action' => 'Not Assign',
                ]);
            }
        }
    }




}
