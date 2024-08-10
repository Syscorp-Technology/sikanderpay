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
                        {{-- <button data-bs-toggle="offcanvas" data-bs-target="#offcanvasRight"
                            aria-controls="offcanvasRight"> --}}
                            @can('Withdraw Add')
                            <a type="button" class="btn btn-light" href="{{ route('withdraw.create') }}   "
                                class="list-group-item">Add<i class="fa-solid fa-plus fs-18"></i></a>
                            @endcan
                            {{-- </button> --}}
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
                                    <tr data-id="{{ $item->id }}" data-platformid="{{ $item->platformDetail->id }}"
                                        data-platformname="{{ $item->platformDetail->platform->name }}">
                                        @php
                                        // Check if $deposit has a related approvalTimeline
                                        if ($item->approvalTimeline) {
                                        // If it does, proceed with your logic
                                        $startTime = strtotime($item->approvalTimeline->created_at);
                                        $adminTime = strtotime(
                                        $item->approvalTimeline->admin_status_at,
                                        );
                                        $bankerTime = strtotime(
                                        $item->approvalTimeline->banker_status_at,
                                        );
                                        $ccTime = strtotime($item->approvalTimeline->cc_status_at);
                                        $endTime = strtotime($item->approvalTimeline->stopped_at); // Get end time
                                        $now = time(); // Get current time
                                        $timeElapsed = 0;

                                        $timeTakenFromcreatedToBanker = $bankerTime
                                        ? $bankerTime - $startTime
                                        : null; // Time taken from banker update to admin update
                                        $timeTakenFromBankerToAdmin = $adminTime
                                        ? $adminTime - $bankerTime
                                        : null; // Time taken from banker update to admin update
                                        $timeTakenFromAdminToCC = $ccTime ? $ccTime - $adminTime : null; // Time taken

                                        if ($item->approvalTimeline->status === 'Completed') {
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
                                        $timeTakenFromadminTimeToBanker = null;
                                        $timeTakenFromcreatedToAdmin = null;
                                        $timeTakenFromAdminToCC = null;
                                        }
                                        @endphp
                                        <td>{{ $item->id }}</td>
                                        <td>
                                            <a type="button" data-note-id="{{ $item->id }}"
                                                href="{{ route('client.get_note', $item->id) }}" type="button"
                                                data-target="#offcanvas" data-bs-toggle="offcanvas"
                                                data-bs-target="#offcanvasRight{{ $item->id }}"
                                                aria-controls="offcanvasRight">
                                                {{ $item->user->name ?? '' }}
                                                {{ $item->platformDetail->platform_username ??
                                                $item->platformDetail->player->name }}
                                            </a>

                                            @if ($item->bank->benificiary)
                                            <a type="button" data-bs-toggle="offcanvas"
                                                data-bs-target="#offcanvasRightBenificiary{{$item->id}}"
                                                aria-controls="offcanvasRight"><span class="badge bg-success">Success
                                                    </span< /a>
                                                    @else
                                                    @endif

                                                    <div class="offcanvas offcanvas-end" tabindex="-1"
                                                        id="offcanvasRightBenificiary{{$item->id}}"
                                                        aria-labelledby="offcanvasRightLabel">
                                                        <div class="offcanvas-header">
                                                            {{-- <h5 id="offcanvasRightLabel">Offcanvas right</h5> --}}
                                                            <button type="button" class="btn-close icon-close new-meth"
                                                                data-bs-dismiss="offcanvas" aria-label="Close"><i
                                                                    class="fa-regular fa-circle-xmark"></i></button>
                                                            <button type="button" class="btn-close text-reset"
                                                                data-bs-dismiss="offcanvas" aria-label="Close"></button>
                                                        </div>
                                                        <div class="offcanvas-body">
                                                            @if ($item->bank->benificiary)
                                                            @foreach ($item->bank->benificiary as $benificiary )
                                                            <p>{{ $benificiary->ourBankDetail->bank_name
                                                                }}</p>
                                                            @endforeach
                                                            @endif
                                                        </div>
                                                    </div>

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
                                                                                        <ul
                                                                                            class="list-group list-group-flush">
                                                                                            <li
                                                                                                class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                                                                                <h6 class="mb-0">Name
                                                                                                </h6>
                                                                                                <span
                                                                                                    class="text-white">{{
                                                                                                    $item->platformDetail->player->name
                                                                                                    ?? '' }}</span>
                                                                                            </li>
                                                                                            <li
                                                                                                class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                                                                                <h6 class="mb-0">Email
                                                                                                </h6>
                                                                                                <span
                                                                                                    class="text-white">{{
                                                                                                    $item->platformDetail->player->email
                                                                                                    ?? '' }}</span>
                                                                                            </li>
                                                                                            <li
                                                                                                class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                                                                                <h6 class="mb-0">Mobile
                                                                                                    Number</h6>
                                                                                                <span
                                                                                                    class="text-white">{{
                                                                                                    $item->platformDetail->player->mobile
                                                                                                    ?? '' }}</span>
                                                                                            </li>
                                                                                            <li
                                                                                                class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                                                                                <h6 class="mb-0">
                                                                                                    Alternative Mobile
                                                                                                    Number
                                                                                                </h6>
                                                                                                <span
                                                                                                    class="text-white">{{
                                                                                                    $item->platformDetail->player->alternative_mobile
                                                                                                    ?? '' }}</span>
                                                                                            </li>
                                                                                            <li
                                                                                                class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                                                                                <h6 class="mb-0">DOB
                                                                                                </h6>
                                                                                                <span
                                                                                                    class="text-white">{{
                                                                                                    $item->platformDetail->player->dob
                                                                                                    ?? '' }}</span>
                                                                                            </li>
                                                                                            <li
                                                                                                class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                                                                                <h6 class="mb-0">
                                                                                                    Location
                                                                                                </h6>
                                                                                                <span
                                                                                                    class="text-white">{{
                                                                                                    $item->platformDetail->player->location
                                                                                                    ?? '' }}</span>

                                                                                            </li>
                                                                                            <li
                                                                                                class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                                                                                <h6 class="mb-0">Lead
                                                                                                    Source
                                                                                                </h6>
                                                                                                <span
                                                                                                    class="text-white">{{
                                                                                                    $item->platformDetail->player->leadSource->name
                                                                                                    ?? '' }}</span>
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
                                                                                        $banks =
                                                                                        App\Models\bank_detail::where(
                                                                                        'player_id',
                                                                                        $item
                                                                                        ->platformDetail
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
                                                                                                    class="text-white">{{
                                                                                                    $banks->bank_name
                                                                                                    }}</span>
                                                                                            </li>
                                                                                            <li
                                                                                                class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                                                                                <h6 class="mb-0">
                                                                                                    Account Number
                                                                                                </h6>
                                                                                                <span
                                                                                                    class="text-white">{{
                                                                                                    $banks->account_number
                                                                                                    }}</span>
                                                                                            </li>
                                                                                            <li
                                                                                                class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                                                                                <h6 class="mb-0">
                                                                                                    Ifsc
                                                                                                    Code</h6>
                                                                                                <span
                                                                                                    class="text-white">{{
                                                                                                    $banks->ifsc_code
                                                                                                    }}</span>
                                                                                            </li>
                                                                                            <li
                                                                                                class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                                                                                <h6 class="mb-0">
                                                                                                    Account Holder
                                                                                                    Name
                                                                                                </h6>
                                                                                                <span
                                                                                                    class="text-white">{{
                                                                                                    $banks->account_name
                                                                                                    }}</span>
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
                                                                                    <div
                                                                                        class="card-body table-responsive">

                                                                                        <table id="platformsTable"
                                                                                            class="table table-striped table-bordered">
                                                                                            <thead>
                                                                                                <tr>
                                                                                                    <th>ID</th>
                                                                                                    <th>Platform Name
                                                                                                    </th>
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
                                                                                                @foreach
                                                                                                ($item->platformDetail->player->platformDetails
                                                                                                as $value)
                                                                                                <tr>
                                                                                                    <td>{{ $count++ }}
                                                                                                    </td>
                                                                                                    <td>{{
                                                                                                        $value->platform->name
                                                                                                        ?? '' }}
                                                                                                    </td>
                                                                                                    <td>{{
                                                                                                        $value->platform_username
                                                                                                        ?? '' }}
                                                                                                    </td>
                                                                                                    <td>{{
                                                                                                        $value->platform_password
                                                                                                        ?? '' }}
                                                                                                    </td>
                                                                                                    <td>
                                                                                                        {{
                                                                                                        $value->platform->url
                                                                                                        ?? '' }}
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
                                                                                    <div
                                                                                        class="card-body table-responsive">


                                                                                        @php
                                                                                        $withdrawsAndDeposits =
                                                                                        DB::select(
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
                                                                                        LEFT JOIN platform_details pd ON
                                                                                        wd.platform_detail_id = pd.id
                                                                                        LEFT JOIN user_registrations ur
                                                                                        ON pd.id
                                                                                        = ur.id
                                                                                        LEFT JOIN deposits d ON pd.id =
                                                                                        d.platform_detail_id
                                                                                        WHERE wd.id = :withdraw_id
                                                                                        ",
                                                                                        [
                                                                                        'withdraw_id' =>
                                                                                        $item->id,
                                                                                        ],
                                                                                        );
                                                                                        @endphp

                                                                                        <table class="table">
                                                                                            <thead>
                                                                                                <tr>
                                                                                                    <th>Date</th>
                                                                                                    <th>User Name</th>
                                                                                                    <th>Withdraw Amount
                                                                                                    </th>
                                                                                                    <th>UTR</th>
                                                                                                    <th>Deposit Amount
                                                                                                    </th>
                                                                                                    <th>Deposit Date
                                                                                                    </th>
                                                                                                    <th>Status</th>
                                                                                                    <th>Remark</th>
                                                                                                </tr>
                                                                                            </thead>
                                                                                            <tbody>
                                                                                                @forelse
                                                                                                ($withdrawsAndDeposits
                                                                                                as $row)
                                                                                                <tr>
                                                                                                    <td>{{
                                                                                                        $row->withdraw_date
                                                                                                        }}
                                                                                                    </td>
                                                                                                    <td>{{
                                                                                                        $row->user_name
                                                                                                        ?? '' }}
                                                                                                    </td>
                                                                                                    <td>{{
                                                                                                        $row->withdraw_amount
                                                                                                        }}
                                                                                                    </td>
                                                                                                    <td>{{ $row->utr }}
                                                                                                    </td>
                                                                                                    <td>{{
                                                                                                        $row->deposit_amount
                                                                                                        }}
                                                                                                    </td>
                                                                                                    <td>{{
                                                                                                        $row->deposit_date
                                                                                                        }}
                                                                                                    </td>
                                                                                                    <td>{{ $row->status
                                                                                                        }}
                                                                                                    </td>
                                                                                                    <td>{{ $row->remark
                                                                                                        }}
                                                                                                    </td>
                                                                                                </tr>
                                                                                                @empty
                                                                                                <tr>
                                                                                                    <td colspan="8">
                                                                                                        No
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

                                                            </div>
                                                        </div>
                                                    </div>

                                        </td>
                                        <td>{{ $item->bank->account_number }}</td>
                                        <td>{{ $item->platformDetail->platform->name ?? '' }}</td>
                                        <td>

                                            <div class="live-counter" data-start-time="{{ $startTime }}">
                                                <a type="button" data-note-id="{{ $item->id }}" type="button"
                                                    data-target="#offcanvas" data-bs-toggle="offcanvas"
                                                    data-bs-target="#offcanvasRightTimeLine{{ $item->id }}"
                                                    aria-controls="offcanvasRight">
                                                    <button class="withdraw-counter btn btn-info">
                                                        {{ isset($hours) ? sprintf('%02d', $hours) : '00' }}:{{
                                                        isset($minutes) ? sprintf('%02d', $minutes) : '00' }}:{{
                                                        isset($seconds) ? sprintf('%02d', $seconds) : '00' }}
                                                    </button>
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

                                                            <div class="card-heaer-cus" style="margin: 20px 0;">
                                                                <div class="h5">TimeLine
                                                                </div>
                                                            </div>
                                                            <ul class="list-group list-group-flush">

                                                                <li
                                                                    class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                                                    <h6 class="mb-0">CreatedBy
                                                                    </h6>
                                                                    <span class="text-white">{{
                                                                        isset($item->approvalTimeLine->createdBy) ?
                                                                        $item->approvalTimeLine->createdBy['name'] : ''
                                                                        }}</span>
                                                                </li>
                                                                <li
                                                                    class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                                                    <h6 class="mb-0">Created At
                                                                    </h6>
                                                                    <span class="text-white">{{
                                                                        isset($item->approvalTimeLine->created_at) ?
                                                                        $item->approvalTimeLine->created_at : ''
                                                                        }}</span>
                                                                </li>
                                                                <li
                                                                    class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                                                    <h6 class="mb-0">
                                                                        Admin User</h6>
                                                                    <span class="text-white">{{
                                                                        isset($item->approvalTimeLine->adminUser) ?
                                                                        $item->approvalTimeLine->adminUser['name'] : '-'
                                                                        }}</span>
                                                                </li>
                                                                <li
                                                                    class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                                                    <h6 class="mb-0">Admin Update
                                                                    </h6>
                                                                    <span class="text-white">{{
                                                                        isset($timeTakenFromBankerToAdmin) ?
                                                                        gmdate('H:i:s', $timeTakenFromBankerToAdmin) :
                                                                        'Pending' }}</span>
                                                                </li>
                                                                <li
                                                                    class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                                                    <h6 class="mb-0">Banker
                                                                    </h6>
                                                                    <span class="text-white">{{
                                                                        isset($item->approvalTimeLine->bankerUser) ?
                                                                        $item->approvalTimeLine->bankerUser['name'] :
                                                                        '-' }}</span>
                                                                </li>
                                                                <li
                                                                    class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                                                    <h6 class="mb-0">Banker Update
                                                                    </h6>
                                                                    <span class="text-white">{{
                                                                        isset($timeTakenFromadminTimeToBanker) ?
                                                                        gmdate('H:i:s', $timeTakenFromadminTimeToBanker)
                                                                        : 'Pending' }}</span>
                                                                </li>

                                                                <li
                                                                    class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                                                    <h6 class="mb-0">
                                                                        CustomerCare User
                                                                    </h6>
                                                                    <span class="text-white">{{
                                                                        isset($item->approvalTimeLine->ccUser) ?
                                                                        $item->approvalTimeLine->ccUser['name'] : '-'
                                                                        }}</span>
                                                                </li>
                                                                <li
                                                                    class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                                                    <h6 class="mb-0">Customer Care Update
                                                                    </h6>
                                                                    <span class="text-white">{{
                                                                        isset($timeTakenFromAdminToCC) ? gmdate('H:i:s',
                                                                        $timeTakenFromAdminToCC) : 'Pending' }}</span>
                                                                </li>
                                                                <li
                                                                    class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                                                    <h6 class="mb-0">Status</h6>
                                                                    <span class="text-white">{{
                                                                        isset($item->approvalTimeLine->status) ?
                                                                        $item->approvalTimeLine->status : '' }}</span>
                                                                </li>

                                                            </ul>
                                                        </div>
                                                    </div>
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
                                                <img src="{{ asset('storage/' . $item->image) }}" alt="Image"
                                                    style="width: 20%;">
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
                                                                                alt="Image" style="width: 100%;">

                                                                        </div>

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
                                                    id="withdraw_admin" name="withdraw_admin" @if ($item->banker_status
                                                    === 'Verified') disabled @else enabled @endif
                                                    @cannot('Withdraw Admin Enable') disabled @endcan required>
                                                    <option value="Pending" @if ($item->admin_status === 'Pending')
                                                        selected @endif>Pending
                                                    </option>
                                                    <option value="Verified" @if ($item->admin_status === 'Verified')
                                                        selected @endif>Verified
                                                    </option>
                                                    <option value="Rejected" @if ($item->admin_status === 'Rejected')
                                                        selected @endif>Rejected
                                                    </option>
                                                </select>

                                            </div>
                                            <!-- Admin Reject Verify Modal -->
                                            <div class="modal fade" id="rejectModal{{ $item->id }}" tabindex="-1"
                                                aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                <input type="hidden" id="new_withdraw_reject_id" class="form-control"
                                                    value="{{ $item->id }}">
                                                <input type="hidden" id="new_platform_id" class="form-control"
                                                    value="{{ $item->platformDetail->platform_id }}">
                                                <div class="modal-dialog modal-lg">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="exampleModalLabel">
                                                                Reject Withdraw</h5>
                                                            <button type="button" class="btn-close"
                                                                data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                    <div class="title">
                                                                        Remarks
                                                                    </div>
                                                                    <div class="content">
                                                                        <textarea name="reject_remark"
                                                                            class="form-control" id="reject_remark"
                                                                            cols="10" rows="5"></textarea>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" id="confirm_admin_verify"
                                                                class="btn btn-primary confirm_admin_reject">Confirm</button>
                                                            <button type="button" id="close_model"
                                                                class="btn btn-secondary close_model"
                                                                data-bs-dismiss="modal">Close1</button>
                                                            <span id="verify_error" class="error-message"
                                                                style="color:red"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="withdraw_status">
                                            <div>
                                                <select style="width: 110px; text-align: center;"
                                                    class="form-control rolling-type-select withdraw_banker"
                                                    id="withdraw_banker" name="withdraw_banker" @if ($item->admin_status
                                                    === 'Verified') enabled @else disabled @endif
                                                    @cannot('Withdraw Banker Enable') disabled @endcan required>
                                                    <option value="Pending" @if ($item->banker_status === 'Pending')
                                                        selected @endif>Pending
                                                    </option>
                                                    <option value="Not Verified" @if ($item->banker_status === 'Not
                                                        Verified') selected @endif>
                                                        Not
                                                        Verified</option>
                                                    <option value="Verified" @if ($item->banker_status === 'Verified')
                                                        selected @endif>Verified
                                                    </option>
                                                    <option value="Rejected" @if ($item->banker_status === 'Rejected')
                                                        selected @endif>Rejected
                                                    </option>
                                                </select>

                                            </div>
                                            <!-- Admin Withdraw Verify Modal -->
                                            <div class="modal fade" id="adminWithdrawVerifyModal{{ $item->id }}"
                                                tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
                                                                data-bs-dismiss="modal" aria-label="Close"></button>
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
                                                                                <td>{{
                                                                                    $item->platformDetail->platform_username
                                                                                    ??
                                                                                    $item->platformDetail->player->name
                                                                                    }}
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>Platform:</td>
                                                                                <td>{{
                                                                                    $item->platformDetail->platform->name
                                                                                    }}
                                                                                </td>
                                                                            </tr>

                                                                            <tr>
                                                                                <td>Bonus Eligible:</td>

                                                                                <td>
                                                                                    @if ($item->is_bonus_eligible == 1)
                                                                                    <span class="badge bg-success">Yes
                                                                                    </span>

                                                                                    @else
                                                                                    <span class="badge bg-danger">No
                                                                                    </span>
                                                                                    @endif
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>Withdraw Amount:</td>
                                                                                <td><span id="deposit_amount">{{
                                                                                        $item->amount }}</span>
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
                                                                            <span id="current_deposit_amount">{{
                                                                                $item->amount }}</span>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row">
                                                                        <div class="col-md-6">
                                                                            <input type="checkbox"
                                                                                name="rollover_complete"
                                                                                id="rollover_complete"
                                                                                class="rollover_complete"
                                                                                value="is Rollover Completed">
                                                                            <label>Is Rollover Completed? <span
                                                                                    class="mandatory-field">*</span></label>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row">
                                                                        <div class="col-md-6">Deduction Chips</div>
                                                                        <div class="col-md-6">
                                                                            <input type="text" id="deduction-chips"
                                                                                name="deduction-chips"
                                                                                class="bonus-editor form-control"
                                                                                value="">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <span id="confirm-message" class="confirm-message">
                                                                    Enter Total Withdraw Amount <input type="text"
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
                                            <div class="modal fade" id="bankerWithdrawVerifyModal{{ $item->id }}"
                                                tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                <input type="hidden" id="new_withdraw_id" class="form-control"
                                                    value="{{ $item->id }}">
                                                <div class="modal-dialog modal-lg">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="exampleModalLabel">
                                                                Verify Withdraw</h5>
                                                            <button type="button" class="btn-close"
                                                                data-bs-dismiss="modal" aria-label="Close"></button>
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
                                                                                <td>{{
                                                                                    $item->platformDetail->platform_username
                                                                                    ??
                                                                                    $item->platformDetail->player->name
                                                                                    }}
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>Platform:</td>
                                                                                <td>{{
                                                                                    $item->platformDetail->platform->name
                                                                                    }}
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>Withdraw Amount:</td>
                                                                                <td><span id="deposit_amount">{{
                                                                                        $item->amount }}</span>
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
                                                                                    @cannot('Withdraw Banker Enable')
                                                                                    disabled @endcan required>
                                                                                    <option value="Pending"
                                                                                        ___inline_directive__________________________5___>
                                                                                        Pending
                                                                                    </option>
                                                                                    <option value="Not Verified"
                                                                                        ___inline_directive__________________________6___>
                                                                                        Not
                                                                                        Verified</option>
                                                                                    <option value="Verified"
                                                                                        ___inline_directive__________________________7___>
                                                                                        Verified
                                                                                    </option>
                                                                                    <option value="Rejected"
                                                                                        ___inline_directive___________________________8___>
                                                                                        Rejected
                                                                                    </option>
                                                                                    </select>

                                                                    </div>
                                                                    <!-- Admin Withdraw Verify Modal -->
                                                                    <div class="modal fade"
                                                                        id="adminWithdrawVerifyModal{{ $item->id }}"
                                                                        tabindex="-1"
                                                                        aria-labelledby="exampleModalLabel"
                                                                        aria-hidden="true">
                                                                        <input type="hidden" id="new_deposit_id"
                                                                            class="form-control"
                                                                            value="{{ $item->id }}">
                                                                        <input type="hidden" id="new_platform_id"
                                                                            class="form-control"
                                                                            value="{{ $item->platformDetail->platform_id }}">
                                                                        <div class="modal-dialog modal-lg">
                                                                            <div class="modal-content">
                                                                                <div class="modal-header">
                                                                                    <h5 class="modal-title"
                                                                                        id="exampleModalLabel">
                                                                                        Verify Withdraw</h5>
                                                                                    <button type="button"
                                                                                        class="btn-close"
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
                                                                                                <table
                                                                                                    class="modal-table">
                                                                                                    <tr>
                                                                                                        <td>Name:</td>
                                                                                                        <td>{{
                                                                                                            $item->platformDetail->platform_username
                                                                                                            ??
                                                                                                            $item->platformDetail->player->name
                                                                                                            }}
                                                                                                        </td>
                                                                                                    </tr>
                                                                                                    <tr>
                                                                                                        <td>Platform:
                                                                                                        </td>
                                                                                                        <td>{{
                                                                                                            $item->platformDetail->platform->name
                                                                                                            }}
                                                                                                        </td>
                                                                                                    </tr>

                                                                                                    <tr>
                                                                                                        <td>Bonus
                                                                                                            Eligible:
                                                                                                        </td>

                                                                                                        <td>
                                                                                                            @if
                                                                                                            ($item->is_bonus_eligible
                                                                                                            == 1)
                                                                                                            <span
                                                                                                                class="badge bg-success">Yes
                                                                                                            </span>

                                                                                                            @else
                                                                                                            <span
                                                                                                                class="badge bg-danger">No
                                                                                                            </span>
                                                                                                            @endif
                                                                                                        </td>
                                                                                                    </tr>
                                                                                                    <tr>
                                                                                                        <td>Withdraw
                                                                                                            Amount:</td>
                                                                                                        <td><span
                                                                                                                id="deposit_amount">{{
                                                                                                                $item->amount
                                                                                                                }}</span>
                                                                                                        </td>
                                                                                                    </tr>
                                                                                                    <tr>
                                                                                                        <td>Bank Detail:
                                                                                                        </td>
                                                                                                        <td>
                                                                                                            @if
                                                                                                            ($item->bank->upi)
                                                                                                            {{
                                                                                                            $item->bank->upi
                                                                                                            }}
                                                                                                            @else
                                                                                                            {{
                                                                                                            $item->bank->account_number
                                                                                                            }},
                                                                                                            {{
                                                                                                            $item->bank->bank_name
                                                                                                            }},
                                                                                                            {{
                                                                                                            $item->bank->ifsc_code
                                                                                                            }}
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
                                                                                                <div class="col-md-6">
                                                                                                    Withdraw Amount
                                                                                                </div>
                                                                                                <div class="col-md-6">
                                                                                                    <span
                                                                                                        id="current_deposit_amount">{{
                                                                                                        $item->amount
                                                                                                        }}</span>
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="row">
                                                                                                <div class="col-md-6">
                                                                                                    <input
                                                                                                        type="checkbox"
                                                                                                        name="rollover_complete"
                                                                                                        id="rollover_complete"
                                                                                                        class="rollover_complete"
                                                                                                        value="is Rollover Completed">
                                                                                                    <label>Is Rollover
                                                                                                        Completed? <span
                                                                                                            class="mandatory-field">*</span></label>
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="row">
                                                                                                <div class="col-md-6">
                                                                                                    Deduction Chips
                                                                                                </div>
                                                                                                <div class="col-md-6">
                                                                                                    <input type="text"
                                                                                                        id="deduction-chips"
                                                                                                        name="deduction-chips"
                                                                                                        class="bonus-editor form-control"
                                                                                                        value="">
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="modal-footer">
                                                                                        <span id="confirm-message"
                                                                                            class="confirm-message">
                                                                                            Enter Total Withdraw Amount
                                                                                            <input type="text"
                                                                                                name="confirm_total_deposit_amount"
                                                                                                id="confirm_total_deposit_amount">
                                                                                        </span>
                                                                                        <span
                                                                                            class="mandatory-field">*</span>
                                                                                        <button type="button"
                                                                                            id="confirm_admin_verify"
                                                                                            class="btn btn-primary confirm_admin_verify">Confirm</button>
                                                                                        <button type="button"
                                                                                            id="close_model"
                                                                                            class="btn btn-secondary close_model"
                                                                                            data-bs-dismiss="modal">Close</button>
                                                                                        <span id="verify_error"
                                                                                            class="error-message"
                                                                                            style="color:red"></span>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <!-- Banker Withdraw Verify Modal -->
                                                                    <div class="modal fade"
                                                                        id="bankerWithdrawVerifyModal{{ $item->id }}"
                                                                        tabindex="-1"
                                                                        aria-labelledby="exampleModalLabel"
                                                                        aria-hidden="true">
                                                                        <input type="hidden" id="new_withdraw_id"
                                                                            class="form-control"
                                                                            value="{{ $item->id }}">
                                                                        <div class="modal-dialog modal-lg">
                                                                            <div class="modal-content">
                                                                                <div class="modal-header">
                                                                                    <h5 class="modal-title"
                                                                                        id="exampleModalLabel">
                                                                                        Verify Withdraw</h5>
                                                                                    <button type="button"
                                                                                        class="btn-close"
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
                                                                                                <table
                                                                                                    class="modal-table">
                                                                                                    <tr>
                                                                                                        <td>Name:</td>
                                                                                                        <td>{{
                                                                                                            $item->platformDetail->platform_username
                                                                                                            ??
                                                                                                            $item->platformDetail->player->name
                                                                                                            }}
                                                                                                        </td>
                                                                                                    </tr>
                                                                                                    <tr>
                                                                                                        <td>Platform:
                                                                                                        </td>
                                                                                                        <td>{{
                                                                                                            $item->platformDetail->platform->name
                                                                                                            }}
                                                                                                        </td>
                                                                                                    </tr>
                                                                                                    <tr>
                                                                                                        <td>Withdraw
                                                                                                            Amount:</td>
                                                                                                        <td><span
                                                                                                                id="deposit_amount">{{
                                                                                                                $item->amount
                                                                                                                }}</span>
                                                                                                        </td>
                                                                                                    </tr>
                                                                                                    <tr>
                                                                                                        <td>Bank Detail:
                                                                                                        </td>
                                                                                                        <td>
                                                                                                            @if
                                                                                                            ($item->bank->upi)
                                                                                                            {{
                                                                                                            $item->bank->upi
                                                                                                            }}
                                                                                                            @else
                                                                                                            {{
                                                                                                            $item->bank->account_number
                                                                                                            }},
                                                                                                            {{
                                                                                                            $item->bank->bank_name
                                                                                                            }},
                                                                                                            {{
                                                                                                            $item->bank->ifsc_code
                                                                                                            }}
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
                                                                                                <div class="col-md-6">
                                                                                                    Withdraw Amount
                                                                                                </div>
                                                                                                <div class="col-md-6">
                                                                                                    <span
                                                                                                        id="current_withdraw_amount">{{
                                                                                                        $item->amount
                                                                                                        }}</span>
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="row">
                                                                                                <div class="col-md-6">
                                                                                                    <label
                                                                                                        for="file-input"
                                                                                                        class="drop-area">
                                                                                                        <p>Click, drag
                                                                                                            and drop, or
                                                                                                            paste an
                                                                                                            image file
                                                                                                            here <span
                                                                                                                class="mandator-field">*</span>
                                                                                                        </p>
                                                                                                        <div
                                                                                                            id="image-preview">
                                                                                                            <img id="image"
                                                                                                                src="#"
                                                                                                                alt="Image Preview">
                                                                                                        </div>
                                                                                                    </label>

                                                                                                    <input type="file"
                                                                                                        class="file-input"
                                                                                                        accept="image/*">
                                                                                                </div>
                                                                                            </div>

                                                                                            <div class="row mt-3">
                                                                                                <div
                                                                                                    class="col-md-6 pt-2">
                                                                                                    Select Debit Bank
                                                                                                </div>
                                                                                                <div class="col-md-6">
                                                                                                    <select
                                                                                                        class="form-control select2"
                                                                                                        required
                                                                                                        name="our_bank_detail"
                                                                                                        id="our_bank_detail">
                                                                                                        <option
                                                                                                            value="">
                                                                                                            Choose
                                                                                                            platform
                                                                                                        </option>
                                                                                                        @foreach
                                                                                                        ($ourbank as
                                                                                                        $key => $value)
                                                                                                        <option
                                                                                                            value="{{ $value->id }}">
                                                                                                            {{
                                                                                                            $value->bank_name
                                                                                                            }}
                                                                                                        </option>
                                                                                                        @endforeach
                                                                                                    </select>
                                                                                                    <span
                                                                                                        id="bank_error"
                                                                                                        style="color: red"></span>
                                                                                                </div>
                                                                                            </div>

                                                                                            <div class="row mt-3">
                                                                                                <div
                                                                                                    class="col-md-6 pt-2">
                                                                                                    Withdraw UTR</div>
                                                                                                <div class="col-md-6">
                                                                                                    <input type="text"
                                                                                                        class="form-control"
                                                                                                        id="withdraw_utr">
                                                                                                    <span
                                                                                                        id="validateError"
                                                                                                        style="color: red"></span>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="modal-footer">
                                                                                    <span id="withdraw-modal-error"
                                                                                        class="error-message"
                                                                                        style="color:red"></span>
                                                                                    <span id="confirm-message"
                                                                                        class="confirm-message">
                                                                                        Enter Total Witdraw Amount
                                                                                        <input type="text"
                                                                                            name="confirm_total_withdraw_amount"
                                                                                            id="confirm_total_withdraw_amount">


                                                                                    </span>
                                                                                    <button type="button"
                                                                                        id="confirm_banker_verify"
                                                                                        class="btn btn-primary confirm_banker_verify">Confirm</button>
                                                                                    <button type="button"
                                                                                        id="close_model"
                                                                                        class="btn btn-secondary close_model"
                                                                                        data-bs-dismiss="modal">Close</button>
                                                                                    <span id="verify_error"
                                                                                        class="error-message"
                                                                                        style="color:red"></span>
                                                                                </div>

                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <!-- Banker Reject Verify Modal -->
                                                                    <div class="modal fade"
                                                                        id="rejectBankerModal{{ $item->id }}"
                                                                        tabindex="-1"
                                                                        aria-labelledby="exampleModalLabel"
                                                                        aria-hidden="true">
                                                                        <input type="hidden"
                                                                            id="new_withdraw_banker_reject_id"
                                                                            class="form-control"
                                                                            value="{{ $item->id }}">
                                                                        <input type="hidden" id="new_platform_id"
                                                                            class="form-control"
                                                                            value="{{ $item->platformDetail->platform_id }}">
                                                                        <div class="modal-dialog modal-lg">
                                                                            <div class="modal-content">
                                                                                <div class="modal-header">
                                                                                    <h5 class="modal-title"
                                                                                        id="exampleModalLabel">
                                                                                        Reject Withdraw</h5>
                                                                                    <button type="button"
                                                                                        class="btn-close"
                                                                                        data-bs-dismiss="modal"
                                                                                        aria-label="Close"></button>
                                                                                </div>
                                                                                <div class="modal-body">
                                                                                    <div class="row">
                                                                                        <div class="col-md-12">
                                                                                            <div class="title">
                                                                                                Remarks
                                                                                            </div>
                                                                                            <div class="content">
                                                                                                <textarea
                                                                                                    name="reject_banker_remark"
                                                                                                    class="form-control"
                                                                                                    id="reject_banker_remark"
                                                                                                    cols="10"
                                                                                                    rows="5"></textarea>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="modal-footer">
                                                                                    <button type="button"
                                                                                        id="confirm_banker_verify"
                                                                                        class="btn btn-primary confirm_banker_reject">Confirm</button>
                                                                                    <button type="button"
                                                                                        id="close_model"
                                                                                        class="btn btn-secondary close_model"
                                                                                        data-bs-dismiss="modal">Close</button>
                                                                                    <span id="verify_error"
                                                                                        class="error-message"
                                                                                        style="color:red"></span>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <!-- userdetail canva  -->
                            @endcan
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
                } else if (selectedValue === "Rejected") {
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
                var modalId = $(this).closest('.modal').attr('id');
                var deposit_amount = $('#' + modalId + ' #current_deposit_amount').html();
                var dchips_amount = $('#' + modalId + ' #deduction-chips').val();
                // alert(dchips_amount);
                var verify_amount = $('#' + modalId + ' #confirm_total_deposit_amount').val();
                var remark = $('#' + modalId + ' #deposit_admin_remarks').val();
                console.log(modalId);
                var withdraw_amt = deposit_amount - dchips_amount;
                // alert(withdraw_amt);

                if (!$('#' + modalId + ' #rollover_complete').is(':checked')) {
                    $('#' + modalId + ' #withdraw-modal-error').html("Need to Fill the required(*) fields")
                        .css("display", "block");
                    displayError($('#' + modalId + ' #withdraw-modal-error'),
                        "Need to Fill the required(*) fields");
                    console.log("rollover not checked");
                    return false;
                } else {
                    var rollover = "Yes";
                }
                console.log(modalId);
                if (verify_amount == "") {
                    displayError($('#' + modalId + ' #withdraw-modal-error'),
                        "Need to Fill the required(*) fields");
                    return false;
                }
                if (deposit_amount == verify_amount) {
                    console.log("amount Equal");
                } else {
                    displayError($('#' + modalId + ' #withdraw-modal-error'),
                        "Validation amount is not equal");
                    console.log("amount Not Equal");
                    return false;
                }
                console.log(modalId);
                var selectedValue = "Verified";
                var rowId = $('#' + modalId + ' #new_deposit_id').val();
                console.log(rowId);
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
                        d_chips: d_chips,
                        withdraw_amt: withdraw_amt
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
            $('.confirm_banker_reject').click(function() {

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

                    return false;
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
                        }
                    },
                    error: function(xhr) {
                        // Handle the error if needed
                        console.log(xhr.responseText);
                    }
                });
            });

            $(document).on('click', '.confirm_banker_verify', function() {

                var imagePath = "";
                console.log("inside confirm_banker_verify");
                // var modalId = $(this).closest('.modal').attr('id');
                var modalId = $('.modal.show').attr('id');

                // $('#' + modalId).css('background', 'red');
                console.log(modalId);
                if (modalId) {
                    console.log(modalId);
                    console.log("modal founded");

                } else {
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

                if (verify_amount == "") {
                    displayError($('#' + modalId + '#verify_error'), "Confirm Amount");
                    return false;
                }
                if (withdraw_amount == verify_amount) {
                    console.log("amount Equal");
                } else {
                    console.log("amount Not Equal");
                    return false;
                }
                console.log(modalId);
                var selectedValue = "Verified";
                var rowId = $('#' + modalId + ' #new_withdraw_id').val();
                console.log(rowId);
                var type = "withdraw_banker";

                formData.append("type", type);
                formData.append("userid", rowId);
                formData.append("selectedValue", selectedValue);
                formData.append("withdrawUtr", withdraw_utr);
                formData.append("ourBankDetail", bank_detail);
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
                        // Handle the response if needed
                        console.log("adminStatus Response");
                        response = response[0];
                        if (response.flag == 1) {
                            displayMessage($(".custom-alert"), "Success",
                                "Status Updated Successfully");
                            // var selectElement = $('.banker_admin');
                            // selectElement.val('Verified');
                            $(".close_model").click();
                        } else if (response.flag == 0) {
                            console.log("UTR Number is Exist");
                            $("#validateError").text("UTR Number is Exist");
                            setTimeout(function() {
                                $("#validateError").text(""); // Clear the text
                            }, 5000);
                        }

                    },
                    error: function(xhr) {
                        // Handle the error if needed
                        console.log(xhr.responseText);
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
                var reader = new FileReader();

                reader.onload = function(e) {
                    $('#' + modalId + ' #image').attr('src', e.target.result);
                    $('#' + modalId + ' #image-preview').show();
                };

                reader.readAsDataURL(file);
            }
        });


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
