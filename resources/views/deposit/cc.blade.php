<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!--favicon-->
    <link rel="icon" href="{{ asset('assets/images/favicon-32x32.png') }}" type="image/png" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link href="{{ asset('assets/plugins/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" />

    {{-- @include('layouts.user_link') --}}
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/bootstrap-extended.css') }}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <link href="{{ asset('assets/css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/icons.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"
        integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <title>SikanderPlayX - Portal</title>
    @include('layouts.user_link')

    <style>
        .wrong_icon_red {
            color: red;
        }

        .righrt_icon_green {
            color: greenyellow;
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
                <div class="row justify-content-between align-items-center">
                    <div class="col-md-4">
                        <h6 class="mb-0 text-uppercase">Deposit List</h6>
                    </div>

                    <div class="col-md-8 d-flex justify-content-end">

                        @can('Deposit Add')
                            <a type="button" class="btn btn-light" href="{{ route('deposit.create') }}   "
                                class="list-group-item">Add<i class="fa-solid fa-plus fs-18"></i></a>
                        @endcan

                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            @can('Deposit Show')
                                <table id="example" class="table table-striped table-bordered" style="width: 100%">
                                    <thead>
                                        <tr>

                                            <th>ID</th>
                                            <th>User Name</th>
                                            <th>Platform</th>
                                            <th>TimeLine</th>
                                            <th>Amount</th>
                                            <th>Bonus</th>
                                            <th>Total Amount</th>
                                            <th>Admin</th>
                                            <th>Banker</th>
                                            <th>CC</th>
                                            <th>Created By</th>

                                        </tr>
                                    </thead>
                                    <tbody id="deposit_tbody">
                                        @foreach ($deposit as $item)
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
                                                    $timeTakenFromBankerToAdmin = $adminTime?$adminTime - $bankerTime:null; // Time taken from banker update to admin update
                                                    $timeTakenFromAdminToCC =$ccTime ? $ccTime - $adminTime : null; // Time taken from admin update to customer care update

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
                                                    $timeTakenFromcreatedToBanker = null;
                                                    $timeTakenFromBankerToAdmin = null;
                                                    $timeTakenFromAdminToCC = null;
                                                }
                                            @endphp
                                            <tr data-id="{{ $item->id }}"
                                                data-platformid="{{ $item->platformDetail->id }}"
                                                data-platformname="{{ $item->platformDetail->platform->name }}">

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
                                                        {{ $item->platformDetail->platform_username ?? $item->platformDetail->player->name }}
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
                                                                <div class="page-content"> </div>
                                                                <div class="row">
                                                                    <div class="col-md-6 ">
                                                                        <div
                                                                            class="card border-top border-0 border-4 border-white">
                                                                            @can('Deposit Info')
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
                                                                                                class="text-white">{{ $item->platformDetail->player->name ?? '-' }}</span>
                                                                                        </li>
                                                                                        <li
                                                                                            class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                                                                            <h6 class="mb-0">Email
                                                                                            </h6>
                                                                                            <span
                                                                                                class="text-white">{{ $item->platformDetail->player->email ?? '-' }}</span>
                                                                                        </li>
                                                                                        <li
                                                                                            class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                                                                            <h6 class="mb-0">Mobile
                                                                                                Number</h6>
                                                                                            <span
                                                                                                class="text-white">{{ $item->platformDetail->player->mobile ?? '-' }}</span>
                                                                                        </li>
                                                                                        <li
                                                                                            class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                                                                            <h6 class="mb-0">
                                                                                                Alternative Mobile Number</h6>
                                                                                            <span
                                                                                                class="text-white">{{ $item->platformDetail->player->alternative_mobile ?? '-' }}</span>
                                                                                        </li>
                                                                                        <li
                                                                                            class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                                                                            <h6 class="mb-0">DOB</h6>
                                                                                            <span
                                                                                                class="text-white">{{ $item->platformDetail->player->dob ?? '-' }}</span>
                                                                                        </li>
                                                                                        <li
                                                                                            class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                                                                            <h6 class="mb-0">Location
                                                                                            </h6>
                                                                                            <span
                                                                                                class="text-white">{{ $item->platformDetail->player->location ?? '-' }}</span>
                                                                                        </li>
                                                                                        <li
                                                                                            class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                                                                            <h6 class="mb-0">Lead
                                                                                                Source
                                                                                            </h6>
                                                                                            <span
                                                                                                class="text-white">{{ $item->leadSource->name ?? '-' }}</span>
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
                                                                                            <div class="h5">Bank Details
                                                                                            </div>
                                                                                        </div>

                                                                                        @php
                                                                                            $banks = App\Models\bank_detail::where(
                                                                                                'player_id',
                                                                                                $item->platformDetail
                                                                                                    ->player_id,
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
                                                                                                    <th>Platform</th>
                                                                                                    <th>UserName</th>
                                                                                                    <th>Password</th>
                                                                                                    <th>Link</th>
                                                                                                    {{-- <th>Action</th> --}}
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
                                                                    <div class="col-md-6 ">
                                                                        <div
                                                                            class="card border-top border-0 border-4 border-white">

                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>

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
                                                                                class="text-white">{{ isset($item->approvalTimeLine->created_at) ? $item->approvalTimeLine->created_at : '' }}</span>
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
                                                                                class="text-white">{{ isset($timeTakenFromcreatedToBanker) ? gmdate('H:i:s', $timeTakenFromcreatedToBanker) : 'Pending' }}</span>
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
                                                                                class="text-white">{{ isset($timeTakenFromBankerToAdmin) ? gmdate('H:i:s', $timeTakenFromBankerToAdmin) : 'Pending' }}</span>
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
                                                                                class="text-white">{{ isset($timeTakenFromAdminToCC) ? gmdate('H:i:s', $timeTakenFromAdminToCC) : 'Pending' }}</span>
                                                                        </li>
                                                                        <li
                                                                            class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                                                            <h6 class="mb-0">Status</h6>
                                                                            <span
                                                                                class="text-white">{{ isset($item->approvalTimeLine->status) ? $item->approvalTimeLine->status : '' }}</span>
                                                                        </li>

                                                                    </ul>
                                                                </div>
                                                            </div>

                                                        </div>
                                                </td>
                                                <td>{{ $item->deposit_amount }}</td>
                                                <td>{{ $item->bonus }}</td>
                                                <td>{{ $item->total_deposit_amount }}</td>

                                                <td class="deposit_status">
                                                    <div>
                                                        <select style="width: 110px; text-align: center;"
                                                            class="form-control rolling-type-select deposit_admin"
                                                            id="deposit_admin" name="deposit_admin"
                                                            @if ($item->banker_status === 'Verified') enabled @else disabled @endif
                                                            @cannot('Deposit Admin Enable') disabled @endcan required>
                                                            <option value="Pending"
                                                            @if ($item->admin_status === 'Pending') selected @endif>Pending
                                                        </option>
                                                        <option value="Not Verified"
                                                            @if ($item->admin_status === 'Not Verified') selected @endif>Not Verified
                                                        </option>
                                                        <option value="Verified"
                                                            @if ($item->admin_status === 'Verified') selected @endif>Verified
                                                        </option>
                                                        <option value="Rejected"
                                                            @if ($item->admin_status === 'Rejected') selected @endif>Rejected
                                                        </option>
                                                        </select>
                                                    </div>
                                                    <!-- end -->
                                                </td>
                                                <td>
                                                    <div>
                                                        <select style="width: 110px; text-align: center;"
                                                            class="form-control rolling-type-select banker_admin"
                                                            id="banker_admin" name="banker_admin"
                                                            @if ($item->admin_status === 'Verified') disabled @else enabled @endif
                                                            @cannot('Deposit Banker Enable') disabled @endcan required>
                                                            <option value="Pending"
                                                                @if ($item->banker_status === 'Pending') selected @endif>Pending
                                                            </option>
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
                                                            class="form-control rolling-type-select cc_deposit"
                                                            id="cc_deposit" name="cc_deposit"
                                                            @if ($item->status === 'Completed') enabled @else disabled @endif
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
                                                        <input type="hidden" id="new_deposit_id" class="form-control" value="{{ $item->id }}">
                <input type="hidden" id="new_platform_id" class="form-control"
                    value="{{ $item->platformDetail->platform_id }}">
                                                        <div class="modal-dialog modal-lg">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title" id="exampleModalLabel">
                                                                        Verify Deposit</h5>
                                                                    <button type="button" class="btn-close"
                                                                        data-bs-dismiss="modal"
                                                                        aria-label="Close"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <div class="row">
                                                                        <div class="col-md-6">
                                                                            <div class="title">
                                                                                Deposit Details
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
                                                                                        <td>Deposit Amount:</td>
                                                                                        <td>{{ $item->deposit_amount }}
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td>Bonus:</td>
                                                                                        <td>{{ $item->bonus }}</td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td>Total Amount:</td>
                                                                                        <td>{{ $item->total_deposit_amount }}
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td>UTR:</td>
                                                                                        <td>{{ $item->utr }}</td>
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
                                                                                <a class="copy-details2">
                                                                                    <i class="fa-solid fa-copy"></i>
                                                                                </a>
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
                                                                                    <tr>
                                                                                        <td>Platform Url: </td>
                                                                                        <td>{{ $item->platformDetail->platform->url }}
                                                                                        </td>
                                                                                    </tr>
                                                                                </table>

                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <div class="title">
                                                                                Deposit Receipt
                                                                            </div>
                                                                            <div class="content">
                                                                                @if ($item->image)
                                                                                <img src="{{ asset('storage/' . $item->image) }}"
                                                                                    alt="Image" style="width: 50%;">
                                                                            @else
                                                                                No Image
                                                                            @endif
                                                            </div>

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
                                                            class="btn btn-primary confirm_cc_verify">Yes</button>
                                                        <button type="button" id="close_model"
                                                            class="btn btn-secondary close_model"
                                                            data-bs-dismiss="modal">Close</button>

                                                    </div>

                                                </td>
                                                <td>
                                                    {{ $item->user->name ?? '' }}
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
        {{--  @include('layouts.footer')  --}}
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
    @foreach ($deposit as $frame)
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
                            action="{{ route('deposit.delete', $frame->id) }}">
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

    <!-- canva end -->

    @include('layouts.user_script')
    <script src="{{ asset('assets/js/copy-platformdetails.js') }}"></script>
    <script src="{{ asset('assets/js/pages/deposit.js') }}"></script>
    <script>


$(document).on('change', '.assigned_to', function () {
                var rowId = $(this).closest('tr').data('id');
                var selectedValue = $(this).is(':checked');
                var type = $(this).closest('.assigned_to').data('type'); // Assuming you have a data-type attribute in your HTML


                console.log(selectedValue,rowId);
                $.ajax({
                    type: "POST",
                    url: "{{route('deposit.assigned-to')}}",
                    data: {
                        selectedValue: selectedValue,
                        depositId: rowId,
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
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        var successAlertRoute = @json(route('success-alert'));
        var adminStatus = "{{ url('admin_status') }}";
        var platformDetailActive = "{{ url('platform_detail_active') }}";
        var checkPlatformDetailExist = "{{ url('check_platform_detail_exist') }}";
    </script>
    <script>
        $(document).ready(function() {
            $('#example').DataTable()
        });
    </script>
</body>

</html>
