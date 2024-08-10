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
        .container{
            position: relative;
        }
         .payment-card {
            background: rgba(255, 255, 255, 0.25);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
            backdrop-filter: blur(4px);
            -webkit-backdrop-filter: blur(4px);
            border-radius: 10px;
            border: 1px solid rgba(255, 255, 255, 0.18);
            / border: 1px solid black; /
            /* position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%); */
            /* position: relative; */
            padding: 20px;
            width: 90%;
        }

        .payment-slip {

            /* position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%); */

            background-color: #cc3f95;
            padding: 10px;
            border-radius: 10px 10px 0px 0px;

        }

        .payment-success h2 {
            padding: 10px 10px;
            color: white !important;
            font-size: 18px !important
        }

        .payment-success {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            width: 100%;
            height: 100%;
        }

        .success-icon {
            background: white;
            border-radius: 50%;
            width: 15%;
            height: 50px;
            border: 1px solid #3BB77E;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            margin: 10px;
        }

        .success-icon i {
            color: #3BB77E !important;
            font-size: 20px !important;
            /* height: 45px !important; */
        }

        .payment-content {
            background: white !important;
            padding: 10px;
            border-radius: 0px 0px 10px 10px;

        }
        .payment-content h5{
            color: black !important
        }


        .payment-content .left-content {
            padding-left: 0px !important;
        }

        .payment-content .left-content li {
            list-style: none;
            margin-bottom: 10px;
            color: black !important;
            font-weight: 500;
            padding-left: 10px;
                }
                .payment-content .right-content{
                    padding-left: 0px !important
                }
        .payment-content .right-content li {
            list-style: none;
            margin-bottom: 10px;
            color: black !important;
            font-weight: 500;
            text-align: end;

        }

        .skender-logo {
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            padding: 20px;

        }

        .logo-img {
            position: absolute;
            top: -70%;
            left: 42%;
            background: white !important;
            border-radius: 50%;
            width: 15%;
            height: 50px;

        }

        .logo-img img {
            position: absolute;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            top: 10px;
            left: -7px;
        }
        .thanks-msg a{
            display: flex;
    align-items: center;
    justify-content: center;
    color: #cc3f95 !important;
    font-size: 10px;
    font-weight: 500;
}
.split-line-box{
    display:flex;
    justify-content: center;
    align-items: center;
}
        .split-line{
            content: '';
    width: 80%;
    height: 1px;
    background: black;
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
                    <div class="col-md-4">
                        <h6 class="mb-0 text-uppercase">Withdraw List</h6>
                    </div>

                    <div class="col-md-8 d-flex justify-content-end">
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
                                            <th>Amount</th>
                                            <th>Rolling</th>
                                            <th>Admin</th>
                                            <th>Banker</th>
                                            <th>CC</th>
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


                                                    $timeTakenFromadminTimeToBanker =$bankerTime ? $bankerTime - $adminTime:null; // Time taken from banker update to admin update
                                                    $timeTakenFromcreatedToAdmin =$adminTime ? $adminTime - $startTime:null; // Time taken from banker update to admin update
                                                    // $timeTakenFromBankerToAdmin = $adminTime - $bankerTime; // Time taken from banker update to admin update
                                                    $timeTakenFromAdminToCC =$ccTime ? $ccTime - $bankerTime  : null; // Time taken from admin update to customer care update

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
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input bg-success assigned_to" type="checkbox" id="flexSwitchCheckChecked" checked data-type="CC">

                                                    </div>
                                                @elseif (auth()->user()->id !== $item->assigned_to && $item->assigned_to)
                                                    <div class="">

                                                        <span class="form-check-label badge rounded-pill bg-warning text-dark" for="flexSwitchCheckDefault">{{$item->assignedTo ? $item->assignedTo->name : ''}}</span>
                                                    </div>
                                                @else
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input  assigned_to" type="checkbox" id="flexSwitchCheckDefault" data-type="CC">
                                                        {{-- <span class="form-check-label" for="flexSwitchCheckDefault">Pending</span> --}}
                                                    </div>
                                                @endif

                                                </td>


                                                <td>
                                                    <a type="button" data-note-id="{{ $item->id }}"
                                                        href="{{ route('client.get_note', $item->id) }}" type="button"
                                                        data-target="#offcanvas" data-bs-toggle="offcanvas"
                                                        data-bs-target="#offcanvasRight{{ $item->id }}"
                                                        aria-controls="offcanvasRight">
                                                        {{ $item->user->name ?? '' }}
                                                        {{ $item->platformDetail->player->name }}
                                                    </a>
                                                    <div class="offcanvas offcanvas-end" tabindex="-1"
                                                        id="offcanvasRight{{ $item->id }}"
                                                        aria-labelledby="offcanvasRightLabel">

                                                        <div class="offcanvas-header">

                                                            <button type="button" class="btn-close icon-close new-meth"
                                                                data-bs-dismiss="offcanvas" aria-label="Close"><i
                                                                    class="fa-regular fa-circle-xmark"></i></button>
                                                        </div>
                                                        <div class="offcanvas-body">
                                                            <div class="">
                                                                <div class="page-content">
                                                                    <div class="row">

                                                                        <div class="col-md-6 ">
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
                                                                                                <h6 class="mb-0">Name</h6>
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
                                                                                                    Alternative Mobile Number
                                                                                                </h6>
                                                                                                <span
                                                                                                    class="text-white">{{ $item->platformDetail->player->alternative_mobile ?? '' }}</span>
                                                                                            </li>
                                                                                            <li
                                                                                                class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                                                                                <h6 class="mb-0">DOB</h6>
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
                                                                                                $banks = App\Models\bank_detail::where(
                                                                                                    'player_id',
                                                                                                    $item->platformDetail->player_id,
                                                                                                )->get();

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
                                                                                <div
                                                                                    class="card border-top border-0 border-4 border-white">
                                                                                    @can('Deposit Bankdetails')
                                                                                        <div class="card-body p-5">
                                                                                            <div class="card-heaer-cus"
                                                                                                style="margin: 20px 0;">
                                                                                                <div class="h5">Platform
                                                                                                    Details
                                                                                                </div>
                                                                                            </div>
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
                                                                                                    @foreach ($item->platformDetail->player->platformDetails as $value)
                                                                                                        <tr>
                                                                                                            @php
                                                                                                                $count = 1;
                                                                                                            @endphp

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
                                                        <a type="button" data-note-id="{{ $item->id }}"
                                                            type="button" data-target="#offcanvas"
                                                            data-bs-toggle="offcanvas"
                                                            data-bs-target="#offcanvasRightTimeLine{{ $item->id }}"
                                                            aria-controls="offcanvasRight">
                                                            <span class="deposit-counter badge rounded-pill bg-warning text-dark">
                                                                {{ isset($hours) ? sprintf('%02d', $hours) : '00' }}:{{ isset($minutes) ? sprintf('%02d', $minutes) : '00' }}:{{ isset($seconds) ? sprintf('%02d', $seconds) : '00' }}
                                                            </span>
                                                        </a>
                                                        <div class="offcanvas offcanvas-end w-50" tabindex="-1"
                                                            id="offcanvasRightTimeLine{{ $item->id }}"
                                                            aria-labelledby="offcanvasRightLabel">

                                                            <div class="offcanvas-header">

                                                                <button type="button"
                                                                    class="btn-close icon-close new-meth"
                                                                    data-bs-dismiss="offcanvas" aria-label="Close"><i
                                                                        class="fa-regular fa-circle-xmark"></i></button>
                                                            </div>
                                                            <div class="offcanvas-body">
                                                                <div class="card-body p-5">
                                                                    <div class="card-heaer-cus" style="margin: 20px 0;">
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
                                                                                class="text-white">{{ isset($item->approvalTimeLine->adminUser['name']) ? $item->approvalTimeLine->adminUser['name'] : '-' }}</span>
                                                                        </li>
                                                                        <li
                                                                            class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                                                            <h6 class="mb-0">Admin Update
                                                                            </h6>
                                                                            <span
                                                                                class="text-white">{{ isset($timeTakenFromcreatedToAdmin) ?  gmdate("H:i:s", $timeTakenFromcreatedToAdmin)  : 'Pending' }}</span>
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

                                                                    </ul>                    </div>
                                                            </div>

                                                        </div>
                                                    </div>
                                                </td>

                                                <td>{{ $item->bank->bank_name }}</td>
                                                <td>{{ $item->amount }}</td>
                                                <td>{{ $item->rolling_type }}</td>
                                                <td>
                                                    <div>
                                                        {{--  @dd($item->withdraw_status);  --}}
                                                        <select style="width: 110px; text-align: center;"
                                                            class="form-control rolling-type-select withdraw_admin"
                                                            id="withdraw_admin" name="withdraw_admin"
                                                            @if ($item->banker_status === 'Verified') disabled @else enabled @endif
                                                            @cannot('Withdraw Admin Enable') disabled @endcan required>
                                                            <option value="Pending" @if ($item->admin_status === 'Pending') selected @endif>Pending
                                                            </option>
                                                            <option value="Verified" @if ($item->admin_status === 'Verified') selected @endif>Verified
                                                            </option>
                                                            <option value="Rejected" @if ($item->admin_status === 'Rejected') selected @endif>Rejected
                                                            </option>
                                                        </select>

                                                    </div>
                                                </td>
                                                <td class="withdraw_status">
                                                    <div>
                                                        <select style="width: 110px; text-align: center;"
                                                            class="form-control rolling-type-select withdraw_banker"
                                                            id="withdraw_banker" name="withdraw_banker"
                                                            @if ($item->admin_status === 'Verified')disabled  @else enabled @endif
                                                            @cannot('Withdraw Banker Enable') disabled @endcan required>
                                                            <option value="Pending"
                                                                @if ($item->banker_status === 'Pending') selected @endif>Pending
                                                            </option>
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
                                                </td>
                                                <td>

                                                    <div>
                                                        <select style="width: 110px; text-align: center;"
                                                            class="form-control rolling-type-select cc_withdraw"
                                                            id="cc_withdraw" name="cc_withdraw"
                                                            @if ($item->status == "Completed") enabled @else disabled @endif
                                                            required>
                                                            <option value="0"
                                                                @if ($item->isInformed == 0) selected @endif>Pending
                                                            </option>
                                                            <option value="1"
                                                                @if ($item->isInformed == 1) selected @endif>Verified
                                                            </option>
                                                        </select>
                                                    </div>
                                                    <!-- Banker Verify Modal -->
                                                    <div class="modal fade" id="ccVerifyModal{{ $item->id }}"
                                                        tabindex="-1" aria-labelledby="exampleModalLabel"
                                                        aria-hidden="true">
                                                        <input type="hidden" id="new_withdraw_id" class="form-control" value="{{ $item->id }}">
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

                                                                                <table class="cc-modal-table">
                                                                                    <tr>
                                                                                        <td>Name:</td>
                                                                                        <td>{{ $item->platformDetail->player->name }}</td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td>Platform:</td>
                                                                                        <td>{{ $item->platformDetail->platform->name }}
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td>Withdraw Amount:</td>
                                                                                        <td>{{ $item->amount }}
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td>Banker Status:</td>
                                                                                        @if ($item->banker_status == 'Verified')
                                                                                        <td><span class="modal-status-success">{{ $item->banker_status }}</span></td>
                                                                                        @else
                                                                                        <td><span class="modal-status-error">{{ $item->banker_status }}</span></td>
                                                                                        @endif
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td>Admin Status:</td>
                                                                                        @if ($item->admin_status == 'Verified')
                                                                                        <td><span class="modal-status-success">{{ $item->admin_status }}</span></td>
                                                                                        @else
                                                                                        <td><span class="modal-status-error">{{ $item->admin_status }}</span></td>
                                                                                        @endif
                                                                                    </tr>
                                                                                </table>

                                                                            </div>

                                                                            <div class="title">
                                                                                Platform Details
                                                                            </div>
                                                                            <div class="content">

                                                                                <table class="cc-modal-table">
                                                                                    <tr>
                                                                                        <td>Platform Name: </td>
                                                                                        <td>{{ $item->platformDetail->platform->name }}</td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td>Platform UserName: </td>
                                                                                        <td>{{ $item->platformDetail->platform_username }}
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td>Platform Password: </td>
                                                                                        <td>{{ $item->platformDetail->platform_password }}
                                                                                        </td>
                                                                                    </tr>
                                                                                </table>

                                                                            </div>
                                                                            <div class="title">
                                                                               Debit Bank Details
                                                                            </div>
                                                                            <div class="content">

                                                                                <table class="cc-modal-table">
                                                                                    <tr>
                                                                                        <td>Bank Name: </td>
                                                                                        <td>{{ $item->withdrawUtr && $item->withdrawUtr->ourBankDetail ? $item->withdrawUtr->ourBankDetail->bank_name:'-' }}</td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td> Account No: </td>
                                                                                        <td>{{ $item->withdrawUtr && $item->withdrawUtr->ourBankDetail ?$item->withdrawUtr->ourBankDetail->account_number:'-'  }}
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td>IFSC: </td>
                                                                                        <td>{{ $item->withdrawUtr && $item->withdrawUtr->ourBankDetail ?$item->withdrawUtr->ourBankDetail->ifsc:'-' }}
                                                                                        </td>
                                                                                    </tr>
                                                                                </table>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <div class="title">
                                                                                Withdraw Receipt
                                                                            </div>

                                                                            <div class="
                                                                            @if ($item->withdrawUtr && $item->withdrawUtr->ourBankDetail->type =='Gateway' &&
                                                                            $item->withdrawUtr->utr == $item->id)
                                                                                  d-none
                                                                               @elseif ($item->withdrawUtr && $item->withdrawUtr->ourBankDetail->type !='Gateway' )
                                                                                     d-none
                                                                                 @endif  gateway-payment-slip">
                                                                                <div class="payment-card">
                                                                                    <div class="payment-slip">
                                                                                        <div class="payment">
                                                                                            <div class="payment-success">
                                                                                                <div class="success-icon">
                                                                                                    <i class="fa-solid fa-check"></i>
                                                                                                </div>
                                                                                                <h2>Payment Successfull!</h2>
                                                                                            </div>
                                                                                        </div>

                                                                                    </div>
                                                                                    <div class="payment-content">
                                                                                        <div class="skender-logo">
                                                                                            <div class="logo-img">
                                                                                                <img src="{{asset('./assets/images/Intro_X.png')}}" alt="">

                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="row">
                                                                                            {{-- <h5>Payment Details</h5> --}}
                                                                                            <div class="col-lg-6">
                                                                                                <ul class="left-content">
                                                                                                    <li>Payment Type</li>
                                                                                                    <li>Bank</li>
                                                                                                    <li>A/C No</li>
                                                                                                    <li>Amount</li>
                                                                                                    <li>UTR No</li>

                                                                                                </ul>
                                                                                            </div>
                                                                                            <div class="col-lg-6">
                                                                                                <ul class="right-content">
                                                                                                    <li>Bank</li>
                                                                                                    <li>{{$item->bank->bank_name}}</li>
                                                                                                    <li>{{ $item->bank && $item->bank->account_number ?  str_repeat('*', strlen($item->bank->account_number) - 4) . substr($item->bank->account_number, -4) :'-'  }}
                                                                                                    </li>
                                                                                                    <li>{{$item->amount}}</li>
                                                                                                    <li class="show-utr-details">{{ $item->withdrawUtr && $item->withdrawUtr->utr ? $item->withdrawUtr->utr:'-'}}</li>

                                                                                                </ul>
                                                                                            </div>
                                                                                            <div class="split-line-box mt-3">
                                                                                                <div class="split-line">

                                                                                                </div>
                                                                                            </div>
                                                                                            <div class=" thanks-msg mt-2 mb-2">
                                                                                                <a href="">Thanks for Playing with Sikander</a>
                                                                                                <a href=""> Suppport  +1 (743) 259 - 1109</a>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>

@if($item->withdrawUtr && $item->withdrawUtr->ourBankDetail->type !='Gateway')
                                                                            <div class="content bank-screenshot">
                                                                                @if ($item->image)
                                                                                <img src="{{ asset('storage/' . $item->image) }}"
                                                                                    alt="Image" style="width: 50%;">
                                                                            @else
                                                                                No Image
                                                                            @endif

                                                            </div>
                                                            @endif

                                                            <div class="title">
                                                                Remarks
                                                            </div>
                                                            <div class="content">
                                                                <textarea name="deposit_remarks" id="deposit_remarks" cols="10" rows="5" class="form-control" readonly>{{ $item->remark }}</textarea>
                                                            </div>

                                                    </div>

                                                    <div class="modal-footer">
                                                        <span id="confirm-message" class="confirm-message">
                                                            Did you informed to client?
                                                        </span>
                                                        <button type="button" id="confirm_cc_verify"
                                                            class="btn btn-primary confirm_cc_verify" >Yes</button>
                                                        <button type="button" id="close_model"
                                                            class="btn btn-secondary close_model"
                                                            data-bs-dismiss="modal">Close</button>

                                                    </div>

                                                </td>
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


    @include('layouts.user_script')

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
            element.querySelector('.deposit-counter').textContent = formattedTime;
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
        $(document).ready(function() {

            $('#example').DataTable({

            })
            $('#platformsTable tr').find('th:eq(4), td:eq(4)').hide();

        });


        $(document).on('change', '.assigned_to', function () {
                var rowId = $(this).closest('tr').data('id');
                var selectedValue = $(this).is(':checked');
                var type = $(this).closest('.assigned_to').data('type'); // Assuming you have a data-type attribute in your HTML


                console.log(selectedValue,rowId);
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
                $(this).removeClass('bg-danger').addClass('bg-success');
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
        function displayMessage(element, title, message) {
            element.css("display", "block")
                .delay(3000)
                .fadeOut(700);
            $('#alert-title').text(title);
            $('#alert-message').text(message);

        }
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $(document).ready(function() {
            $.ajax({
                    type: "POST",
                    url: "{{route('gateway.utr-get')}}",
                    success: function(response) {
                        console.log(response);
                    }, // bind 'this' to the success function
                    error: function(xhr) {
                        // Handle the error if needed
                        console.log(xhr.responseText);
                        console.log('error');
                    }
                });
        });


        var withdrawStatus = "{{ url('withdraw_status') }}";
        $('.cc_withdraw').change(function() {
            var informStatus = $(this).val();

            rowId = $(this).closest('tr').data('id');


            if (informStatus == 1) {

                $('#ccVerifyModal' + rowId).modal('show');
            }
        });


        $('.confirm_cc_verify').click(function() {
            console.log("inside confirm_cc_verify");
            var modalId = $(this).closest('.modal').attr('id');
            var rowId = $('#' + modalId + ' #new_withdraw_id').val();
            console.log(rowId);
            var type = "withdraw_cc";
            // var test = "sample";
            $.ajax({
                type: "POST",
                dataType: "JSON",
                url: withdrawStatus,
                data: {
                    userid: rowId,
                    type: type
                },
                success: function(response) {
                    // Handle the response if needed
                    console.log("adminStatus Response");
                    console.log(response);
                    console.log(response[0]);
                    console.log(response.flag);
                    response = response[0];
                    if (response.flag == 1) {
                        displayMessage($(".custom-alert"), "Success", "Status Updated Successfully");
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
    </script>
    <script src="assets/js/index.js"></script>

</body>

</html>
