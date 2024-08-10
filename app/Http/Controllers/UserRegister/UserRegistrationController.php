<?php

namespace App\Http\Controllers\UserRegister;

use App\Http\Controllers\Controller;
use App\Models\ApprovalWorkTimeline;
use App\Models\LeadSource;
use App\Models\PlatForm;
use App\Models\UserRegistration;
use App\Models\bank_detail;
use App\Models\Branch;
use App\Models\deposit;
use App\Models\OurBankDetail;
use App\Models\PlatformDetails;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;
use GuzzleHttp\Client;


class UserRegistrationController extends Controller
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
        // $userRegistration = UserRegistration::take(250)->get();
        return view('UserRegister.index');
    }
    public function allPlayersData()
    {

        $players = UserRegistration::with('platformDetails')
            ->select(['user_registrations.id', 'user_registrations.name', 'user_registrations.mobile'])
            ->leftJoin('platform_details', 'user_registrations.id', '=', 'platform_details.player_id');


        return DataTables::of($players)
            ->addColumn('platformNames', function ($player) {
                return $player->platformDetails->pluck('platform_username')->implode(', ');
            })
            ->filterColumn('platformNames', function ($query, $keyword) {
                $query->whereRaw("platform_details.platform_username like ?", ["%$keyword%"]);
            })
            ->filterColumn('mobile', function ($query, $keyword) {
                $query->where('user_registrations.mobile', 'like', "%$keyword%");
            })
            ->make(true);
    }


    public function platform_selected(Request $request)
    {
        $platform_id = $request['selectedPlatform'];
        $platform_details = PlatformDetails::where('platform_id', $platform_id)->with('player')->get();
        $players = $platform_details->pluck('player.name', 'player.id', 'player.email', 'player.dob');
        return response()->json(['players' => $players]);
    }

    public function getUserDetails($userId)
    {
        $user = UserRegistration::where('id', $userId)->with('user')->first();
        return response()->json(['user' => $user]);
    }
    public function create()
    {
        $bankdetail = OurBankDetail::where('type','Bank')->where('status',1)->get();
        $data = Branch::get();
        $lead_source = LeadSource::get();
        $platform = PlatForm::get();
        return view('UserRegister.create', compact('data', 'lead_source', 'platform', 'bankdetail'));
    }

    public function store(Request $request)
    {
// dd($request->all());
        $auth_id = Auth::user()->id;
        $year = Carbon::now()->year;
        $month = Carbon::now()->format('m');

        $request->validate([
            'branch_name' => 'required',
            'our_bank_name' => 'required',
            'name' => 'required',
            'mobile' => 'required|integer|unique:user_registrations',
            'alternative_mobile'=>'nullable|integer|unique:user_registrations',
            'platform' => 'required',
            'utr' => 'required|unique:deposits',
            'deposit_amount' => 'required',
            'bonus' => 'required',
            'total_deposit_amount' => 'required',
        ],[

            'mobile.integer' => 'The mobile field must be an Number Only.',
            'alternative_mobile.integer' => 'The alternative mobile field must be an Number Only.',
            'alternative_mobile.unique' => 'The alternative mobile has already been taken..',

        ]);
        if ($request->input('lead_source') == null) {
            $lead_source = 1;
        } else {
            $lead_source = $request->input('lead_source');
        }

// dd('hello');
        $userRegistration = UserRegistration::create([
            'branch_id' => $request->input('branch_name'),
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'mobile' => $request->input('mobile'),
            'alternative_mobile' => $request->input('alternative_mobile'),
            'dob' => $request->input('dob'),
            'location' => $request->input('location'),
            'lead_source_id' => $lead_source,
            'created_by' => $auth_id,
            'updated_by' => $auth_id,
        ]);
// dd($userRegistration);

        $platformDetails = [
            'player_id' => $userRegistration->id,
            'platform_id' => $request->input('platform'),
            'status' => 'InActive'
        ];

        $platform_details = PlatformDetails::create($platformDetails);

        // dd($userRegistration);
        // $platform_details_id =  $platform_details->id;
        $accountNames = $request->input('account_name');
        $accountNumbers = $request->input('account_number');
        $ifscCodes = $request->input('ifsc_code');
        $bankNames = $request->input('bank_name');

        foreach ($accountNames as $key => $accountName) {
            if (!empty($accountName)) {
                $data = [

                    'player_id' => $userRegistration->id,
                    'account_name' => $accountNames[$key],
                    'account_number' => $accountNumbers[$key],
                    'ifsc_code' => $ifscCodes[$key],
                    'bank_name' => $bankNames[$key],
                ];

                // Use updateOrInsert to handle duplicates by account_number
                bank_detail::updateOrInsert(
                    ['account_number' => $accountNumbers[$key]],
                    $data
                );
            }
        }



        // dd('success');

        $depositData = [
            'platform_detail_id' => $platform_details->id,
            'our_bank_detail_id' => $request->input('our_bank_name'),
            'utr' => $request->input('utr'),
            'deposit_amount' => $request->input('deposit_amount'),
            'is_bonus_eligible' => 1,
            'bonus' => $request->input('bonus'),
            'total_deposit_amount' => $request->input('total_deposit_amount'),
            'admin_status' => "Not Verified",
            'banker_status' => "Pending",
            'status' => "On Process",
            'created_by' => $auth_id,
            'updated_by' => $auth_id,
        ];

        if ($request->hasFile('image')) {
            // $imagePath = $request->file('image')->store("public/images/{$year}/{$month}");
            // $deposit->image = str_replace('public/', '', $imagePath);
            $imagePath = $request->file('image')->store('public/images');
            // // Update the image path in the database without the 'public' prefix
            $depositData['image'] = str_replace('public/', '', $imagePath);
        }

        $deposit = deposit::create($depositData);
        ApprovalWorkTimeline::create([
            'type' => 'deposit',
            'deposit_id' => $deposit->id,
            'created_by' => $auth_id,
        ]);
        session()->flash('success', 'User successfully created.');

        return redirect()->route('UserRegister.index');
    }

    public function edit($id)
    {

        $data = UserRegistration::find($id);

        $platform_details = PlatformDetails::where('player_id', $data->id)->get();
        $bankDetails = bank_detail::where('player_id', $data->id)->get();
        $users = Branch::all();
        $lead_source = LeadSource::all();
        $platform = PlatForm::all();
        return view('UserRegister.edit', compact('data', 'lead_source', 'platform', 'users', 'bankDetails', 'platform_details'));
    }

    public function editPlatformDetails(Request $request)
    {

        $seletedPlatform = $request->input('selectedValue');
        $playerId = $request->input('playerId');
        $platformDetails = PlatformDetails::where('player_id', $playerId)->where('platform_id', $seletedPlatform)->first();

        $result = [
            "platformUsername" => $platformDetails->platform_username,
            "platformPassword" => $platformDetails->platform_password,
        ];

        return response()->json([($result)]);
    }

    public function update(Request $request, $id)
    {
        $platform_id = $request->input('selected_platform');
        if ($platform_id != null) {
            $platform_details = PlatformDetails::where('player_id', $id)->where('platform_id', $platform_id)->first();
            $platform_details->update([
                'platform_username' => $request->input('p_username'),
                'platform_password' => $request->input('p_password')
            ]);
        }

        $data = $request->validate([
            'user_name' => 'required',
            'name' => 'required',
            'lead_source' => 'required',
        ]);
        $userRegistration = UserRegistration::findOrFail($id);
        $bankDetails=bank_detail::where('player_id',$id)->first();
        if($userRegistration->name !=$request->input('name') && $bankDetails && $bankDetails->r_pay_contact_id || $userRegistration->mobile !=$request->input('mobile') && $bankDetails && $bankDetails->r_pay_contact_id )
        {

            try {

                $response = $this->client->request('PATCH', "contacts/{$userRegistration->r_pay_contact_id}");

                $body = $response->getBody();

                $contactDetails = json_decode($body, true);

            } catch (\Exception $e) {

                return response()->json(['error' => $e->getMessage()], 500);
            }
        }

        $userRegistration->update([
            'branch_id' => $request->input('user_name'),
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'alternative_mobile' => $request->input('alternative_mobile'),
            'dob' => $request->input('dob'),
            'location' => $request->input('location'),
            'lead_source_id' => $request->input('lead_source'),
        ]);

        $platformdetails = PlatformDetails::where('player_id', $userRegistration->id)->first();

        $accountNames = $request->input('account_name');
        $accountNumbers = $request->input('account_number');
        $ifscCodes = $request->input('ifsc_code');
        $bankNames = $request->input('bank_name');

        // $data='';
        foreach ($accountNames as $key => $accountName) {
            if (!empty($accountName)) {
                $data = [
                    'player_id' => $userRegistration->id,
                    'account_name' => $accountNames[$key],
                    'account_number' => $accountNumbers[$key],
                    'ifsc_code' => $ifscCodes[$key],
                    'bank_name' => $bankNames[$key],
                    // 'r_pay_fund_account_id'=>null,
                ];
                //for razorpay. user has razorpays contact id means check bank deatils other wise no need
                if(!empty($userRegistration->r_pay_contact_id)){
                    $existingBankDetail = bank_detail::where('player_id', $userRegistration->id)
                    ->where('account_name', $accountNames[$key])
                    ->first();
                    if ($existingBankDetail) {
                        $detailsChanged = (
                            $existingBankDetail->account_number !== $accountNumbers[$key] ||
                            $existingBankDetail->ifsc_code !== $ifscCodes[$key] ||
                            $existingBankDetail->bank_name !== $bankNames[$key]
                        );
                        if ($detailsChanged) {
                            $existingBankDetail->update(array_merge($data, ['r_pay_fund_account_id' => null]));
                        }
                    }else{
                        bank_detail::create($data);
                    }
                }else{
                    bank_detail::updateOrCreate(
                        ['player_id' => $userRegistration->id, 'account_name' => $accountNames[$key]],
                        $data
                    );
                }

            }
        }

        return redirect()->route('UserRegister.index');
    }
    public function delete(string $id)
    {

        $user = UserRegistration::find($id);
        $user->delete();

        if ($user) {
            return redirect()->route('UserRegister.index')
                ->with('success', 'User deleted successfully');
        }

        return back()->with('failure', 'Please try again');
    }
}
