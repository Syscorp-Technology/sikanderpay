<?php

namespace App\Jobs;

use App\Models\OurBankDetail;
use App\Models\OurBankDetailRecord;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class OurBankDetailBalanceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $jobRequest;
    /**
     * Create a new job instance.
     */
    public function __construct($jobRequest)
    {
        $this->jobRequest  = $jobRequest;

        // dd($this->jobRequest);

    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // dd($this->jobRequest);
        //increase opeation
        if ($this->jobRequest['operation'] == 'Increase') {
            $ourBankDetail = OurBankDetail::where('id', $this->jobRequest['our_bank_detail_id'])->lockForUpdate()->first();
            // dd($ourBankDetail);
            $data = [
                'our_bank_detail_id' => $this->jobRequest['our_bank_detail_id'],
                'type' => $this->jobRequest['type'],
                'operation' => $this->jobRequest['operation'],
                'bank_amount' => $ourBankDetail['amount'],
                'req_amount' => $this->jobRequest['req_amount'],
                'updated_amount' => ($ourBankDetail->amount) + ($this->jobRequest['req_amount']),
                'created_by' => $this->jobRequest['created_by'],
            ];
            //  dd($data);
            $ourBankDetailRecord = OurBankDetailRecord::create($data);
            $ourBankDetail->update([
                'amount' =>  $ourBankDetailRecord->updated_amount,
            ]);
            //decrease opeation
        } elseif ($this->jobRequest['operation'] == 'Decrease') {
            $ourBankDetail = OurBankDetail::where('id', $this->jobRequest['our_bank_detail_id'])->lockForUpdate()->first();
            // dd($ourBankDetail);
            $data = [
                'our_bank_detail_id' => $this->jobRequest['our_bank_detail_id'],
                'type' => $this->jobRequest['type'],
                'operation' => $this->jobRequest['operation'],
                'bank_amount' => $ourBankDetail['amount'],
                'req_amount' => $this->jobRequest['req_amount'],
                'updated_amount' => ($ourBankDetail->amount) - ($this->jobRequest['req_amount']),
                'created_by' => $this->jobRequest['created_by'],
            ];
            //  dd($data);

            $ourBankDetailRecord = OurBankDetailRecord::create($data);
            $ourBankDetail->update([
                'amount' =>  $ourBankDetailRecord->updated_amount,
            ]);
            //internal Bank operion
        } elseif ($this->jobRequest['operation'] == 'Both') {
            $fromBank = OurBankDetail::where('id', $this->jobRequest['from_bank'])->lockForUpdate()->first();
            $toBank = OurBankDetail::where('id', $this->jobRequest['to_bank'])->lockForUpdate()->first();
            $fromData = [
                'our_bank_detail_id' => $this->jobRequest['from_bank'],
                'type' => $this->jobRequest['type'],
                'operation' => $this->jobRequest['operation'],
                'bank_amount' => $fromBank['amount'],
                'req_amount' => $this->jobRequest['req_amount'],
                'updated_amount' => ($fromBank->amount) - ($this->jobRequest['req_amount']),
                'created_by' => $this->jobRequest['created_by'],
            ];
            //bank amount update
            $fromOurBankDetailRecord = OurBankDetailRecord::create($fromData);
            $fromBank->update([
                'amount' =>  $fromOurBankDetailRecord->updated_amount,
            ]);
            $toData = [
                'our_bank_detail_id' => $this->jobRequest['to_bank'],
                'type' => $this->jobRequest['type'],
                'operation' => $this->jobRequest['operation'],
                'bank_amount' => $toBank['amount'],
                'req_amount' => $this->jobRequest['req_amount'],
                'updated_amount' => ($toBank->amount) + ($this->jobRequest['req_amount']),
                'created_by' => $this->jobRequest['created_by'],
            ];
            $toOurBankDetailRecord = OurBankDetailRecord::create($toData);
            $toBank->update([
                'amount' =>  $toOurBankDetailRecord->updated_amount,
            ]);

            //income bank change operion
        } elseif ($this->jobRequest['operation'] == 'Bank Changed') {
            $oldBank = OurBankDetail::where('id', $this->jobRequest['old_bank'])->lockForUpdate()->first();
            $newBank = OurBankDetail::where('id', $this->jobRequest['new_bank'])->lockForUpdate()->first();
            if($this->jobRequest['type']=="Expense Edit"){
                $oldData = [
                    'our_bank_detail_id' => $this->jobRequest['old_bank'],
                    'type' => $this->jobRequest['type'],
                    'operation' => $this->jobRequest['operation'],
                    'bank_amount' => $oldBank['amount'],
                    'req_amount' => $this->jobRequest['old_amount'],
                    'updated_amount' => ($oldBank->amount) + ($this->jobRequest['old_amount']),
                    'created_by' => $this->jobRequest['created_by'],
                ];
                //bank amount update
                $fromOurBankDetailRecord = OurBankDetailRecord::create($oldData);
                $oldBank->update([
                    'amount' =>  $fromOurBankDetailRecord->updated_amount,
                ]);
                $newData = [
                    'our_bank_detail_id' => $this->jobRequest['new_bank'],
                    'type' => $this->jobRequest['type'],
                    'operation' => $this->jobRequest['operation'],
                    'bank_amount' => $newBank['amount'],
                    'req_amount' => $this->jobRequest['new_amount'],
                    'updated_amount' => ($newBank->amount) - ($this->jobRequest['new_amount']),
                    'created_by' => $this->jobRequest['created_by'],
                ];
                $toOurBankDetailRecord = OurBankDetailRecord::create($newData);
                $newBank->update([
                    'amount' =>  $toOurBankDetailRecord->updated_amount,
                ]);
            }elseif($this->jobRequest['type'] == 'income Edit'){
                $oldData = [
                    'our_bank_detail_id' => $this->jobRequest['old_bank'],
                    'type' => $this->jobRequest['type'],
                    'operation' => $this->jobRequest['operation'],
                    'bank_amount' => $oldBank['amount'],
                    'req_amount' => $this->jobRequest['old_amount'],
                    'updated_amount' => ($oldBank->amount) - ($this->jobRequest['old_amount']),
                    'created_by' => $this->jobRequest['created_by'],
                ];
                //bank amount update
                $fromOurBankDetailRecord = OurBankDetailRecord::create($oldData);
                $oldBank->update([
                    'amount' =>  $fromOurBankDetailRecord->updated_amount,
                ]);
                $newData = [
                    'our_bank_detail_id' => $this->jobRequest['new_bank'],
                    'type' => $this->jobRequest['type'],
                    'operation' => $this->jobRequest['operation'],
                    'bank_amount' => $newBank['amount'],
                    'req_amount' => $this->jobRequest['new_amount'],
                    'updated_amount' => ($newBank->amount) + ($this->jobRequest['new_amount']),
                    'created_by' => $this->jobRequest['created_by'],
                ];
                $toOurBankDetailRecord = OurBankDetailRecord::create($newData);
                $newBank->update([
                    'amount' =>  $toOurBankDetailRecord->updated_amount,
                ]);
            }

        } elseif ($this->jobRequest['operation'] == 'Both Operation') {
            $oldFromBank = OurBankDetail::where('id', $this->jobRequest['old_from_bank'])->lockForUpdate()->first();
            $newFromBank = OurBankDetail::where('id', $this->jobRequest['new_from_bank'])->lockForUpdate()->first();
            $oldToBank = OurBankDetail::where('id', $this->jobRequest['old_to_bank'])->lockForUpdate()->first();
            $newToBank = OurBankDetail::where('id', $this->jobRequest['new_to_bank'])->lockForUpdate()->first();
            if ($oldFromBank->id != $newFromBank->id) {
                //old from bank-data
                $oldFromBankData = [
                    'our_bank_detail_id' => $this->jobRequest['old_from_bank'],
                    'type' => $this->jobRequest['type'],
                    'operation' => $this->jobRequest['operation'],
                    'bank_amount' => $oldFromBank['amount'],
                    'req_amount' => $this->jobRequest['req_amount'],
                    'updated_amount' => ($oldFromBank['amount']) + ($this->jobRequest['old_amount']),
                    'created_by' => $this->jobRequest['created_by'],
                ];
                //bank old from bank amount update
                $fromOurBankDetailRecord = OurBankDetailRecord::create($oldFromBankData);
                $oldFromBank->update([
                    'amount' =>  $fromOurBankDetailRecord->updated_amount,
                ]);
                //  dd($this->jobRequest);

                //new from bank-data
                $newFromBankData = [
                    'our_bank_detail_id' => $this->jobRequest['new_from_bank'],
                    'type' => $this->jobRequest['type'],
                    'operation' => $this->jobRequest['operation'],
                    'bank_amount' => $newFromBank['amount'],
                    'req_amount' => $this->jobRequest['req_amount'],
                    'updated_amount' => ($newFromBank['amount']) - ($this->jobRequest['new_amount']),
                    'created_by' => $this->jobRequest['created_by'],
                ];
                //bank amount update
                $fromOurBankDetailRecord = OurBankDetailRecord::create($newFromBankData);
                $newFromBank->update([
                    'amount' =>  $fromOurBankDetailRecord->updated_amount,
                ]);
            } elseif ($oldFromBank->id == $newFromBank->id) {
                $newFromBankData=[];
                if($this->jobRequest['old_amount']!= $this->jobRequest['req_amount']){
                    $newFromBankData = [
                        'our_bank_detail_id' => $this->jobRequest['new_from_bank'],
                        'type' => $this->jobRequest['type'],
                        'operation' => $this->jobRequest['operation'],
                        'bank_amount' => $newFromBank['amount'],
                        'req_amount' => $this->jobRequest['req_amount'],
                        'updated_amount' =>$this->jobRequest['it_operation'] !='Decrease' ? ($newFromBank['amount']) - ($this->jobRequest['req_amount']) :($newFromBank['amount']) +($this->jobRequest['req_amount']),
                        'created_by' => $this->jobRequest['created_by'],
                    ];
                }elseif($this->jobRequest['old_amount'] == $this->jobRequest['req_amount']){
                    $newFromBankData = [
                        'our_bank_detail_id' => $this->jobRequest['new_from_bank'],
                        'type' => $this->jobRequest['type'],
                        'operation' => $this->jobRequest['operation'],
                        'bank_amount' => $newFromBank['amount'],
                        'req_amount' => $this->jobRequest['req_amount'],
                        'updated_amount' => ($newFromBank['amount']),
                        'created_by' => $this->jobRequest['created_by'],
                    ];
                }
                //new from bank-data

                //bank amount update
                $fromOurBankDetailRecord = OurBankDetailRecord::create($newFromBankData);
                $newFromBank->update([
                    'amount' =>  $fromOurBankDetailRecord->updated_amount,
                ]);
            }
            if ($oldToBank->id != $newToBank->id) {
                //old to bank-data
                $oldToBankData = [
                    'our_bank_detail_id' => $this->jobRequest['old_to_bank'],
                    'type' => $this->jobRequest['type'],
                    'operation' => $this->jobRequest['operation'],
                    'bank_amount' => $oldToBank['amount'],
                    'req_amount' => $this->jobRequest['req_amount'],
                    'updated_amount' => ($oldToBank['amount']) - ($this->jobRequest['old_amount']),
                    'created_by' => $this->jobRequest['created_by'],
                ];
                //bank amount update
                $fromOurBankDetailRecord = OurBankDetailRecord::create($oldToBankData);
                $oldToBank->update([
                    'amount' =>  $fromOurBankDetailRecord->updated_amount,
                ]);
                //old to bank-data
                $newToBankData = [
                    'our_bank_detail_id' => $this->jobRequest['new_to_bank'],
                    'type' => $this->jobRequest['type'],
                    'operation' => $this->jobRequest['operation'],
                    'bank_amount' => $newToBank['amount'],
                    'req_amount' => $this->jobRequest['req_amount'],
                    'updated_amount' => ($newToBank['amount']) + ($this->jobRequest['new_amount']),
                    'created_by' => $this->jobRequest['created_by'],
                ];
                //bank amount update
                $fromOurBankDetailRecord = OurBankDetailRecord::create($newToBankData);
                $newToBank->update([
                    'amount' =>  $fromOurBankDetailRecord->updated_amount,
                ]);
            } elseif ($oldToBank->id == $newToBank->id) {
                $newToBankData=[];
                if($this->jobRequest['old_amount'] != $this->jobRequest['req_amount']){
                    $newToBankData = [
                        'our_bank_detail_id' => $this->jobRequest['old_to_bank'],
                        'type' => $this->jobRequest['type'],
                        'operation' => $this->jobRequest['operation'],
                        'bank_amount' => $oldToBank['amount'],
                        'req_amount' => $this->jobRequest['req_amount'],
                        'updated_amount' =>$this->jobRequest['it_operation'] == 'Increase' ? ($oldToBank['amount'])  + ($this->jobRequest['req_amount']):($oldToBank['amount'])  - ($this->jobRequest['req_amount']),
                        'created_by' => $this->jobRequest['created_by'],
                    ];
                }elseif($this->jobRequest['old_amount']==$this->jobRequest['req_amount']){
                    $newToBankData = [
                        'our_bank_detail_id' => $this->jobRequest['old_to_bank'],
                        'type' => $this->jobRequest['type'],
                        'operation' => $this->jobRequest['operation'],
                        'bank_amount' => $oldToBank['amount'],
                        'req_amount' => $this->jobRequest['req_amount'],
                        'updated_amount' => ($oldToBank['amount']),
                        'created_by' => $this->jobRequest['created_by'],
                    ];
                }


                //bank amount update
                $fromOurBankDetailRecord = OurBankDetailRecord::create($newToBankData);
                $newToBank->update([
                    'amount' =>  $fromOurBankDetailRecord->updated_amount,
                ]);
            }
        }
    }
}
