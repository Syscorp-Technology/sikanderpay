<?php

namespace App\Http\Controllers;

use App\Models\deposit;
use App\Models\GatewayCategory;
use App\Models\IncomeAndExpense;
use App\Models\InternalTransfer;
use App\Models\OurBankDetail;
use App\Models\Withdraw;
use App\Models\WithdrawUtr;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;


class OurBankDetailController extends Controller
{
    public function index()
    {
        $bankdetail = OurBankDetail::get();
        $activeBanksAmount = $bankdetail->where('status', 1)->sum('amount');
        $tempBanksAmount = $bankdetail->where('status', 2)->sum('amount');
        $inactiveBanksAmount = $bankdetail->where('status', 0)->sum('amount');
        // dd($inactiveBanks, $activeBanks);
        $totalActiveBankBalance=$activeBanksAmount+$tempBanksAmount;
        return view('Master.ourbankdetail.index', compact('bankdetail','inactiveBanksAmount','totalActiveBankBalance'));
    }

    public function create()
    {
        $gatewayCategory=GatewayCategory::where('status',1)->get();
        return view('Master.ourbankdetail.create',compact('gatewayCategory'));
    }

    public function store(Request $request)
    {

        // dd($request->all());
        $request->validate([
            'type'=>'required',
            'bank_name' => 'required',
            'account_number' => 'required',
            'ifsc' => 'required',
            'amount' => 'required',
            'limit' => 'required',
            'remarks' => 'required',

        ]);

        $input = [
            'type'=>$request['type'],
            'category'=>$request['category'],
            'bank_name' => $request['bank_name'],
            'account_number' => $request['account_number'],
            'ifsc' => $request['ifsc'],
            'amount' => $request['amount'],
            'limit' => $request['limit'],
            'remarks' => $request['remarks'],
            'status' => $request['status'],

        ];
        OurBankDetail::create($input);
        session()->flash('success', 'Data has been successfully stored.');
        return redirect()->route('ourbankdetail.index');
    }

    public function edit($id)
    {
        $frame = OurBankDetail::find($id);
        $gatewayCategory=GatewayCategory::where('status',1)->get();

        return view('Master.ourbankdetail.edit', compact('frame','gatewayCategory'));
    }

    public function update(Request $request, $id)
    {
        // dd($request->all());
        $request->validate([
            'bank_name' => 'required',
            'type'=>'required',
            'account_number' => 'required',
            'amount' => 'required',
            'limit' => 'required',
            'ifsc' => 'required',

        ]);

        $record = OurBankDetail::find($id);

        if (!$record) {
            return redirect()->route('ourbankdetail.index')->with('error', 'Record not found');
        }
        $record->type = $request->input('type');
        $record->category = $request->input('category');
        $record->bank_name = $request->input('bank_name');
        $record->account_number = $request->input('account_number');
        $record->ifsc = $request->input('ifsc');

        $record->amount = $request->input('amount');
        $record->limit = $request->input('limit');
        $record->status = $request->input('status');
        $record->remarks = $request->input('remarks');
        $record->save();

        return redirect()->route('ourbankdetail.index')->with('success', 'Record updated successfully');
    }


    public function delete($id)
    {
        $a = OurBankDetail::find($id);

        $a->delete();
        session()->flash('success', 'Data has been successfully Deleted.');
        return redirect()->route('ourbankdetail.index');
    }
    public function ourBankIncome($id)
    {
        $bankId = $id;
        $bankDetail = OurBankDetail::find($bankId);
        // $incomeDetails = IncomeAndExpense::where('our_bank_detail_id', $bankId)->where('type', 'income')->where('status', 1)->get();
        // dd($incomeDetails);
        return view('ourBankBalanc.income', compact('bankId', 'bankDetail'));
    }

    public function ourBankAllIncome(Request $request)
    {
        // dd($request->all());
        $bankId = $request->bankId;
        // dd($bankId);
        $incomeQuery = IncomeAndExpense::with(['category', 'paymentMode', 'createdBy', 'ourBankDetail'])
            ->where('our_bank_detail_id', $bankId)
            ->where('type', 'income')
            ->where('banker_status','Verified')
            ->where('status', 1);


        $income = $incomeQuery->orderBy('created_at', 'desc')->get();
        // dd($income);

        return DataTables::of($income)
            ->addColumn('category_name', function ($income) {
                return $income->category->category_name ?? '-';
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
                    return $imageUrl;
                } else {
                    return 'No Image';
                }
            })
            ->addColumn('date', function ($income) {
                $carbonDate = Carbon::parse($income->date);
                return $carbonDate->format('Y F j') ?? '-';
            })
            ->addColumn('createdBy', function ($income) {
                return $income->createdBy->name ?? '-';
            })
            ->rawColumns(['image'])
            ->make(true);
    }

    public function ourBankExpense($id)
    {

        $bankId = $id;
        $bankDetail = OurBankDetail::find($bankId);

        return view('ourBankBalanc.expense', compact('bankId', 'bankDetail'));
    }

    public function ourBankAllExpense(Request $request)
    {

        $bankId = $request->bankId;
        $expenseQuery = IncomeAndExpense::with(['expenseCategory', 'paymentMode', 'createdBy', 'ourBankDetail'])
            ->where('our_bank_detail_id', $bankId)
            ->where('type', 'expense')
            ->where('financier_status','Verified')
            ->where('operation_head_status','Verified')
            ->where('superviser_status','Verified')
            ->where('banker_status', 'Verified')
            ->where('status', 1);

        $expense = $expenseQuery->orderBy('created_at', 'desc')->get();

        return DataTables::of($expense)
            ->addColumn('expense_category', function ($expense) {
                return $expense->expenseCategory->expense_category_name ?? '-';
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
                    return $imageUrl;
                } else {
                    return 'No Image';
                }
            })
            ->addColumn('date', function ($expense) {
                $carbonDate = Carbon::parse($expense->date);
                return $carbonDate->format('Y F j') ?? '-';
            })
            ->addColumn('createdBy', function ($expense) {
                return $expense->createdBy->name ?? '-';
            })

            ->rawColumns(['image'])
            ->make(true);
    }


    public function ourBankInternalTransferSender($id)
    {
        $bankId = $id;
        $bankDetail = OurBankDetail::find($bankId);
        return view('ourBankBalanc.internalTransferFrom', compact('bankId', 'bankDetail'));
    }

    public function allInternalTransferSenderReports(Request $request)
    {
        $bankId = $request->bankId;

        $internalTransferQuery = InternalTransfer::with(['bankFrom', 'bankTo', 'createdBy'])
            ->where('bank_from', $bankId)
            ->where('superviser_status', 'Verified')
            ->where('banker_status', 'Verified')
            ->where('status', 1);

        $internalTransfer = $internalTransferQuery->orderBy('created_at', 'desc')->get();

        return DataTables::of($internalTransfer)
            ->addColumn('bankFrom', function ($internalTransfer) {
                return $internalTransfer->bankFrom->bank_name ?? '-';
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
             ->addColumn('date', function ($internalTransfer) {
                $carbonDate = Carbon::parse($internalTransfer->date);
                return $carbonDate->format('Y F j') ?? '-';
            })
            ->addColumn('createdBy', function ($internalTransfer) {
                return $internalTransfer->createdBy->name ?? '-';
            })

            ->rawColumns(['image'])
            ->make(true);
    }


    public function ourBankInternalTransferReciver($id)
    {
        $bankId = $id;
        $bankDetail = OurBankDetail::find($bankId);

        return view('ourBankBalanc.internalTransferTo', compact('bankId', 'bankDetail'));
    }


    public function allInternalTransferReciverReports(Request $request)
    {
        $bankId = $request->bankId;

        $internalTransferQuery = InternalTransfer::with(['bankFrom', 'bankTo', 'createdBy'])
            ->where('bank_to', $bankId)
            ->where('superviser_status', 'Verified')
            ->where('banker_status', 'Verified')
            ->where('status', 1);

        $internalTransfer = $internalTransferQuery->orderBy('created_at', 'desc')->get();

        return DataTables::of($internalTransfer)
            ->addColumn('bankFrom', function ($internalTransfer) {
                return $internalTransfer->bankFrom->bank_name ?? '-';
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
            ->addColumn('date', function ($internalTransfer) {
                $carbonDate = Carbon::parse($internalTransfer->date);
                return $carbonDate->format('Y F j') ?? '-';
            })
            ->addColumn('createdBy', function ($internalTransfer) {
                return $internalTransfer->createdBy->name ?? '-';
            })

            ->rawColumns(['image'])
            ->make(true);
    }


    public function ourBankDeposit($id)
    {
        $bankId = $id;
        $bankDetail = OurBankDetail::find($bankId);
        return view('ourBankBalanc.deposit', compact('bankId', 'bankDetail'));
    }

    public function ourBankAllDeposit(Request $request)
    {
        $bankId = $request->bankId;

        $deposits = deposit::with('platformDetail.player', 'user', 'approvalTimeLine')
            ->where('our_bank_detail_id', $bankId)
            ->where('admin_status', 'Verified')
            ->where('banker_status', 'Verified')
            ->where('isInformed', 1)
            ->orderBy('created_at', 'desc')
            ->get();

        return DataTables::of($deposits)
            ->addColumn('player_name', function ($deposit) {
                return $deposit->platformDetail->platform_username ?? '-';
            })
            ->addColumn('name', function ($deposit) {
                return $deposit->platformDetail->player->name ?? '-';
            })
            ->addColumn('created_at',function($deposit){
                return $deposit->created_at->format('Y F j');
                // $carbonDate = Carbon::parse($deposit->created_at);
                // return $carbonDate->format('Y F j') ?? '-';
            })
            ->addColumn('created_by', function ($withdraw) {
                return $withdraw->user->name ?? '-';
            })
            ->addColumn('image', function ($deposit) {
                $imagePath = 'storage/' . $deposit->image;
                $imageUrl = asset($imagePath);

                if (file_exists(public_path($imagePath))) {
                    return $imageUrl;
                } else {
                    return 'No Image';
                }
            })

            ->rawColumns(['image'])

            ->make(true);
    }
    public function ourBankWithdraw($id)
    {
        $bankId = $id;
        $bankDetail = OurBankDetail::find($bankId);
        return view('ourBankBalanc.withdraw', compact('bankId', 'bankDetail'));
    }
    public function ourBankAllWithdraw(Request $request)
    {
        $bankId = $request->bankId;
        $withdrawids = WithdrawUtr::where('our_bank_detail', $bankId)->pluck('withdraw_id');

        $withdraw = Withdraw::with('platformDetail.player', 'bank', 'employee', 'withdrawUtr', 'approvalTimeLine')
            ->whereIn('id', $withdrawids)
            ->where('isInformed', 1)
            ->where('admin_status', 'Verified')
            ->where('banker_status', 'Verified')
            ->orderBy('created_at', 'desc')
            ->get();

        return DataTables::of($withdraw)
            ->addColumn('player_name', function ($withdraw) {
                return $withdraw->platformDetail->platform_username ?? '-';
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
            ->addColumn('created_at',function($withdraw){
                return $withdraw->created_at->format('Y F j');
            })
            ->addColumn('created_by', function ($withdraw) {
                return $withdraw->employee->name ?? '-';
            })

            ->addColumn('image', function ($withdraw) {
                $imagePath = 'storage/' . $withdraw->image;
                $imageUrl = asset($imagePath);

                if (file_exists(public_path($imagePath))) {
                    return $imageUrl;
                } else {
                    return 'No Image';
                }
            })
            ->rawColumns(['image'])

            ->make(true);
    }
}
