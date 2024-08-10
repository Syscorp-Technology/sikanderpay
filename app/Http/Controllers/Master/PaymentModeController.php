<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\PaymentMode;
use Faker\Provider\ar_EG\Payment;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PaymentModeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $payments = PaymentMode::get();
        return view('Master.payment-modes.index', compact('payments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('Master.payment-modes.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'payment_mode_name' => [
                'required', 'min:2', 'max:50', Rule::unique('payment_modes')
            ],
        ]);
        PaymentMode::create([
            'payment_mode_name' => $request->payment_mode_name,
            'status' => $request->status,
            'created_by' => auth()->user()->id,
        ]);
        return redirect(route('payment-mode.index'))->with('success', 'Payment Mode was Created sucessfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $paymentMode = PaymentMode::find($id);

        return view('Master.payment-modes.edit', compact('paymentMode'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // dd($request->all());
        $request->validate([
            'payment_mode_name' => [
                'required', 'min:2', 'max:50', Rule::unique('payment_modes')->ignore($id)
            ],
        ]);
        PaymentMode::where('id', $id)->update([
            'payment_mode_name' => $request->payment_mode_name,
            'status' => $request->status,
            'updated_by' => auth()->user()->id,
        ]);
        return redirect(route('payment-mode.index'))->with('success', 'Payment Mode was updated sucessfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
