<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!--favicon-->
    <link rel="icon" href="{{ asset('assets/images/favicon-32x32.png') }}" type="image/png" />

    @include('layouts.user_link')
    <title>SikanderPlayx</title>
    <style>
        .wrong_icon_red {
            color: red;
        }

        .righrt_icon_green {
            color: greenyellow;
        }
        .loader {
            border: 8px solid #f3f3f3;
            border-radius: 50%;
            border-top: 8px solid #3498db;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
            position: fixed;
            top: 50%;
            left: 50%;
            margin-top: -25px;
            /* Adjust based on loader height */
            margin-left: -25px;
            /* Adjust based on loader width */
            z-index: 9999;
            display: none;
            /* Initially hide the loader */
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }
    </style>
</head>

<body class="bg-theme bg-theme2">
    @include('components.success-alert')

    <!--wrapper-->
    <div class="wrapper">
        @include('layouts.sidebar')
        <!--start header -->
        @include('layouts.header')
        <!--end header -->
        <!--start page wrapper -->
    <div id="loader" class="loader"></div>

        <div class="page-wrapper">
            <div class="page-content">
                <!-- Modal -->
                <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content bg-white">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5 text-black" id="exampleModalLabel">Profile Details</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body bg-black">
                                <ul class="list-group list-group-flush">
                                    <li
                                        class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                        <h6 class="mb-0">User Name</h6>
                                        <span class="text-white">Kamal Durai</span>
                                    </li>
                                    <li
                                        class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                        <h6 class="mb-0">Mobile No</h6>
                                        <span class="text-white">1234567890</span>
                                    </li>
                                    <li
                                        class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                        <h6 class="mb-0">Platform Paying</h6>
                                        <span class="text-white">@Sikander</span>
                                    </li>
                                    <li
                                        class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                        <h6 class="mb-0">Account Id</h6>
                                        <span class="text-white">#23333334</span>
                                    </li>
                                    <li
                                        class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                        <h6 class="mb-0">User Id</h6>
                                        <span class="text-white">1234</span>
                                    </li>
                                </ul>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="button" class="btn btn-primary">Deposite</button>
                                <button type="button" class="btn btn-primary">Withdraw</button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- end -->

                <div class="row justify-content-between align-items-center">
                    <div class="col-md-3">
                        <a href="{{route('withdraw.pending')}}"> <h6 class="mb-0 text-uppercase">Withdraw List</h6></a>
                     </div>



                    <div class="col-md-6 d-flex justify-content-end">
                        {{-- <button  data-bs-toggle="offcanvas" data-bs-target="#offcanvasRight" aria-controls="offcanvasRight">  --}}
                        @can('Withdraw Add')
                            <a type="button" class="btn btn-light" href="{{ route('withdraw.create') }}   "
                                class="list-group-item">Add<i class="fa-solid fa-plus fs-18"></i></a>
                        @endcan
                        {{-- </button>  --}}
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            @can('Withdraw Show')
                                <table id="example" class="table table-striped table-bordered" style="width: 100%">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>User Name</th>
                                            <th>Account Number</th>
                                            <th>Platform</th>
                                            <th>TimeLine</th>

                                            <th>Bank Name</th>
                                            <th>Bonus Eligible</th>
                                            <th>Amount</th>
                                            <th>Rolling</th>
                                            <th>Image</th>
                                            <th>Deduction Chips</th>
                                            <th>Admin</th>
                                            <th>Banker</th>
                                            {{-- <th>Action</th> --}}
                                        </tr>
                                    </thead>
                                    <tbody id="withdraw_tbody">
                                        @foreach ($withdraws as $item)
                                        @php
                                        // Check if $deposit has a related approvalTimeline
                                        if ($item->approvalTimeline) {
                                            // If it does, proceed with your logic
                                            $startTime = strtotime($item->approvalTimeline->created_at);
                                                    $adminTime = strtotime($item->approvalTimeline->admin_status_at);
                                                    $bankerTime = strtotime($item->approvalTimeline->banker_status_at);
                                                    $ccTime = strtotime($item->approvalTimeline->cc_status_at);
                                                    $endTime = strtotime($item->approvalTimeline->stopped_at); // Get end time
                                                    $now = time(); // Get current time
                                                    $timeElapsed = 0;


                                                    $timeTakenFromcreatedToBanker =$bankerTime ? $bankerTime - $startTime:null; // Time taken from banker update to admin update
                                                    $timeTakenFromBankerToAdmin =$adminTime ? $adminTime - $bankerTime:null; // Time taken from banker update to admin update
                                                    $timeTakenFromAdminToCC = $ccTime ? $ccTime - $adminTime: null; // Time taken from admin update to customer care update

                                            if ($item->approvalTimeline->status === 'Completed') {
                                                // If status is completed, calculate time elapsed between created_at and cc_status_at
                                                $timeElapsed = $endTime - $startTime;
                                            } else {
                                                // If status is pending, calculate time elapsed from creation time until now
                                                $timeElapsed = $now - $startTime;
                                            }

                                            // Calculate hours, minutes, and seconds from the time elapsed
                                            $hours = floor($timeElapsed / 3600);
                                            $minutes = floor(($timeElapsed % 3600) / 60);
                                            $seconds = $timeElapsed % 60;
                                        } else {
                                            // If $deposit does not have a related approvalTimeline, handle this scenario
                                            // For example, set default values or log a message
                                            $startTime = null;
                                            $endTime = null;
                                            $hours = 0;
                                            $minutes = 0;
                                            $seconds = 0;
                                            $timeTakenFromadminTimeToBanker=null;
                                                    $timeTakenFromcreatedToAdmin=null;
                                                    $timeTakenFromAdminToCC=null;
                                        }
                                    @endphp
                                            <tr data-id="{{ $item->id }}"
                                                data-platformid ="{{ $item->platformDetail->id }}"
                                                data-platformname ="{{ $item->platformDetail->platform->name }}">
                                                <td>{{ $item->id }}
                                                     @if (auth()->user()->id == $item->assigned_to)
                                                @if ($item->admin_status === 'Verified')
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input bg-success assigned_to" type="checkbox" id="flexSwitchCheckChecked" checked data-type="Banker">
                                                </div>
                                                @else
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input bg-success assigned_to" type="checkbox" id="flexSwitchCheckChecked" checked data-type="Admin">
                                                </div>
                                                @endif

                                                @elseif (auth()->user()->id !== $item->assigned_to && $item->assigned_to)
                                                    <div class="">
                                                        <span class="form-check-label badge rounded-pill bg-warning text-dark" for="flexSwitchCheckDefault">{{$item->assignedTo ? $item->assignedTo->name : ''}}</span>
                                                    </div>
                                                @else

                                                @if ($item->admin_status === 'Verified')
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input  assigned_to" type="checkbox" id="flexSwitchCheckDefault" @cannot('Withdraw Banker Enable') disabled @endcan data-type="Banker">
                                                        {{-- <span class="form-check-label" for="flexSwitchCheckDefault">Pending</span> --}}
                                                    </div>
                                                @else
                                                   <div class="form-check form-switch">
                                                    <input class="form-check-input   assigned_to" type="checkbox" id="flexSwitchCheckDefault"@cannot('Withdraw Admin Enable') disabled @endcan data-type="Admin">
                                                    {{-- <span class="form-check-label" for="flexSwitchCheckDefault">Pending</span> --}}
                                                </div>
                                                @endif


                                                @endif




                                                </td>



                                                <td>

                                                    <a type="button" data-note-id="{{ $item->id }}"
                                                        href="{{ route('client.get_note', $item->id) }}" type="button"
                                                        data-target="#offcanvas" data-bs-toggle="offcanvas"
                                                        data-bs-target="#offcanvasRight{{ $item->id }}"
                                                        aria-controls="offcanvasRight">
                                                        {{ $item->user->name ?? '' }}
                                                        {{ $item->platformDetail->platform_username ?? $item->platformDetail->player->name }}
                                                    </a>


                                                    @if($item->bank->benificiary->count()!==0)
                                                    <a  type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasRightB{{$item->id}}" aria-controls="offcanvasRight"><p><span class="badge bg-success">Benificiary Exists</span></p></a>
                                                    @else
                                                    <p><span class="badge bg-danger">Benificiary Not Exists</span></p>
                                                    @endif

                                                    <div class="offcanvas offcanvas-end w-50" tabindex="-1" id="offcanvasRightB{{$item->id}}" aria-labelledby="offcanvasRightLabel">
                                                      <div class="offcanvas-header">
                                                        {{-- <h5 id="offcanvasRightLabel">Offcanvas right</h5> --}}

                                                        <button button type="button" class="btn-close icon-close new-meth"
                                                        data-bs-dismiss="offcanvas" aria-label="Close"><i
                                                            class="fa-regular fa-circle-xmark"></i></button>
                                                                            </div>
                                                      <div class="offcanvas-body">
                                                        @if ($item->bank->benificiary)





                                                        <div class="card-body table-responsive">

                                                            <table id="platformsTable"
                                                                class="table table-striped table-bordered">
                                                                <thead>
                                                                    <tr>
                                                                        <th>Bank Name</th>
                                                                        <th>A/C No</th>
                                                                        <th>Limit</th>
                                                                        <th>Count</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>

                                                                    @foreach($item->bank->benificiary as $benificiary)
                                                                    @if ($benificiary->ourBankDetail->status==1)
                                                                        <tr>
                                                                            <td>{{$benificiary->ourBankDetail->bank_name}}
                                                                            </td>
                                                                            <td>{{$benificiary->ourBankDetail->account_number}}
                                                                            </td>
                                                                            <td>{{$benificiary->ourBankDetail->limit}}
                                                                            </td>
                                                                            <td>
                                                                                @php
                                                                                $withdrawUtrCount = App\Models\WithdrawUtr::where('our_bank_detail', $benificiary->our_bank_details_id)
                                                                                ->whereDate('created_at', today())
                                                                                ->whereHas('withdraw', function ($query) {
                                                                                  $query->where('admin_status', 'Verified')
                                                                                  ->where('banker_status', 'Verified');
                                                                                  })->count();
                                                                              @endphp
                                                                              @if ($benificiary->ourBankDetail->limit<=$withdrawUtrCount && $benificiary->ourBankDetail->limit!=0)
                                                                              <span
                                                                              class="badge bg-danger">{{$withdrawUtrCount}}</span>
                                                                              @else
                                                                              <span
                                                                              class="badge bg-success">{{$withdrawUtrCount}}</span>
                                                                              @endif

                                                                            </td>

                                                                        </tr>
                                                                        @endif
                                                                        @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div>







                                                        @endif
                                                      </div>
                                                    </div>
                                                    <div class="offcanvas offcanvas-end" tabindex="-1"
                                                        id="offcanvasRight{{ $item->id }}"
                                                        aria-labelledby="offcanvasRightLabel">

                                                        <div class="offcanvas-header">

                                                           <button button type="button" class="btn-close icon-close new-meth"
                                                                data-bs-dismiss="offcanvas" aria-label="Close"><i
                                                                    class="fa-regular fa-circle-xmark"></i></button>
                                                        </div>
                                                        <div class="offcanvas-body">
                                                            <div class="">
                                                                <div class="page-content">
                                                                    <div class="row">
                                                                        <div class="col-md-6">
                                                                            <div class="col-md-12 ">
                                                                                <div
                                                                                    class="card border-top border-0 border-4 border-white">
                                                                                    @can('Withdraw Info')
                                                                                        <div class="card-body p-5">
                                                                                            <div class="card-heaer-cus"
                                                                                                style="margin: 20px 0;">
                                                                                                <div class="h5">User Info
                                                                                                </div>
                                                                                            </div>
                                                                                            <ul class="list-group list-group-flush">
                                                                                                <li
                                                                                                    class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                                                                                    <h6 class="mb-0">Name
                                                                                                    </h6>
                                                                                                    <span
                                                                                                        class="text-white">{{ $item->platformDetail->player->name ?? '' }}</span>
                                                                                                </li>
                                                                                                <li
                                                                                                    class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                                                                                    <h6 class="mb-0">Email
                                                                                                    </h6>
                                                                                                    <span
                                                                                                        class="text-white">{{ $item->platformDetail->player->email ?? '' }}</span>
                                                                                                </li>
                                                                                                <li
                                                                                                    class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                                                                                    <h6 class="mb-0">Mobile
                                                                                                        Number</h6>
                                                                                                    <span
                                                                                                        class="text-white">{{ $item->platformDetail->player->mobile ?? '' }}</span>
                                                                                                </li>
                                                                                                <li
                                                                                                    class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                                                                                    <h6 class="mb-0">
                                                                                                        Alternative Mobile
                                                                                                        Number
                                                                                                    </h6>
                                                                                                    <span
                                                                                                        class="text-white">{{ $item->platformDetail->player->alternative_mobile ?? '' }}</span>
                                                                                                </li>
                                                                                                <li
                                                                                                    class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                                                                                    <h6 class="mb-0">DOJ</h6>
                                                                                                    <span
                                                                                                        class="text-white">{{ $item->platformDetail->player->dob ?? '' }}</span>
                                                                                                </li>
                                                                                                <li
                                                                                                    class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                                                                                    <h6 class="mb-0">Location
                                                                                                    </h6>
                                                                                                    <span
                                                                                                        class="text-white">{{ $item->platformDetail->player->location ?? '' }}</span>

                                                                                                </li>
                                                                                                <li
                                                                                                    class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                                                                                    <h6 class="mb-0">Lead
                                                                                                        Source
                                                                                                    </h6>
                                                                                                    <span
                                                                                                        class="text-white">{{ $item->platformDetail->player->leadSource->name ?? '' }}</span>
                                                                                                </li>
                                                                                            </ul>
                                                                                        </div>
                                                                                    @endcan
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-12">
                                                                                <div
                                                                                    class="card border-top border-0 border-4 border-white">
                                                                                    @can('Deposit Bankdetails')
                                                                                        <div class="card-body p-5">
                                                                                            <div class="card-heaer-cus"
                                                                                                style="margin: 20px 0;">
                                                                                                <div class="h5">Bank
                                                                                                    Details
                                                                                                </div>
                                                                                            </div>

                                                                                            @php
                                                                                                $banks = App\Models\bank_detail::where('player_id', $item->platformDetail->player_id)->get();

                                                                                            @endphp
                                                                                            @foreach ($banks as $banks)
                                                                                                <ul
                                                                                                    class="list-group list-group-flush">
                                                                                                    <li
                                                                                                        class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                                                                                        <h6 class="mb-0">
                                                                                                            Bank Name</h6>
                                                                                                        <span
                                                                                                            class="text-white">{{ $banks->bank_name }}</span>
                                                                                                    </li>
                                                                                                    <li
                                                                                                        class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                                                                                        <h6 class="mb-0">
                                                                                                            Account Number
                                                                                                        </h6>
                                                                                                        <span
                                                                                                            class="text-white">{{ $banks->account_number }}</span>
                                                                                                    </li>
                                                                                                    <li
                                                                                                        class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                                                                                        <h6 class="mb-0">
                                                                                                            Ifsc
                                                                                                            Code</h6>
                                                                                                        <span
                                                                                                            class="text-white">{{ $banks->ifsc_code }}</span>
                                                                                                    </li>
                                                                                                    <li
                                                                                                        class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                                                                                        <h6 class="mb-0">
                                                                                                            Account Holder
                                                                                                            Name
                                                                                                        </h6>
                                                                                                        <span
                                                                                                            class="text-white">{{ $banks->account_name }}</span>
                                                                                                    </li>
                                                                                                </ul>
                                                                                            @endforeach
                                                                                        </div>
                                                                                    @endcan
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-12">
                                                                                <div
                                                                                    class="card border-top border-0 border-4 border-white">
                                                                                    <div class="card-heaer-cus"
                                                                                    style="margin: 20px 20px;">
                                                                                    <div class="h5">Platform
                                                                                        Details
                                                                                    </div>
                                                                                </div>
                                                                                    @can('Deposit Bankdetails')
                                                                                        <div class="card-body table-responsive">

                                                                                            <table id="platformsTable"
                                                                                                class="table table-striped table-bordered">
                                                                                                <thead>
                                                                                                    <tr>
                                                                                                        <th>ID</th>
                                                                                                        <th>Platform Name</th>
                                                                                                        <th>UserName</th>
                                                                                                        <th>Password</th>
                                                                                                        <th>Link</th>
                                                                                                        <th>Action</th>
                                                                                                    </tr>
                                                                                                </thead>
                                                                                                <tbody>
                                                                                                        @php
                                                                                                            $count = 1;
                                                                                                        @endphp
                                                                                                        @foreach ($item->platformDetail->player->platformDetails as $value)
                                                                                                        <tr>
                                                                                                        <td>{{ $count++ }}
                                                                                                            </td>
                                                                                                            <td>{{ $value->platform->name ?? '' }}
                                                                                                            </td>
                                                                                                            <td>{{ $value->platform_username ?? '' }}
                                                                                                            </td>
                                                                                                            <td>{{ $value->platform_password ?? '' }}
                                                                                                            </td>
                                                                                                            <td>
                                                                                                                {{ $value->platform->url ?? '' }}
                                                                                                            </td>
                                                                                                            <td>
                                                                                                                <a
                                                                                                                    class="copy-details">
                                                                                                                    <i
                                                                                                                        class="fa-solid fa-copy"></i>
                                                                                                                </a>
                                                                                                            </td>
                                                                                                        </tr>
                                                                                                        @endforeach
                                                                                                </tbody>
                                                                                            </table>
                                                                                        </div>
                                                                                    @endcan
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-md-6">
                                                                            <div class="col-md-12">

                                                                                <div
                                                                                    class="card border-top border-0 border-4 border-white">
                                                                                    <div class="card-heaer-cus"
                                                                                    style="margin: 20px 20px;">
                                                                                    <div class="h5">History
                                                                                    </div>
                                                                                </div>
                                                                                    @can('Deposit Bankdetails')
                                                                                        <div class="card-body table-responsive">


                                                                                            @php
                                                                                                $withdrawsAndDeposits = DB::select(
                                                                                                    "
            SELECT
                wd.created_at AS withdraw_date,
                ur.name AS user_name,
                wd.amount AS withdraw_amount,
                d.utr,
                d.deposit_amount,
                d.created_at AS deposit_date,
                d.status,
                d.remark
            FROM withdraws wd
            LEFT JOIN platform_details pd ON wd.platform_detail_id = pd.id
            LEFT JOIN user_registrations ur ON pd.id = ur.id
            LEFT JOIN deposits d ON pd.id = d.platform_detail_id
            WHERE wd.id = :withdraw_id
        ",
                                                                                                    ['withdraw_id' => $item->id],
                                                                                                );
                                                                                            @endphp

                                                                                            <table class="table">
                                                                                                <thead>
                                                                                                    <tr>
                                                                                                        <th>Date</th>
                                                                                                        <th>User Name</th>
                                                                                                        <th>Withdraw Amount</th>
                                                                                                        <th>UTR</th>
                                                                                                        <th>Deposit Amount</th>
                                                                                                        <th>Deposit Date</th>
                                                                                                        <th>Status</th>
                                                                                                        <th>Remark</th>
                                                                                                    </tr>
                                                                                                </thead>
                                                                                                <tbody>
                                                                                                    @forelse ($withdrawsAndDeposits as $row)
                                                                                                        <tr>
                                                                                                            <td>{{ $row->withdraw_date }}
                                                                                                            </td>
                                                                                                            <td>{{ $row->user_name ?? '' }}
                                                                                                            </td>
                                                                                                            <td>{{ $row->withdraw_amount }}
                                                                                                            </td>
                                                                                                            <td>{{ $row->utr }}
                                                                                                            </td>
                                                                                                            <td>{{ $row->deposit_amount }}
                                                                                                            </td>
                                                                                                            <td>{{ $row->deposit_date }}
                                                                                                            </td>
                                                                                                            <td>{{ $row->status }}
                                                                                                            </td>
                                                                                                            <td>{{ $row->remark }}
                                                                                                            </td>
                                                                                                        </tr>
                                                                                                    @empty
                                                                                                        <tr>
                                                                                                            <td colspan="8">No
                                                                                                                data found</td>
                                                                                                        </tr>
                                                                                                    @endforelse
                                                                                                </tbody>
                                                                                            </table>
                                                                                        </div>
                                                                                    @endcan
                                                                                </div>
                                                                            </div>
                                                                        </div>



                                                                    </div>
                                                                </div>



                                                                @can('Withdraw Feedback')
                                                                    {{-- <div class="col-md-6 ">
                                                                                <div
                                                                                    class="card border-top border-0 border-4 border-white">
                                                                                    <div class="card-body p-5">
                                                                                        <form id="noteForm"
                                                                                            action="{{ route('client.note') }}"
                                                                                            method="POST">
                                                                                            @csrf
                                                                                            <div class="card-heaer-cus">
                                                                                                <div class="h5">
                                                                                                    FeedBack
                                                                                                </div>
                                                                                            </div>
                                                                                            <br>
                                                                                            <textarea id="mytextarea_{{ $item->id }}" name="feedback"></textarea>
                                                                                            <script>
                                                                                                tinymce.init({
                                                                                                    selector: "#mytextarea_{{ $item->id }}",
                                                                                                    plugins: "autoresize",
                                                                                                    toolbar: "undo redo | bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link",
                                                                                                });
                                                                                            </script>
                                                                                            <div>
                                                                                                <br>
                                                                                                <input type="hidden"
                                                                                                    name="user_id"
                                                                                                    value="{{ $item->id }}">



                                                                                                <div
                                                                                                    class="d-flex justify-content-center">
                                                                                                    <button
                                                                                                        class="btn btn-primary"
                                                                                                        style="margin-right: -5px"
                                                                                                        type="submit"
                                                                                                        id="saveNoteBtn">Save</button>
                                                                                                </div>
                                                                                        </form>
                                                                                        <div class="container py-2">
                                                                                            @php
                                                                                                $feedback = App\Models\feedback::where('user_id', $item->id)->get();
                                                                                            @endphp

                                                                                            @foreach ($feedback as $feedback)
                                                                                                <div class="row">
                                                                                                    <div
                                                                                                        class="col-auto text-center flex-column d-none d-sm-flex">
                                                                                                        <div class="row h-50">
                                                                                                            <div
                                                                                                                class="col">
                                                                                                                &nbsp;</div>
                                                                                                            <div
                                                                                                                class="col">
                                                                                                                &nbsp;</div>
                                                                                                        </div>
                                                                                                        <h5 class="m-2">
                                                                                                            <span
                                                                                                                class="badge rounded-pill bg-light border">&nbsp;</span>
                                                                                                        </h5>
                                                                                                        <div class="row h-50">
                                                                                                            <div
                                                                                                                class="col border-end">
                                                                                                                &nbsp;</div>
                                                                                                            <div
                                                                                                                class="col">
                                                                                                                &nbsp;</div>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                    <div class="col py-2">
                                                                                                        <div
                                                                                                            class="card radius-15">
                                                                                                            <div
                                                                                                                class="card-body">
                                                                                                                <h4
                                                                                                                    class="card-title text-white">
                                                                                                                    {!! $feedback->feedback ?? '' !!}
                                                                                                                </h4>
                                                                                                                <p
                                                                                                                    class="card-text">
                                                                                                                    {{ $feedback->branchUser->name ?? '' }}
                                                                                                                </p>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>
                                                                                            @endforeach
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div> --}}
                                                                @endcan

                                                            </div>
                                                        </div>
                                                    </div>
                            </div>

                        </div>
                    </div>
                </div>

                </td>
                                                <td>{{ $item->bank->account_number }}</td>
                                                <td>{{ $item->platformDetail->platform->name ?? '' }}</td>
                                                <td>

                                                    <div class="live-counter" data-start-time="{{ $startTime }}">
                                                        <a type="button" data-note-id="{{ $item->id }}" type="button" data-target="#offcanvas"
                                                            data-bs-toggle="offcanvas" data-bs-target="#offcanvasRightTimeLine{{ $item->id }}"
                                                            aria-controls="offcanvasRight">
                                                            <span class="withdraw-counter badge rounded-pill bg-warning text-dark">
                                                                {{ isset($hours) ? sprintf('%02d', $hours) : '00' }}:{{ isset($minutes) ? sprintf('%02d', $minutes) : '00' }}:{{ isset($seconds) ? sprintf('%02d', $seconds) : '00' }}
                                                            </span>
                                                        </a>
                                                        <div class="offcanvas offcanvas-end w-50" tabindex="-1"
                                                                                        id="offcanvasRightTimeLine{{ $item->id }}"
                                                                                        aria-labelledby="offcanvasRightLabel">

                                                                                        <div class="offcanvas-header">

                                                                                            <button type="button" class="btn-close icon-close new-meth"
                                                                                                data-bs-dismiss="offcanvas" aria-label="Close"><i
                                                                                                    class="fa-regular fa-circle-xmark"></i></button>
                                                                                        </div>
                                                                                        <div class="offcanvas-body">
                                                                                            <div class="card-body p-5">
                                                                                                <div class="card-heaer-cus"
                                                                                                    style="margin: 20px 0;">
                                                                                                    <div class="h5">TimeLine
                                                                                                    </div>
                                                                                                </div>
                                                                                                <ul class="list-group list-group-flush">

                                                                                                    <li
                                                                                                        class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                                                                                        <h6 class="mb-0">CreatedBy
                                                                                                        </h6>
                                                                                                        <span
                                                                                                            class="text-white">{{ isset($item->approvalTimeLine->createdBy) ? $item->approvalTimeLine->createdBy['name'] : '' }}</span>
                                                                                                    </li>
                                                                                                    <li
                                                                                                        class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                                                                                        <h6 class="mb-0">Created At
                                                                                                        </h6>
                                                                                                        <span
                                                                                                            class="text-white">{{ isset($item->approvalTimeLine->created_at) ? $item->approvalTimeLine->created_at  : '' }}</span>
                                                                                                    </li>
                                                                                                    <li
                                                                                                        class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                                                                                        <h6 class="mb-0">
                                                                                                            Admin User</h6>
                                                                                                        <span
                                                                                                            class="text-white">{{ isset($item->approvalTimeLine->adminUser) ? $item->approvalTimeLine->adminUser['name'] : '-' }}</span>
                                                                                                    </li>
                                                                                                    <li
                                                                                                        class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                                                                                        <h6 class="mb-0">Admin Update
                                                                                                        </h6>
                                                                                                        <span
                                                                                                            class="text-white">{{ isset($timeTakenFromBankerToAdmin) ?  gmdate("H:i:s", $timeTakenFromBankerToAdmin)  : 'Pending' }}</span>
                                                                                                    </li>
                                                                                                    <li
                                                                                                        class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                                                                                        <h6 class="mb-0">Banker
                                                                                                        </h6>
                                                                                                        <span
                                                                                                            class="text-white">{{ isset($item->approvalTimeLine->bankerUser) ? $item->approvalTimeLine->bankerUser['name'] : '-' }}</span>
                                                                                                    </li>
                                                                                                    <li
                                                                                                        class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                                                                                        <h6 class="mb-0">Banker Update
                                                                                                        </h6>
                                                                                                        <span
                                                                                                            class="text-white">{{ isset($timeTakenFromadminTimeToBanker) ?  gmdate("H:i:s", $timeTakenFromadminTimeToBanker)  : 'Pending' }}</span>
                                                                                                    </li>

                                                                                                    <li
                                                                                                        class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                                                                                        <h6 class="mb-0">
                                                                                                            CustomerCare User
                                                                                                        </h6>
                                                                                                        <span
                                                                                                            class="text-white">{{ isset($item->approvalTimeLine->ccUser) ? $item->approvalTimeLine->ccUser['name'] : '-' }}</span>
                                                                                                    </li>
                                                                                                    <li
                                                                                                        class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                                                                                        <h6 class="mb-0">Customer Care Update
                                                                                                        </h6>
                                                                                                        <span
                                                                                                            class="text-white">{{ isset($timeTakenFromAdminToCC) ?  gmdate("H:i:s", $timeTakenFromAdminToCC)  : 'Pending' }}</span>
                                                                                                    </li>
                                                                                                    <li
                                                                                                        class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                                                                                        <h6 class="mb-0">Status</h6>
                                                                                                        <span
                                                                                                            class="text-white">{{ isset($item->approvalTimeLine->status) ? $item->approvalTimeLine->status : '' }}</span>
                                                                                                    </li>

                                                                                                </ul>                                                 </div>
                                                                                        </div>

                                                    </div>
                                                </td>
                                                <td>{{ $item->bank->bank_name }}</td>
                                                <td>
                                                    @if ($item->is_bonus_eligible == 1)
                                                        <span class="badge bg-success">Yes</span>
                                                    @else
                                                        <span class="badge bg-danger">No</span>
                                                    @endif
                                                </td>
                                                <td>{{ $item->amount }}</td>
                                                <td>{{ $item->rolling_type }}</td>
                                                <td>
                                                    @if ($item->image)
                                                        <a type="button" data-note-id="{{ $item->id }}"
                                                            href="{{ route('deposit.show', $item->id) }}" type="button"
                                                            data-target="#offcanvas" data-bs-toggle="offcanvas"
                                                            data-bs-target="#offcanvasRight1{{ $item->id }}"
                                                            aria-controls="offcanvasRight">
                                                            <img src="{{ asset('storage/' . $item->image) }}"
                                                                alt="Image" style="width: 20%;">
                                                        </a>
                                                    @else
                                                        No Image
                                                    @endif
                                                    <div class="offcanvas offcanvas-end w-50" tabindex="-1"
                                                        id="offcanvasRight1{{ $item->id }}"
                                                        aria-labelledby="offcanvasRightLabel">

                                                        <div class="offcanvas-header">

                                                            <button type="button" class="btn-close icon-close new-meth"
                                                                data-bs-dismiss="offcanvas" aria-label="Close"><i
                                                                    class="fa-regular fa-circle-xmark"></i></button>
                                                        </div>
                                                        <div class="offcanvas-body">
                                                            <div class="">
                                                                <div class="page-content"> </div>
                                                                <div class="row justify-content-center">
                                                                    <div class="col-md-6 ">
                                                                        <div class="">

                                                                            <div class="d-flex justify-content-center">

                                                                                <div class="card-heaer-cus">
                                                                                    <div class="h5">
                                                                                        Image
                                                                                    </div>
                                                                                    <img src="{{ asset('storage/' . $item->image) }}"
                                                                                        alt="Image"
                                                                                        style="width: 100%;">

                                                                                </div>


                                                                                <div>

                                                                                </div>


                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>



                                                </td>
                                                <td>{{ $item->d_chips }}</td>
                                                <td>
                                                    <div>
                                                        <select style="width: 110px; text-align: center;"
                                                            class="form-control rolling-type-select withdraw_admin"
                                                            id="withdraw_admin" name="withdraw_admin"
                                                            @if ($item->banker_status === 'Verified') disabled @else enabled @endif
                                                            @cannot('Withdraw Admin Enable') disabled @endcan required>
                                                            <option value="Pending"
                                                                @if ($item->admin_status === 'Pending') selected @endif>Pending
                                                            </option>
                                                            <option value="Verified"
                                                                @if ($item->admin_status === 'Verified') selected @endif>Verified
                                                            </option>
                                                            <option value="Rejected"
                                                                @if ($item->admin_status === 'Rejected') selected @endif>Rejected
                                                            </option>
                                                        </select>

                                                    </div>
                                                    <!-- Admin Reject Verify Modal -->
            <div class="modal fade" id="rejectModal{{ $item->id }}" tabindex="-1"
                aria-labelledby="exampleModalLabel" aria-hidden="true">
                <input type="hidden" id="new_withdraw_reject_id" class="form-control" value="{{ $item->id }}">
                <input type="hidden" id="new_platform_id" class="form-control"
                    value="{{ $item->platformDetail->platform_id }}">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">
                                Reject Withdraw</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="title">
                                        Remarks
                                    </div>
                                    <div class="content">
                                        <textarea name="reject_remark" class="form-control" id="reject_remark" cols="10" rows="5"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" id="confirm_admin_verify"
                                class="btn btn-primary confirm_admin_reject">Confirm</button>
                            <button type="button" id="close_model" class="btn btn-secondary close_model"
                                data-bs-dismiss="modal">Close</button>
                            <span id="verify_error" class="error-message" style="color:red"></span>
                        </div>
                    </div>
                </div>
            </div>
                                                </td>
                                                <td class="withdraw_status">
                                                    <div>
                                                        <select style="width: 110px; text-align: center;"
                                                            class="form-control rolling-type-select withdraw_banker"
                                                            id="withdraw_banker" name="withdraw_banker"
                                                            @if ($item->admin_status === 'Verified') enabled @else disabled @endif
                                                            @cannot('Withdraw Banker Enable') disabled @endcan required>
                                                            <option value="Pending"
                                                                @if ($item->banker_status === 'Pending') selected @endif>Pending
                                                            </option>
                                                            {{-- <option value="Processing"
                                                            @if ($item->banker_status === 'Processing') selected @endif>Processing
                                                        </option> --}}
                                                            <option value="Not Verified"
                                                                @if ($item->banker_status === 'Not Verified') selected @endif>
                                                                Not
                                                                Verified</option>
                                                            <option value="Verified"
                                                                @if ($item->banker_status === 'Verified') selected @endif>Verified
                                                            </option>
                                                            <option value="Rejected"
                                                                @if ($item->banker_status === 'Rejected') selected @endif>Rejected
                                                            </option>
                                                        </select>

                                                    </div>
                                                    <!-- Admin Withdraw Verify Modal -->
                                                    <div class="modal fade"
                                                        id="adminWithdrawVerifyModal{{ $item->id }}" tabindex="-1"
                                                        aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                        <input type="hidden" id="new_deposit_id" class="form-control"
                                                            value="{{ $item->id }}">
                                                        <input type="hidden" id="new_platform_id" class="form-control"
                                                            value="{{ $item->platformDetail->platform_id }}">
                                                        <div class="modal-dialog modal-lg">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title" id="exampleModalLabel">
                                                                        Verify Withdraw</h5>
                                                                    <button type="button" class="btn-close"
                                                                        data-bs-dismiss="modal"
                                                                        aria-label="Close"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <div class="row">
                                                                        <div class="col-md-6">
                                                                            <div class="title">
                                                                                Withdraw Details
                                                                            </div>
                                                                            <div class="content">
                                                                                <table class="modal-table">
                                                                                    <tr>
                                                                                        <td>Name:</td>
                                                                                        <td>{{ $item->platformDetail->platform_username ?? $item->platformDetail->player->name }}
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td>Platform:</td>
                                                                                        <td>{{ $item->platformDetail->platform->name }}
                                                                                        </td>
                                                                                    </tr>

                                                                                  <tr>

                                                                                        <td>Bonus Eligible:</td>
<td>


                                                                                        @if ($item->is_bonus_eligible == 1)
                                                                                        <span class="badge bg-success fs-6">Yes</span>
                                                                                        {{-- <td></td> --}}
                                                                                        @else
                                                                                        {{-- <td> --}}
                                                                                            <span class="badge bg-danger fs-6">No</span>
                                                                                        {{-- </td> --}}
                                                                                        @endif
                                                                                    </td>
                                                                                  </tr>
                                                                                    <tr>
                                                                                        <td>Withdraw Amount:</td>
                                                                                        <td><span
                                                                                                id="deposit_amount" class="badge bg-warning text-dark fs-6">{{ $item->amount }}</span>
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td>Bank Detail:</td>
                                                                                        <!-- <td>{{ $item->bank->account_number }},
                                                                                            {{ $item->bank->bank_name }}
                                                                                        </td> -->
                                                                                        <td>
                                                            @if ($item->bank->upi)
                                                                {{ $item->bank->upi }}
                                                            @else
                                                                {{ $item->bank->account_number }},
                                                                {{ $item->bank->bank_name }},
                                                                {{ $item->bank->ifsc_code }}
                                                            @endif
                                                        </td>

                                                                                    </tr>
                                                                                </table>
                                                                            </div>
                                                                        </div>

<div class="col-md-6">
    <div class="title">
        Withdraw Confirm
    </div>
    <div class="row">
        <div class="col-md-6">Withdraw Amount</div>
        <div class="col-md-6">
            <span id="current_deposit_amount" class="badge bg-warning text-dark fs-6">{{ $item->amount }}</span>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <input type="checkbox" name="rollover_complete" id="rollover_complete" class="rollover_complete" value="is Rollover Completed">
            <label>Is Rollover Completed? <span class="mandatory-field">*</span></label>
            <span class="error" id="roll-over-error{{$item->id}}" style="color: red"></span>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">Deduction Chips</div>
        <div class="col-md-6">
            <input type="text" id="deduction-chips" name="deduction-chips" class="bonus-editor form-control" value="">
        </div>
    </div>
    <span class="error" id="admin-model-error{{$item->id}}" style="color: red"></span>
</div>
                                                                </div>
                                                                <div class="modal-footer">

                                                                    <span id="confirm-message" class="confirm-message">Enter Total Withdraw Amount <input type="text"
                                                                            name="confirm_total_deposit_amount"
                                                                            id="confirm_total_deposit_amount">
                                                                    </span>
                                                                    <span class="mandatory-field">*</span>
                                                                    <button type="button" id="confirm_admin_verify"
                                                                        class="btn btn-primary confirm_admin_verify">Confirm</button>
                                                                    <button type="button" id="close_model"
                                                                        class="btn btn-secondary close_model"
                                                                        data-bs-dismiss="modal">Close</button>
                                                                    <span id="verify_error" class="error-message"
                                                                        style="color:red"></span>

                                                                </div>

                                                            </div>
                                                        </div>
                                                    </div>
                                                    </div>
                                                    <!-- Banker Withdraw Verify Modal -->
                                                    <div class="modal fade"
                                                        id="bankerWithdrawVerifyModal{{ $item->id }}" tabindex="-1"
                                                        aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                        <input type="hidden" id="new_withdraw_id" class="form-control"
                                                            value="{{ $item->id }}">
                                                        <div class="modal-dialog modal-lg">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title" id="exampleModalLabel">
                                                                        Verify Withdraw</h5>
                                                                    <button type="button" class="btn-close"
                                                                        data-bs-dismiss="modal"
                                                                        aria-label="Close"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <div class="row">
                                                                        <div class="col-md-6">
                                                                            <div class="title">
                                                                                Withdraw Details
                                                                            </div>
                                                                            <div class="content">
                                                                                <table class="modal-table">
                                                                                    <tr>
                                                                                        <td>Name:</td>
                                                                                        <td>{{ $item->platformDetail->platform_username ?? $item->platformDetail->player->name }}
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td>Platform:</td>
                                                                                        <td>{{ $item->platformDetail->platform->name }}
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td>Withdraw Amount:</td>
                                                                                        <td><span
                                                                                                id="deposit_amount" class="badge bg-warning text-dark fs-6">{{ $item->amount }}</span>
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                        <td>Bank Detail:</td>
                                                        <td>
                                                            @if ($item->bank->upi)
                                                                {{ $item->bank->upi }}
                                                            @else
                                                                {{ $item->bank->account_number }},
                                                                {{ $item->bank->bank_name }},
                                                                {{ $item->bank->ifsc_code }}
                                                            @endif
                                                        </td>
                                                    </tr>
                                                                                    <!-- <tr>
                                                                                        <td>Bank Detail:</td>
                                                                                        <td>{{ $item->bank->account_number }},
                                                                                            {{ $item->bank->bank_name }}
                                                                                        </td>

                                                                                    </tr> -->
                                                                                </table>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <div class="title">
                                                                                Withdraw Confirm
                                                                            </div>
                                                                            <div class="form-check form-switch">
                                                                                <input class="form-check-input payment-switch" type="checkbox" role="switch"
                                                                                    id="flexSwitchCheckDefault">
                                                                                <label class="form-check-label" for="flexSwitchCheckDefault">Payment Gateway</label>
                                                                            </div>
                                                                            <div class="row">
                                                                                <div class="col-md-6">Withdraw Amount</div>
                                                                                <div class="col-md-6">
                                                                                    <span
                                                                                        id="current_withdraw_amount" >{{ $item->amount }}</span>
                                                                                </div>
                                                                            </div>
                                                                            <div class="row gateway-switch">
                                                                                <div class="col-md-6">
                                                                                    <label for="file-input" class="drop-area">
                                                                                        <p>Click, drag and drop, or paste an
                                                                                            image file here <span class="mandator-field">*</span></p>
                                                                                        <div id="image-preview">
                                                                                            <img id="image" src="#" alt="Image Preview">
                                                                                        </div>
                                                                                    </label>

                                                                                    <input type="file" class="file-input" accept="image/*">
                                                                                    <span class="image_error" style="color: red"></span>
                                                                                    <span class="error" id="image_error{{ $item->id }}" style="color: red"></span>
                                                                                </div>

                                                                            </div>

                                                                            <div class="row mt-3 gateway-switch">
                                                                                <div class="col-md-6 pt-2">Select Debit Bank</div>
                                                                                <div class="col-md-6">
                                                                                    <select class="form-control select2 our_bank_detail " required name="our_bank_detail"
                                                                                        id="our_bank_detail">
                                                                                        <option value="">Choose Bank</option>
                                                                                        @foreach ($ourbank as $key => $value)
                                                                                            <option value="{{ $value->id }}">{{ $value->bank_name }}
                                                                                            </option>
                                                                                        @endforeach
                                                                                    </select>
                                                                                    <span class="bank_error" id="bank_error" style="color: red"></span>
                                                                                    <span class="error" id="ourBankDetail_error{{ $item->id }}"
                                                                                        style="color: red"></span>
                                                                                </div>
                                                                            </div>


                                                                            <div class="row mt-3 gateway-switch-bank">
                                                                                <div class="col-md-6 pt-2">Select Gateway</div>
                                                                                <div class="col-md-6">
                                                                                    <select class="form-control select2  gateway-category" required name="gateway_category"
                                                                                        id="gateway-category">
                                                                                        <option value="">Choose Gateway</option>
                                                                                        @foreach ($gatewayCategories as $key => $value)
                                                                                            <option value="{{ $value->id }}">{{ $value->gateway_name }}
                                                                                            </option>
                                                                                        @endforeach
                                                                                    </select>

                                                                                    {{-- <span class="bank_error" id="bank_error" style="color: red"></span> --}}
                                                                                    <span class="error" id="ourBankDetail_error{{ $item->id }}"
                                                                                        style="color: red"></span>
                                                                                </div>
                                                                            </div>

                                                                            <div class="row mt-3 gateway-switch-bank">
                                                                                <div class="col-md-6 pt-2">Select Debit Bank</div>
                                                                                <div class="col-md-6">
                                                                                    <select class="form-control select2 our_bank_detail" required name="our_bank_detail"
                                                                                        id="gateway-bank">
                                                                                        <option value="">Choose Bank</option>
                                                                                        {{-- @foreach ($gatewayBank as $key => $value)
                                                                                            <option value="{{ $value->id }}">{{ $value->bank_name }}
                                                                                            </option>
                                                                                        @endforeach --}}
                                                                                    </select>
                                                                                    <span class="error" id="gateway_bank_error{{ $item->id }}"
                                                                                        style="color: red"></span>
                                                                                        <span class="error" id="gateway_acc_error{{ $item->id }}"
                                                                                            style="color: red"></span>
                                                                                </div>
                                                                            </div>

                                                                            <div class="row mt-3 gateway-switch">
                                                                                <div class="col-md-6 pt-2">Withdraw UTR</div>
                                                                                <div class="col-md-6">
                                                                                    <input type="text" class="form-control" id="withdraw_utr">
                                                                                    <span class="utr_error" id="validateError{{ $item->id }}" style="color: red"></span>
                                                                                    <span class="error" id="withdrawUtr_error{{ $item->id }}"
                                                                                        style="color: red"></span>
                                                                                    <span class="error" id="Utr_exit_error{{ $item->id }}"
                                                                                        style="color: red"></span>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">

                                                                    <span id="withdraw-modal-error" class="error-message" style="color:red"></span>
                                                                    <span id="confirm-message" class="confirm-message">
                                                                        Enter Total Witdraw Amount <input type="text"
                                                                            name="confirm_total_withdraw_amount"
                                                                            id="confirm_total_withdraw_amount">


                                                                    </span>
                                                                    <button type="button" id="confirm_banker_verify"
                                                                        class="btn btn-primary confirm_banker_verify">Confirm</button>
                                                                    <button type="button" id="close_model"
                                                                        class="btn btn-secondary close_model"
                                                                        data-bs-dismiss="modal">Close</button>
                                                                    <span id="verify_error" class="error-message"
                                                                        style="color:red"></span>
                                                                        <span class="error-message" id="banker_verify_error{{$item->id}}" style="color: red"></span>
                                                                </div>

                                                            </div>
                                                        </div>
                                                    </div>

                                                     <!-- Banker Reject Verify Modal -->
            <div class="modal fade" id="rejectBankerModal{{ $item->id }}" tabindex="-1"
                aria-labelledby="exampleModalLabel" aria-hidden="true">
                <input type="hidden" id="new_withdraw_banker_reject_id" class="form-control" value="{{ $item->id }}">
                <input type="hidden" id="new_platform_id" class="form-control"
                    value="{{ $item->platformDetail->platform_id }}">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">
                                Reject Withdraw</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="title">
                                        Remarks
                                    </div>
                                    <div class="content">
                                        <textarea name="reject_banker_remark" class="form-control" id="reject_banker_remark" cols="10" rows="5"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" id="confirm_banker_verify"
                                class="btn btn-primary confirm_banker_reject">Confirm</button>
                            <button type="button" id="close_model" class="btn btn-secondary close_model"
                                data-bs-dismiss="modal">Close</button>
                            <span id="verify_error" class="error-message" style="color:red"></span>
                        </div>
                    </div>
                </div>
            </div>
                                                </td>
                                                {{-- <td>

                                                    @can('Withdraw Edit')
                                                        <a href="{{ route('withdraw.edit', $item->id) }}"><i
                                                                class="fa-regular fa-pen-to-square"></i></a>
                                                    @endcan
                                                    @can('Withdraw Delete')
                                                        <a href="" data-id="{{ $item->id }}"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#exampleSmallModal{{ $item->id }}"><i
                                                                class="fa-solid fa-trash-can"></i></a>
                                                    @endcan
                                                </td> --}}
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--end page wrapper -->
        <!--start overlay-->
        <div class="overlay toggle-icon"></div>
        <!--end overlay-->
        <!--Start Back To Top Button--> <a href="javaScript:;" class="back-to-top"><i
                class='bx bxs-up-arrow-alt'></i></a>
        <!--End Back To Top Button-->
        {{-- @include('layouts.footer')  --}}
    </div>
    <!--end wrapper-->
    <!--start switcher-->
    <div class="switcher-wrapper">
        <div class="switcher-btn"> <i class='bx bx-cog bx-spin'></i>
        </div>
        <div class="switcher-body">
            <div class="d-flex align-items-center">
                <h5 class="mb-0 text-uppercase">Theme Customizer</h5>
                <button type="button" class="btn-close ms-auto close-switcher" aria-label="Close"></button>
            </div>
            <hr />
            <p class="mb-0">Gaussian Texture</p>
            <hr>
            <ul class="switcher">
                <li id="theme1"></li>
                <li id="theme2"></li>
                <li id="theme3"></li>
                <li id="theme4"></li>
                <li id="theme5"></li>
                <li id="theme6"></li>
            </ul>
            <hr>
            <p class="mb-0">Gradient Background</p>
            <hr>
            <ul class="switcher">
                <li id="theme7"></li>
                <li id="theme8"></li>
                <li id="theme9"></li>
                <li id="theme10"></li>
                <li id="theme11"></li>
                <li id="theme12"></li>
                <li id="theme13"></li>
                <li id="theme14"></li>
                <li id="theme15"></li>
            </ul>
        </div>
    </div>
    <!--end switcher-->

    @foreach ($withdraws as $frame)
        <div class="modal fade" id="exampleSmallModal{{ $frame->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"> Delete Confirmation</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to delete this item?
                        <form id="deleteForm{{ $frame->id }}" method="post"
                            action="{{ route('withdraw.delete', $frame->id) }}">
                            @csrf
                            @method('DELETE')
                            {{-- <button type="submit" class="btn btn-danger">Delete</button> --}}

                    </div>
                    <div class="modal-footer">
                        <!-- Add a "Cancel" button that dismisses the modal -->
                        <button type="submit" class="btn btn-danger">Delete</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    </div>
                    </form>
                </div>
            </div>
        </div>
        </div>
    @endforeach

    <!-- userdetail canva  -->

    <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasRight" aria-labelledby="offcanvasRightLabel">

        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="offcanvasRightLabel">
                Offcanvas right
            </h5>
            <button type="button" class="btn-close icon-close new-meth" data-bs-dismiss="offcanvas"
                aria-label="Close"><i class="fa-regular fa-circle-xmark"></i></button>
        </div>
        <div class="offcanvas-body">
            <div class="">
                <div class="page-content">
                    <!--breadcrumb-->
                    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
                        <div class="breadcrumb-title pe-3">Forms</div>
                        <div class="ps-3">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb mb-0 p-0">
                                    <li class="breadcrumb-item">
                                        <a href="javascript:;"><i class="bx bx-home-alt"></i></a>
                                    </li>
                                    <li class="breadcrumb-item active" aria-current="page">
                                        Form Layouts
                                    </li>
                                </ol>
                            </nav>
                        </div>
                        <div class="ms-auto">
                            <div class="btn-group">
                                <button type="button" class="btn btn-light">
                                    Settings
                                </button>
                                <button type="button" class="btn btn-light dropdown-toggle dropdown-toggle-split"
                                    data-bs-toggle="dropdown">
                                    <span class="visually-hidden">Toggle Dropdown</span>
                                </button>
                                <div class="dropdown-menu dropdown-menu-right dropdown-menu-lg-end">
                                    <a class="dropdown-item" href="javascript:;">Action</a>
                                    <a class="dropdown-item" href="javascript:;">Another action</a>
                                    <a class="dropdown-item" href="javascript:;">Something else here</a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="javascript:;">Separated link</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--end breadcrumb-->
                    <div class="row">
                        <div class="col-xl-12 mx-auto">
                            <h6 class="mb-0 text-uppercase">Basic Form</h6>
                            <hr>
                            <div class="card border-top border-0 border-4 border-white">
                                <div class="card-body p-5">
                                    <div class="card-title d-flex align-items-center">
                                        <div>
                                            <i class="bx bxs-user me-1 font-22 text-white"></i>
                                        </div>
                                        <h5 class="mb-0 text-white">
                                            User Creation
                                        </h5>
                                    </div>



                                    <hr>

                                    <form class="row g-3">
                                        <div class="col-12">
                                            <label for="inputAddress" class="form-label">Address</label>
                                            <textarea class="form-control" id="inputAddress" placeholder="Address..." rows="3"></textarea>
                                        </div>

                                        <div class="col-12">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="gridCheck">
                                                <label class="form-check-label" for="gridCheck">Check me out</label>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <button type="submit" class="btn btn-light px-5">
                                                Register
                                            </button>
                                        </div>
                                    </form>

                                    <div class="container py-2">
                                        <h2 class="font-weight-light text-center text-white py-3">Chat </h2>
                                        <!-- timeline item 1 -->
                                        <div class="row">
                                            <!-- timeline item 1 left dot -->
                                            <div class="col-auto text-center flex-column d-none d-sm-flex">
                                                <div class="row h-50">
                                                    <div class="col">&nbsp;</div>
                                                    <div class="col">&nbsp;</div>
                                                </div>
                                                <h5 class="m-2">
                                                    <span class="badge rounded-pill bg-light border">&nbsp;</span>
                                                </h5>
                                                <div class="row h-50">
                                                    <div class="col border-end">&nbsp;</div>
                                                    <div class="col">&nbsp;</div>
                                                </div>
                                            </div>
                                            <!-- timeline item 1 event content -->
                                            <div class="col py-2">
                                                <div class="card radius-15">
                                                    <div class="card-body">
                                                        <div class="float-end">Mon, Jan 9th 2020 7:00 AM</div>
                                                        <h4 class="card-title text-white">Orientation</h4>
                                                        <p class="card-text">Welcome to the campus, introduction and
                                                            get started with the tour.</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!--/row-->

                                        <div class="row">
                                            <!-- timeline item 1 left dot -->
                                            <div class="col-auto text-center flex-column d-none d-sm-flex">
                                                <div class="row h-50">
                                                    <div class="col">&nbsp;</div>
                                                    <div class="col">&nbsp;</div>
                                                </div>
                                                <h5 class="m-2">
                                                    <span class="badge rounded-pill bg-light border">&nbsp;</span>
                                                </h5>
                                                <div class="row h-50">
                                                    <div class="col border-end">&nbsp;</div>
                                                    <div class="col">&nbsp;</div>
                                                </div>
                                            </div>
                                            <!-- timeline item 1 event content -->
                                            <div class="col py-2">
                                                <div class="card radius-15">
                                                    <div class="card-body">
                                                        <div class="float-end">Mon, Jan 9th 2020 7:00 AM</div>
                                                        <h4 class="card-title text-white">Orientation</h4>
                                                        <p class="card-text">Welcome to the campus, introduction and
                                                            get started with the tour.</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>


                                        <div class="row">
                                            <!-- timeline item 1 left dot -->
                                            <div class="col-auto text-center flex-column d-none d-sm-flex">
                                                <div class="row h-50">
                                                    <div class="col">&nbsp;</div>
                                                    <div class="col">&nbsp;</div>
                                                </div>
                                                <h5 class="m-2">
                                                    <span class="badge rounded-pill bg-light border">&nbsp;</span>
                                                </h5>
                                                <div class="row h-50">
                                                    <div class="col border-end">&nbsp;</div>
                                                    <div class="col">&nbsp;</div>
                                                </div>
                                            </div>
                                            <!-- timeline item 1 event content -->
                                            <div class="col py-2">
                                                <div class="card radius-15">
                                                    <div class="card-body">
                                                        <div class="float-end">Mon, Jan 9th 2020 7:00 AM</div>
                                                        <h4 class="card-title text-white">Orientation</h4>
                                                        <p class="card-text">Welcome to the campus, introduction and
                                                            get started with the tour.</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>


                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- end -->

    <!-- canva end -->

    @include('layouts.user_script')

    <script>
        $(document).ready(function() {

            $('#example').DataTable({

            })
            $('#platformsTable tr').find('th:eq(4), td:eq(4)').hide();

        });

        // var rowIds = [];

        //self assign
        $(document).on('change', '.assigned_to', function () {
                var rowId = $(this).closest('tr').data('id');
                var selectedValue = $(this).is(':checked');
                var type = $(this).closest('.assigned_to').data('type'); // Assuming you have a data-type attribute in your HTML

            console.log(selectedValue, rowId, type);

                // console.log(selectedValue,rowId);
                $.ajax({
                    type: "POST",
                    url: "{{route('withdraw.assigned-to')}}",
                    data: {
                        selectedValue: selectedValue,
                        withdrawId: rowId,
                        type: type
                    },
                    success: function(response) {
            console.log(response);
            if (response.action == "Assigned") {
                $(this).removeClass('bg-danger').addClass('bg-success').prop("checked", true);
            } else if(response.action == "Assign Removed") {
                $(this).removeClass('bg-success').prop("checked", false);
            }else if(response.action == "Assign To Someone"){
                $(this).addClass('bg-danger').prop("checked", false);
            }else if(response.action == "Not Assign"){
                $(this).addClass('bg-danger').prop("checked", false);

            }
        }.bind(this), // bind 'this' to the success function
                    error: function(xhr) {
                        // Handle the error if needed
                        console.log(xhr.responseText);
                        console.log('error');
                    }
                });
        });


        // Function to copy table data
        $('.copy-details').click(function() {

            var $row = $(this).closest('tr'); // Get the parent row

            // Define the placeholders
            var placeholders = {
                '{Platform Name}': $row.find('td').eq(1).text().trim(),
                '{Platform link}': $row.find('td').eq(4).text().trim(),
                '{platform_username}': $row.find('td').eq(2).text().trim(),
                '{platform_password}': $row.find('td').eq(3).text().trim(),
                '{Static Content}': `1 point @ 1 Rupees Balance
Fancy Minimum Bet 100
Match Minimum Bet 100

*For deposit -
7208306079

*For withdrawal -
7208611809

*for customer support -
+1(409) 419 - 6217 `
            };

            // Create the template
            var template =
                `💰 ${placeholders['{Platform Name}']} 💰\n\nSite : ${placeholders['{Platform link}']}\n\nUser Id : ${placeholders['{platform_username}']}\nPassword : ${placeholders['{platform_password}']}\n\n${placeholders['{Static Content}']}`;

            // Attempt to use the Clipboard API
            if (navigator.clipboard) {
                navigator.clipboard.writeText(template).then(function() {
                    alert('Content copied to clipboard:\n' + template);
                }).catch(function(err) {
                    // If Clipboard API fails, try the alternative method
                    alternativeCopyMethod(template);
                });
            } else {
                // If Clipboard API is not supported, try the alternative method
                alternativeCopyMethod(template);
            }
        });

        // Function for the alternative copy method
        function alternativeCopyMethod(text) {
            var textArea = document.createElement("textarea");
            textArea.value = text;
            document.body.appendChild(textArea);
            textArea.select();

            try {
                document.execCommand("copy");
                alert('Content copied to clipboard (alternative method):\n' + text);
            } catch (err) {
                alert('Failed to copy content to clipboard using the alternative method: ' + err);
            } finally {
                document.body.removeChild(textArea);
            }
        }
    </script>
       <script>
        // Function to update the live timer
        function updateTimer(element) {
            var startTime = parseInt(element.dataset.startTime); // Get the start time
            var now = Math.floor(Date.now() / 1000); // Get the current time in seconds

            // Calculate the elapsed time
            var elapsedTime = now - startTime;

            // Calculate hours, minutes, and seconds
            var hours = Math.floor(elapsedTime / 3600);
            var minutes = Math.floor((elapsedTime % 3600) / 60);
            var seconds = elapsedTime % 60;

            // Format the time
            var formattedTime = ('0' + (hours ? hours : '00')).slice(-2) + ':' +
                ('0' + (minutes ? minutes : '00')).slice(-2) + ':' +
                ('0' + (seconds ? seconds : '00')).slice(-2);

            // Update the timer display
            element.querySelector('.withdraw-counter').textContent = formattedTime;
        }

        // Function to update the timers every second
        function updateTimers() {
            // Get all elements with the 'live-counter' class
            var timerElements = document.querySelectorAll('.live-counter');

            // Update each timer
            timerElements.forEach(function(element) {
                updateTimer(element);
            });
        }

        // Update the timers every second
        setInterval(updateTimers, 1000);

        // Call updateTimers() initially to start the timers immediately
        updateTimers();
    </script>
    <script>
        const btn_icon = document.querySelectorAll('.action-icons');
        console.log(btn_icon, "btn_icon sdxgdfhgh");

        btn_icon.forEach((btn_icons) => {
            btn_icons.addEventListener('click', () => {
                if (btn_icons.classList.contains('clicked')) {
                    // If the button was already clicked, reset it
                    btn_icons.innerHTML = `Not Verified <i class="fa-solid fa-xmark wrong_icon_red"></i>`;
                    // button.classList.add('fas', 'fa-heart');
                    btn_icons.classList.remove('clicked');
                } else {

                    btn_icons.innerHTML = `Verified <i class="fas fa-check righrt_icon_green"></i>`;
                    btn_icons.classList.add('clicked');
                }
            })
        });
    </script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });


        $(document).ready(function() {


            // if()

            var withdrawStatus = "{{ url('withdraw_status') }}";

            $('#platformsTable tr').find('th:eq(4), td:eq(4)').hide();

            // Function to copy table data
            $('.copy-details').click(function() {
                var $row = $(this).closest('tr'); // Get the parent row

                // Define the placeholders
                var placeholders = {
                    '{Platform Name}': $row.find('td').eq(1).text().trim(),
                    '{Platform link}': $row.find('td').eq(4).text().trim(),
                    '{platform_username}': $row.find('td').eq(2).text().trim(),
                    '{platform_password}': $row.find('td').eq(3).text().trim(),
                    '{Static Content}': `1 point @ 1 Rupees Balance
Fancy Minimum Bet 100
Match Minimum Bet 100

*For deposit -
7208306079

*For withdrawal -
7208611809

*for customer support -
+1(409) 419 - 6217 `
                };

                // Create the template
                var template =
                    `💰 ${placeholders['{Platform Name}']} 💰\n\nSite : ${placeholders['{Platform link}']}\n\nUser Id : ${placeholders['{platform_username}']}\nPassword : ${placeholders['{platform_password}']}\n\n${placeholders['{Static Content}']}`;

                // Attempt to use the Clipboard API
                if (navigator.clipboard) {
                    navigator.clipboard.writeText(template).then(function() {
                        alert('Content copied to clipboard:\n' + template);
                    }).catch(function(err) {
                        // If Clipboard API fails, try the alternative method
                        alternativeCopyMethod(template);
                    });
                } else {
                    // If Clipboard API is not supported, try the alternative method
                    alternativeCopyMethod(template);
                }
            });

            // Function for the alternative copy method
            function alternativeCopyMethod(text) {
                var textArea = document.createElement("textarea");
                textArea.value = text;
                document.body.appendChild(textArea);
                textArea.select();

                try {
                    document.execCommand("copy");
                    alert('Content copied to clipboard (alternative method):\n' + text);
                } catch (err) {
                    alert('Failed to copy content to clipboard using the alternative method: ' + err);
                } finally {
                    document.body.removeChild(textArea);
                }
            }



            $(document).on('change', '.withdraw_admin', function() {

                var selectedValue = $(this).val();
                var rowId = $(this).closest('tr').data('id');
                var platformId = $(this).closest('tr').data('platformid');
                var platformName = $(this).closest('tr').data('platformname');
                if (selectedValue == "Verified") {
                    $('#adminWithdrawVerifyModal' + rowId).modal('show');

                    return false;
                }
                else if (selectedValue === "Rejected") {
                    $('#rejectModal' + rowId).modal('show');

                    return false;
                }
                var type = "withdraw_banker";
                console.log(selectedValue);
                console.log(rowId);
                // var test = "sample";
                $.ajax({
                    type: "POST",
                    url: withdrawStatus,
                    data: {
                        selectedValue: selectedValue,
                        userid: rowId,
                        type: type
                    },
                    success: function(response) {
                        // Handle the response if needed
                        response = response[0];
                        if (response.flag == 1) {
                            console.log("inside flag");
                            $("table tbody#withdraw_tbody tr[data-id='" + rowId +
                                "'] td.withdraw_status select").val(response
                                .withdraw_status);
                        }
                    },
                    error: function(xhr) {
                        // Handle the error if needed
                        console.log(xhr.responseText);
                    }
                });
            });
function displayError(element, errorMessage) {
        element
            .html(errorMessage)
            .css("display", "block")
            .delay(3000)
            .fadeOut(700);
    }
         //Function click when Admin Submit Verified the verify modal
         $(document).on('click', '.confirm_admin_verify', function() {
                console.log("inside confirm_admin_verify");
                var that = this;

                var modalId = $(this).closest('.modal').attr('id');
                var rowId = $(this).closest('tr').data('id');

                var deposit_amount = $('#' + modalId + ' #current_deposit_amount').html();
                var dchips_amount = $('#' + modalId + ' #deduction-chips').val();
                // alert(dchips_amount);
                var verify_amount = $('#' + modalId + ' #confirm_total_deposit_amount').val();
                var remark = $('#' + modalId + ' #deposit_admin_remarks').val();
                console.log("model id"+rowId);
                var withdraw_amt = deposit_amount - dchips_amount;
                // alert(withdraw_amt);

                if (!$('#' + modalId + ' #rollover_complete').is(':checked')) {
                    $('#' + modalId + ' #withdraw-modal-error').html("Need to Fill the required(*) fields").css("display", "block");
                    displayError($('#' + modalId + ' #withdraw-modal-error'), "Need to Fill the required(*) fields");
                    console.log("rollover not checked");
                    $('#roll-over-error'+rowId).text('rollover not checked');

                    return false;
                } else {
                    var rollover = "Yes";
                    $('#roll-over-error'+rowId).text('');

                }
                console.log(modalId);
                if (verify_amount == "") {
                    displayError($('#' + modalId + ' #withdraw-modal-error'), "Need to Fill the required(*) fields");
                    $('#admin-model-error'+rowId).text('Please Enter Withdraw Amount');

                    // admin-model-error
                    return false;
                }
                if (deposit_amount == verify_amount) {
                    $('#admin-model-error'+rowId).text('');

                    console.log("amount Equal");
                } else {
                    displayError($('#' + modalId + ' #withdraw-modal-error'), "Validation amount is not equal");
                    console.log("amount Not Equal");
                    $('#admin-model-error'+rowId).text('Amount Not Equal');

                    return false;
                }
                console.log(modalId);
                var selectedValue = "Verified";
                 rowId = $('#' + modalId + ' #new_deposit_id').val();
                console.log("row if"+rowId);
                var platformId = $('#' + modalId + ' #new_platform_id').val();
                var type = "withdraw_admin";
                var d_chips = $('#' + modalId + ' #deduction-chips').val();
                // var test = "sample";
                $.ajax({
                    type: "POST",
                    dataType: "JSON",
                    url: withdrawStatus,
                    data: {
                        selectedValue: selectedValue,
                        userid: rowId,
                        type: type,
                        rollover: rollover,
                        d_chips:d_chips,
                        withdraw_amt:withdraw_amt
                    },
                    success: function(response) {
                        // Handle the response if needed
                        console.log("adminStatus Response");
                        response = response[0];
                        if (response.flag == 1) {
                            displayMessage($(".custom-alert"), "Success",
                                "Status Updated Successfully");
                            // var selectElement = $('.banker_admin');
                            // selectElement.val('Verified');
                            $("table tbody#withdraw_tbody tr[data-id='" + rowId +
                                "'] td.withdraw_banker select").val(response
                                .withdraw_status);
                                $("table tbody#withdraw_tbody tr[data-id='" + rowId + "'] .assigned_to").prop('disabled', true);

                         }
                        $(".close_model").click();

                    },
                    error: function(xhr) {
                        // Handle the error if needed
                        console.log(xhr.responseText);
                    }
                });

            });
              //Function click when Admin Submit Rejected the reject modal
              $(document).on('click', '.confirm_admin_reject', function() {
                console.log("inside confirm_admin_reject");
                var modalId = $(this).closest('.modal').attr('id');
                var remark = $('#' + modalId + ' #reject_remark').val();

                var selectedValue = "Rejected";
                var rowId = $('#' + modalId + ' #new_withdraw_reject_id').val();
                console.log(rowId);
                var platformId = $('#' + modalId + ' #new_platform_id').val();
                var type = "withdraw_admin";
                // var test = "sample";
                $.ajax({
                    type: "POST",
                    dataType: "JSON",
                    url: withdrawStatus,
                    data: {
                        selectedValue: selectedValue,
                        userid: rowId,
                        type: type,
                        remark: remark
                    },
                    success: function(response) {
                        // Handle the response if needed
                        console.log("adminStatus Response");
                        console.log(response);
                        console.log(response[0]);
                        console.log(response.flag);
                        response = response[0];
                        if (response.flag == 1) {
                            displayMessage($(".custom-alert"), "Success",
                                "Status Updated Successfully");
                            $("table tbody#withdraw_tbody tr[data-id='" + rowId +
                                "'] .assigned_to").prop('disabled', true);

                            // var selectElement = $('.banker_admin');
                            // selectElement.val('Verified');
                        }
                        $(".close_model").click();

                    },
                    error: function(xhr) {
                        // Handle the error if needed
                        console.log(xhr.responseText);
                    }
                });

            });


            //Function click when Admin Submit Rejected the reject modal
            $('.confirm_banker_reject').click(function () {

                console.log("inside confirm_baker_reject");
                var modalId = $(this).closest('.modal').attr('id');
                var remark = $('#' + modalId + ' #reject_banker_remark').val();
                console.log(modalId);
                var selectedValue = "Rejected";
                var rowId = $('#' + modalId + ' #new_withdraw_banker_reject_id').val();
                console.log(rowId);
                var type = "withdraw_banker";
                // var test = "sample";
                $.ajax({
                    type: "POST",
                    dataType: "JSON",
                    url: withdrawStatus,
                    data: {
                        selectedValue: selectedValue,
                        userid: rowId,
                        type: type,
                        remark: remark
                    },
                    success: function (response) {
                        // Handle the response if needed
                        console.log("adminStatus Response");
                        console.log(response);
                        console.log(response[0]);
                        console.log(response.flag);
                        response = response[0];
                        if (response.flag == 1) {
                            displayMessage($(".custom-alert"), "Success", "Status Updated Successfully");
                            $("table tbody#withdraw_tbody tr[data-id='" + rowId + "'] .assigned_to").prop('disabled', true);

                            // var selectElement = $('.banker_admin');
                            // selectElement.val('Verified');
                        }
                        $(".close_model").click();

                    },
                    error: function (xhr) {
                        // Handle the error if needed
                        console.log(xhr.responseText);
                    }
                });

            });

            $('#submit_withdraw_status').click(function() {

                var userId = $('#user-id').val();
                var userPassword = $('#user-password').val();
                var platformId = $('#platform_id').val();
                console.log(platformId);

                if (userId.trim() === '') {
                    displayError($("#user-id-error"), "The User Id field is required");
                    return false;
                }
                if (userPassword.trim() === '') {
                    displayError($("#user-password-error"), "The User Password field is required");
                    return false;
                }
                var selectedValue = "Verified";
                // var rowId = $(this).closest('tr').data('id');
                var rowId = $('#deposit_id').val();

                var type = "deposit_admin";
                console.log(selectedValue);
                console.log(rowId);
                // var test = "sample";
                $.ajax({
                    type: "POST",
                    url: withdrawStatus,
                    data: {
                        selectedValue: selectedValue,
                        userid: rowId,
                        type: type,
                        platform_id: platformId,
                        userId: userId,
                        userPassword: userPassword
                    },
                    success: function(response) {

                        response = response[0];
                        if (response.flag == 1) {
                            displayError($("#success-message"),
                                "Data Saved successfully");

                                $("table tbody#withdraw_tbody tr[data-id='" + rowId + "'] .assigned_to").prop('disabled', true);

                            // Close the modal after a brief delay (e.g., 2 seconds)
                            setTimeout(function() {
                                $('#verifiedModal').modal('hide');
                            }, 2000);
                        }

                    },
                    error: function(xhr) {
                        // Handle the error if needed
                        console.log(xhr.responseText);
                    }
                });
            });

            $(document).on('change', '.withdraw_banker', function() {

                var selectedValue = $(this).val();
                var rowId = $(this).closest('tr').data('id');
                var platformId = $(this).closest('tr').data('platformid');
                var platformName = $(this).closest('tr').data('platformname');
                if (selectedValue == "Verified") {
                    $('#bankerWithdrawVerifyModal' + rowId).modal('show');
                    const model = 'bankerWithdrawVerifyModal' + rowId;
                   const isSwitchChecked = $('#' + model + ' .payment-switch').is(':checked');
                   console.log(isSwitchChecked);

                   if (isSwitchChecked) {
                    $('#' + model + ' .gateway-switch').hide();
                    $('#' + model + ' .gateway-switch-bank').show();
                    return false;

                } else {
                    $('#' + model + ' .gateway-switch').show();
                    $('#' + model + ' .gateway-switch-bank').hide();
                    return false;
                }

                    // $('#' + model + ' .gateway-switch-bank').hide()
                }
                if (selectedValue == "Rejected") {
                    $('#rejectBankerModal' + rowId).modal('show');

                    return false;
                }
                var type = "withdraw_banker";
                console.log(selectedValue);
                console.log(rowId);
                // var test = "sample";
                $.ajax({
                    type: "POST",
                    url: withdrawStatus,
                    data: {
                        selectedValue: selectedValue,
                        userid: rowId,
                        type: type
                    },
                    success: function(response) {
                        // Handle the response if needed
                        response = response[0];
                        if (response.flag == 1) {
                            console.log("inside flag");
                            $("table tbody#withdraw_tbody tr[data-id='" + rowId +
                                "'] td.withdraw_status select").val(response
                                .withdraw_status);
                                 $("table tbody#withdraw_tbody tr[data-id='" + rowId + "'] .assigned_to").prop('disabled', true);

                        }
                    },
                    error: function(xhr) {
                        // Handle the error if needed
                        console.log(xhr.responseText);
                    }
                });
            });

            $('.payment-switch').on('change', function() {
                var modalId = $('.modal.show').attr('id');
                if ($(this).is(':checked')) {
                    $('#' + modalId + ' .gateway-switch').hide();
                    $('#' + modalId + ' .gateway-switch-bank').show();

                } else {
                    $('#' + modalId + ' .gateway-switch').show();
                    $('#' + modalId + ' .gateway-switch-bank').hide();

                }
            });

            $(document).on('change', '.gateway-category', function() {
                // console.log('hello navee');
                // console.log(this.value);
                var selectedValue = $(this).val();
                var modalId = $('.modal.show').attr('id');
                rowId = $(this).closest('tr').data('id');
                var formData = new FormData();
                formData.append("gatewayId", selectedValue);


                   $.ajax({
                    type: "POST",
                    dataType: "JSON",
                    url:"{{url('/get-gateway-banks')}}",
                    processData: false,
                    contentType: false,
                    data: formData,
                    success: function(response) {
                        $('#' + modalId +' #gateway-bank').empty();
                        // Handle the response if needed
                        for(var data of response){
                            // console.log(data);
                            $('#' + modalId +' #gateway-bank').append('<option value="'+data.id+'">'+data.bank_name+'</option>');
                        }
                    },
                });
            });


            $(document).on('click', '.confirm_banker_verify', function() {
                var $button = $(this);

                var imagePath = "";
                console.log("inside confirm_banker_verify");
                // var modalId = $(this).closest('.modal').attr('id');
                var modalId = $('.modal.show').attr('id');
                rowId = $(this).closest('tr').data('id');
                var isSwitchChecked = $('#' + modalId + ' .payment-switch').is(':checked');


                // console.log()
// console.log('row id'+rowId);
                // $('#' + modalId).css('background', 'red');


                console.log(modalId);
                if(modalId){
                    console.log(modalId);
                    console.log("modal founded");

                }else{
                console.log("Not Find Modal ID");
                return false;

                }
                var file = $('#' + modalId + ' .file-input').prop('files')[0];
                console.log(file);

                    console.log("has file");
                    var formData = new FormData();
                    formData.append('image', $('#' + modalId + ' .file-input').prop('files')[0]);
                    // reader.readAsDataURL(file);

                console.log(formData);
                var withdraw_amount = $('#' + modalId + ' #current_withdraw_amount').html();
                var verify_amount = $('#' + modalId + ' #confirm_total_withdraw_amount').val();
                var withdraw_utr = $('#' + modalId + ' #withdraw_utr').val();
                var bank_detail = $('#' + modalId + ' #our_bank_detail').val();
                var gateway_bank_detail = $('#' + modalId + ' #gateway-bank').val();


                if (verify_amount == "") {
            $('#banker_verify_error'+rowId).append('<span>' + 'Enter Witdraw Amount' + '</span>');

                    return false;
                }
                if (withdraw_amount == verify_amount) {

                    console.log("amount Equal");
                    $('.error-message').empty();

                } else {
                    console.log("amount Not Equal");
            $('#banker_verify_error'+rowId).append('<span>' + 'Amount Not Equal' + '</span>');

                    return false;
                }
                console.log(modalId);
                var selectedValue = "Verified";
                var rowId = $('#' + modalId + ' #new_withdraw_id').val();
                // console.log(rowId);
                var type = "withdraw_banker";

                if (isSwitchChecked) {
                    formData.append("paymentMode", "Razorpay");
                    formData.append("type", type);
                    formData.append("userid", rowId);
                    formData.append("selectedValue", selectedValue);
                    formData.append("gateway_bank", gateway_bank_detail);
                } else {
                    formData.append("paymentMode", "Manual");
                    formData.append("type", type);
                    formData.append("userid", rowId);
                    formData.append("selectedValue", selectedValue);
                    formData.append("withdrawUtr", withdraw_utr);
                    formData.append("ourBankDetail", bank_detail);
                }


            //   .prop('disabled', true);
            // console.log('closeessdsjf');


                 // Disable the button and show the spinner
                  $button.prop('disabled', true).html('<span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span>');
                // var test = "sample";bank_detail
                $.ajax({
                    type: "POST",
                    dataType: "JSON",
                    url: withdrawStatus,
                    processData: false,
                    contentType: false,
                    data: formData,
                    // data: {
                    //     selectedValue: selectedValue,
                    //     userid: rowId,
                    //     type: type,
                    //     imagePath: imagePath
                    // },
                    success: function(response) {
                        $('.error').empty();
                        // Handle the response if needed
                        console.log("adminStatus Response");
                        response = response[0];
                        if (response.flag == 1) {
                            displayMessage($(".custom-alert"), "Success",
                                "Status Updated Successfully");
                            //    if(isSwitchChecked==false) {
                                $("table tbody#withdraw_tbody tr[data-id='" + rowId + "'] .assigned_to").prop('disabled', true);

                            //    }else{
                            //     var $withdrawBankerSelect = $(this).closest('tr').find('.withdraw_banker').prop('disabled',true);
                            //    }


                            // var selectElement = $('.banker_admin');
                            // selectElement.val('Verified');
                            $(".close_model").click();
                        }

                        else if (response.flag == 0) {
                            console.log("UTR Number is Exist");
                          $('#Utr_exit_error'+rowId).text('UTR Number is Exist');

                            // $("#validateError").text("UTR Number is Exist");
                            setTimeout(function() {
                                $("#Utr_exit_error" + rowId).text(''); // Clear the text
                            }, 5000);
                        }else if(response.status_code == 402){
                            // $("#validateError").text("Insufficient Funds");
                          $('#ourBankDetail_error'+rowId).text('Insufficient Funds');

                            setTimeout(function() {
                            $("#ourBankDetail_error"+rowId).text(''); // Clear the text
                            }, 5000);
                        }
                        $button.prop('disabled', false).html('Confirm');

                    },
                    error: function(xhr) {

                        var errors = xhr.responseJSON.errors;
                        gatewayError=xhr.responseJSON;
                        console.log(errors);
                        console.log(gatewayError);

                        $('.error').empty();
                        if(gatewayError.flag==3){
                            // gateway_acc_error
                            $('#gateway_acc_error'+rowId).text(gatewayError.g_error);
                            // console.log('thsi gatew way');
                            console.log($('#gateway_acc_error'+rowId));

                            setTimeout(function() {
                                $("#gateway_acc_error"+rowId).text(''); // Clear the text
                            }, 5000);
                        }

                     if(errors){
                        for (var key in errors) {
    if (errors.hasOwnProperty(key)) {
        // Clear previous error messages before appending new ones
        $('#' + key + '_error').empty();

        errors[key].forEach(function(errorMsg) {
            // Append each error message to the appropriate location within the HTML
            $('#' + key + '_error'+rowId).append('<span>' + errorMsg + '</span>');
        });
    }
}

                        }

                        $button.prop('disabled', false).html('Confirm');


                    }
                });

            });

            function displayMessage(element, title, message) {
                element.css("display", "block")
                    .delay(3000)
                    .fadeOut(700);
                $('#alert-title').text(title);
                $('#alert-message').text(message);

            }
        });

        // $(document).ready(function() {
            // Handle file input change event
            $('.file-input').on('change', function() {
                // var modalId = $(this).closest('.modal.show').attr('id');
                var modalId = $('.modal.show').attr('id');
                var file = this.files[0];
                console.log("file");
                console.log(file);
                if (file) {
                    const formData = new FormData();
                formData.append('image', file);
                    var reader = new FileReader();

                    reader.onload = function(e) {
                        $('#' + modalId + ' #image').attr('src', e.target.result);
                        $('#' + modalId + ' #image-preview').show();
                    };

                    reader.readAsDataURL(file);
                    sendImageForConversion(formData,modalId);

                }
            });



            async function sendImageForConversion(formData,modalId) {
                    try {
                        showLoader();
                        const response = await fetch('{{ route('image-to-text') }}', {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        });
                        hideLoader();

                        if (response.ok) {
                            const data = await response.json();
                            handleConversionResult(data,modalId); // Handle the conversion result
                        } else {
                            console.error('Error:', response.statusText);
                        }

                    } catch (error) {
                        // hideLoader();
                        console.error('Error:', error);
                    }
                }


    function handleConversionResult(data,modalId) {
                    // rowId = $(this).closest('tr').data('id');
                   const utrNumber = data.utr[0];
                    // console.log('row row owroskfsdlk'+rowId);
                    const amount = parseInt(data.amount.replace(/,/g, ''), 10);
                    // console.log('amount result '+utrNumber);
                    $('#' + modalId + ' #withdraw_utr').val(utrNumber);

                }

     function showLoader() {
                document.getElementById('loader').style.display = 'block';
                // document.getElementById('content').style.opacity = '0.5';
            }


            function hideLoader() {
                document.getElementById('loader').style.display = 'none';
                // document.getElementById('content').style.opacity = '1';
            }
            $('.drop-area').on('click', function(e) {
                console.log("in");
                console.log($(this));
                // var modalId = $('.modal.show').attr('id');
                var fileInput = $(this).parent().find('input.file-input');
                console.log(fileInput);
                fileInput.click();
            });
            // Handle drag and drop events
            $('.drop-area').on('dragover', function(e) {
                var modalId = $('.modal.show').attr('id');

                e.preventDefault();
                $(this).css('border-color', 'blue');
            });



            $('.drop-area').on('dragleave', function(e) {
                var modalId = $('.modal.show').attr('id');


                e.preventDefault();
                $(this).css('border-color', '#ccc');
            });

            $('.drop-area').on('drop', function(e) {
            var modalId = $(this).closest('.modal').attr('id');

                e.preventDefault();
                $(this).css('border-color', '#ccc');
                var file = e.originalEvent.dataTransfer.files[0];
                if (file) {
                    var reader = new FileReader();

                    reader.onload = function(e) {
                        $('#image').attr('src', e.target.result);
                        $('#image-preview').show();
                    };

                    reader.readAsDataURL(file);
                }
            });

            // Handle pasting an image from clipboard
            $(document).on('paste', function(e) {
            console.log("inside paste doc");
            var items = e.originalEvent.clipboardData.items;
            var modalId = $('.modal.show').attr('id');

            for (var i = 0; i < items.length; i++) {
                console.log("inside for");

                if (items[i].type.indexOf('image') !== -1) {
                    const targetElement = document.querySelector('#' + modalId + ' .file-input');
                    var file = items[i].getAsFile();

                    const newFileList = new DataTransfer();
                      newFileList.items.add(file);

                    // Assign the new FileList to the input element
                    // fileInput.files = newFileList.files;
                    targetElement.files = newFileList.files;
                    console.log("file");

                    console.log(file);

                    if (file) {
                                    var reader = new FileReader();

                                    reader.onload = function(e) {
                                        $('#' + modalId + ' #image').attr('src', e.target.result).trigger("change");
                                        $('#' + modalId + ' #image-preview').show();
                                    };

                                    reader.readAsDataURL(file);
                                }
                }
            }
        });
        // });
    </script>
    <script src="assets/js/index.js"></script>

</body>

</html>
