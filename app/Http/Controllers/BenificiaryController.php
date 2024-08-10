<?php

namespace App\Http\Controllers;

use App\Models\bank_detail;
use App\Models\Benificiary;
use App\Models\OurBankDetail;
use App\Models\PlatForm;
use App\Models\User;
use App\Models\UserRegistration;
use App\Models\Withdraw;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class BenificiaryController extends Controller
{
    public function index()
    {
        // $ourbank = OurBankDetail::where('status', 1)->get();

        $created_by = User::all();
        // $platforms = PlatForm::all();
        $ourbank = OurBankDetail::where('type','Bank')->where('status', 1)->get();


        return view('benificiary.index')->with([
            // 'withdrawal' => $withdrawal,
            // 'withdrawal_count' => $withdrawCount,
            // 'total_amount' => $totalAmount,
            'ourbank'=>$ourbank,
            'created_by' => $created_by,
        ]);
    }


    public function benificiaryReport(Request $request)
    {
        $filters = $request->all();
        // dd($request->all());
        $userDeatilsQuery = bank_detail::query()
            ->with(['player', 'benificiary'])

            ->when(isset($filters['start_date']) && isset($filters['end_date']), function ($query) use ($filters) {
                return $query->whereHas('benificiary', function ($query) use ($filters) {
                    // Convert start_date to MySQL date format (Y-m-d)
                    $startDate = date('Y-m-d', strtotime($filters['start_date']));
                    $endDate = date('Y-m-d', strtotime($filters['end_date']));
                    $query->whereBetween(DB::raw('DATE(created_at)'), [$startDate, $endDate]);
                });
            })
            ->when(isset($filters['created_by']), function ($query) use ($filters) {
                // return $query->where('platform_details.platform_id', $filters['platform']);
                return $query->whereHas('benificiary', function ($query) use ($filters) {
                    $query->where('created_by', $filters['created_by']);
                });
            })
            ->when(isset($filters['bank']), function ($query) use ($filters) {
                // return $query->where('platform_details.platform_id', $filters['platform']);
                return $query->whereHas('benificiary', function ($query) use ($filters) {
                    $query->where('our_bank_details_id', $filters['bank']);
                });
            })

            ->when(isset($filters['pending_banks']), function ($query) use ($filters) {
                // return $query->where('platform_details.platform_id', $filters['platform']);
                return $query->whereDoesntHave('benificiary', function ($query) use ($filters) {
                    $query->where('our_bank_details_id', $filters['pending_banks']);
                });
            });



        $userDeatils = $userDeatilsQuery->orderBy('created_at', 'desc')->get();
        // dd($userDeatils);


        $activeBeneficiariesCountTotal=0;
        $inactiveBeneficiariesCountTotal=0;

            foreach ($userDeatils as $bankDetail) {
                $activeBeneficiariesCount = $bankDetail->benificiary()->whereHas('ourBankDetail', function ($query) {
                    $query->where('status', 1);
                })->count();

                $inactiveBeneficiariesCount = $bankDetail->benificiary()->whereHas('ourBankDetail', function ($query) {
                    $query->where('status', 0);
                })->count();
                $activeBeneficiariesCountTotal +=$activeBeneficiariesCount;
                $inactiveBeneficiariesCountTotal +=$inactiveBeneficiariesCount;

            }
            // dd($allActive,$allInactive);

        return DataTables::of($userDeatils)
        // ->orderColumn('created_at', 'DESC')
            ->addColumn('user_name', function ($userDeatils) {
                return $userDeatils->player->name ?? '-';
            })
            ->addColumn('created_at', function ($income) {
                $carbonDate = Carbon::parse($income->created_at);
                return $carbonDate->format('Y F j') ?? '-';
            })
            ->addColumn('ac_no', function ($userDeatils) {
                return $userDeatils->account_number ?? '-';
            })
            ->addColumn('ifsc', function ($userDeatils) {
                return $userDeatils->ifsc_code ?? '-';
            })
            ->addColumn('bank_name', function ($userDeatils) {
                return $userDeatils->bank_name ?? '-';
            })
            ->addColumn('benificiary', function ($userDeatils) {
                return '<a  data-bs-toggle="modal" data-bs-target="#exampleModal" class="exampleModal"><span class="badge bg-success">+ benificiary</span>
              </a>';
            })
// ->addColumn('Pending Banks',function ($userDeatils) {
//     return '<i class="fa-solid fa-eye"></i>';
// })
            ->addColumn('action', function ($userDeatils) {
                return '<i class="fa-solid fa-eye"></i>';
            })
            ->with([
                'active_benificiary_count'=>$activeBeneficiariesCountTotal,
                'inactive_benificiary_count'=>$inactiveBeneficiariesCountTotal,
            ])
            ->rawColumns(['action', 'benificiary'])

            ->make(true);
    }
    public function benificiaryUpdate(Request $request)
    {
       $exist= Benificiary::where('bank_detail_id',$request->bank_details_id)->where('our_bank_details_id',$request->our_bank_detail_id)->first();
       if(!$exist){
           Benificiary::create([
            'bank_detail_id' => $request->bank_details_id,
            'our_bank_details_id' => $request->our_bank_detail_id,
            'created_by' => auth()->user()->id,
           ]);
        }
        return response()->json(['status' => 'success', 'status_code' => 200]);
    }
    public function benificiaryShow(Request $request)
    {
        // dd($request->all());
        $bankDetailId = $request->input('bank_details_id');
        // dd()

        // Fetch beneficiary data based on bank detail ID
        $bank_detail = bank_detail::with('benificiary.ourBankDetail', 'benificiary.createdBy')->find($bankDetailId);
        // dd($bank_detail);

        return response()->json(['beneficiaries' => $bank_detail]);
    }
    public function benificiaryBanksFetch(Request $request)
    {
        // Retrieve existing bank IDs
        $bankDetailId = $request->input('bank_details_id');
        $bank_detail = bank_detail::with('benificiary')->find($bankDetailId);
        $existBanks = $bank_detail->benificiary->pluck('our_bank_details_id')->toArray();

        // Query the balance for other bank IDs
        $otherBanks = OurBankDetail::whereNotIn('id', $existBanks)->where('status', 1)->get();
        $otherBanksCount=$otherBanks->Count();
        return response()->json([
            'banks' => $otherBanks,
            'otherBanksCount'=>$otherBanksCount
        ]);
    }
}
