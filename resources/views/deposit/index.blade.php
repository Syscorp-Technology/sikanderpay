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
                                            <th>Deposit Amount</th>
                                            <th>Bonus Eligible</th>

                                            <th>Bonus</th>
                                            <th>Total Amount</th>
                                            <th>Image</th>
                                            <th>Admin</th>
                                            <th>UTR Details</th>
                                            <th>Banker</th>
                                            <th>Created By</th>
                                            {{-- <th>Action</th> --}}

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


                                                    $timeTakenFromcreatedToBanker =$bankerTime? $bankerTime - $startTime:null; // Time taken from banker update to admin update
                                                    $timeTakenFromBankerToAdmin =$adminTime? $adminTime - $bankerTime:null; // Time taken from banker update to admin update
                                                    $timeTakenFromAdminToCC = $ccTime ? $ccTime - $adminTime : null; // Time taken from admin update to customer care update

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
                                                data-platformname="{{ $item->platformDetail->platform->name }}"
                                                data-playername="{{ $item->platformDetail->player->name }}">

                                                <td>{{ $item->id }}
                                                    @if (auth()->user()->id == $item->assigned_to)
                                                @if ($item->banker_status === 'Verified')
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input bg-success assigned_to" type="checkbox" id="flexSwitchCheckChecked" checked data-type="Admin">
                                                    </div>
                                                    @else
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input bg-success assigned_to" type="checkbox" id="flexSwitchCheckChecked" checked data-type="Banker">
                                                    </div>
                                                 @endif
                                                @elseif (auth()->user()->id !== $item->assigned_to && $item->assigned_to)
                                                    <div class="">
                                                        <span class="form-check-label badge rounded-pill bg-warning text-dark" for="flexSwitchCheckDefault">{{$item->assignedTo ? $item->assignedTo->name : ''}}</span>
                                                    </div>
                                                @else

                                                @if ($item->banker_status === 'Verified')
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input  assigned_to" type="checkbox" id="flexSwitchCheckDefault" @cannot('Deposit Admin Enable') disabled @endcan data-type="Admin">
                                                        {{-- <span class="form-check-label" for="flexSwitchCheckDefault">Pending</span> --}}
                                                    </div>
                                                @else
                                                   <div class="form-check form-switch">
                                                    <input class="form-check-input   assigned_to" type="checkbox" id="flexSwitchCheckDefault" @cannot('Deposit Banker Enable') disabled @endcan data-type="Banker">
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
                                                                                            <h6 class="mb-0">DOJ</h6>
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
                                                                                                class="text-white">{{ $item->platformDetail->player->leadSource->name ?? '-' }}</span>
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
                                                                    <div class="col-md-6 ">
                                                                        <div
                                                                            class="card border-top border-0 border-4 border-white">
                                                                            @can('Deposit Feedback')
                                                                                {{-- <div class="card-body p-5">
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
                                                                                        <textarea class="form-control" id="mytextarea_{{ $item->id }}" name="feedback"></textarea>
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
                                                                                                <button class="btn btn-primary"
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
                                                                                                <div class="col py-2">
                                                                                                    <div
                                                                                                        class="card radius-15">
                                                                                                        <div
                                                                                                            class="card-body d-flex flex-column">
                                                                                                            <h4
                                                                                                                class="card-title text-white">
                                                                                                                {!! $feedback->feedback !!}
                                                                                                            </h4>
                                                                                                            <div
                                                                                                                class="card-text d-flex flex-grow-1">
                                                                                                                <p
                                                                                                                    class="me-auto">
                                                                                                                    {{ $feedback->branchUser->name ?? '' }}
                                                                                                                </p>
                                                                                                                <p>
                                                                                                                    {{ $feedback->created_at }}
                                                                                                                </p>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>

                                                                                            </div>
                                                                                        @endforeach


                                                                                    </div>
                                                                                </div> --}}
                                                                            @endcan

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
                                                            <span class="deposit-counter badge rounded-pill bg-warning text-dark ">
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
                                                                                class="text-white">{{ isset($timeTakenFromcreatedToBanker) ?  gmdate("H:i:s", $timeTakenFromcreatedToBanker)  : 'Pending' }}</span>
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

                                                                    </ul>
                                                                </div>
                                                            </div>

                                                        </div>
                                                </td>
                                                <td>{{ $item->deposit_amount }}</td>
                                                <td>
                                                    @if ($item->is_bonus_eligible == 1)
                                                        <span class="badge bg-success">Yes</span>
                                                    @else
                                                        <span class="badge bg-danger">No</span>
                                                    @endif
                                                </td>
                                                <td>{{ $item->bonus }}</td>
                                                <td>{{ $item->total_deposit_amount }}</td>
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
                                                <td class="deposit_status">
                                                    <div>
                                                        <select style="width: 110px; text-align: center;"
                                                            class="form-control rolling-type-select deposit_admin"
                                                            id="deposit_admin" name="deposit_admin"
                                                            @if ($item->banker_status == 'Verified') enabled @else disabled @endif
                                                            @cannot('Deposit Admin Enable') disabled @endcan required>
                                                            <option value="Pending" ___inline_directive__________________________1___>Pending</option>
                                                            <option value="Not Verified" ___inline_directive__________________________2___ disabled>Not Verified</option>
                                                            <option value="Verified" ___inline_directive__________________________3___>Verified</option>
                                                            <option value="Rejected" ___inline_directive__________________________4___>Rejected</option>
                                                        </select>

                                                    </div>

                                                    <!-- Banker Verify Modal -->
                                                    <div class="modal fade" id="bankerVerifyModal{{ $item->id }}"
                                                        tabindex="-1" aria-labelledby="exampleModalLabel"
                                                        aria-hidden="true">
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

                                                                                <table class="modal-table">
                                                                                    <tr>
                                                                                        <td>User Name:</td>
                                                                                        <td>{{ $item->platformDetail->platform_username ?? $item->platformDetail->player->name }}</td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td>Platform:</td>
                                                                                        <td>{{ $item->platformDetail->platform->name }}
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td>Deposit Amount:</td>
                                                                                        <td><span class="badge bg-warning text-dark fs-6">{{ $item->deposit_amount }}
                                                                                        </span></td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td>Bonus:</td>
                                                                                        <td>{{ $item->bonus }}</td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td>Total Amount:</td>
                                                                                        <td ><span class="badge bg-warning text-dark fs-6">{{ $item->total_deposit_amount }}</span>
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td>UTR:</td>
                                                                                        <td>{{ $item->utr }}</td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td>Our Bank Detail:</td>
                                                                                        <td>{{ $item->ourBankDetail->account_number }},
                                                                                            {{ $item->ourBankDetail->bank_name }}
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

                                                    </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <span id="confirm-message" class="confirm-message">
                                Do you confirm the amount <span class="amount">{{ $item->deposit_amount }}</span>
                                deposited?
                            </span>
                            <button type="button" id="confirm_banker_verify"
                                class="btn btn-primary confirm_banker_verify">Confirm</button>
                            <button type="button" id="close_model" class="btn btn-secondary close_model"
                                data-bs-dismiss="modal">Close</button>

                        </div>
                    </div>
                </div>
            </div>
            <!-- end -->
            <!-- Admin Exist Verify Modal -->
            <div class="modal fade adminExistVerifyModal" id="adminExistVerifyModal{{ $item->id }}" tabindex="-1"
                aria-labelledby="exampleModalLabel" aria-hidden="true">
                <input type="hidden" id="new_deposit_id" class="form-control" value="{{ $item->id }}">
                <input type="hidden" id="new_platform_id" class="form-control"
                    value="{{ $item->platformDetail->platform_id }}">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">
                                Verify Deposit</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="title">
                                        Deposit Details
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
                                                <td>Deposit Amount:</td>
                                                <td><span id="deposit_amount" class="badge bg-warning text-dark fs-6">{{ $item->deposit_amount }}</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Eligible To Bonus:</td>

                                                <td>
                                                    @if ($item->is_bonus_eligible == 1)
                                                        <span class="badge bg-success fs-6">Yes</span>
                                                    @else
                                                        <span class="badge bg-danger fs-6">No</span>
                                                    @endif
                                                </td>
                                            </tr>

                                            <tr>
                                                <td>Bonus<span class="mandatory-field">*</span>:</td>
                                                <td><input type="text" id="bonus-editor" class="bonus-editor form-control" value="{{ $item->bonus }}">
                                            </tr>
                                            <tr>
                                                <td>Total Amount:</td>
                                                <td><span
                                                        id="total_deposit_amount" class="badge bg-warning text-dark fs-6">{{ $item->total_deposit_amount }}</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>UTR:</td>
                                                <td>{{ $item->utr }}</td>
                                            </tr>
                                            <tr>
                                                <td>Our Bank Detail:</td>
                                                <td>{{ $item->ourBankDetail->account_number }},
                                                    {{ $item->ourBankDetail->bank_name }}
                                                </td>

                                            </tr>
                                        </table>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="title">
                                        Deposit Confirm
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">Deposit Amount</div>
                                        <div class="col-md-6">
                                            <span id="current_deposit_amount" class="badge bg-warning text-dark">{{ $item->deposit_amount }}</span>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <input type="checkbox" name="bonus_eligibility" id="bonus_eligibility"
                                                class="bonus_eligibility" value="is bonus eligibility">
                                            <label>Is
                                                Eligible for Bonus?</label>
                                        </div>

                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            Remarks:
                                            <textarea name="deposit_admin_remarks" class="form-control" id="deposit_admin_remarks" cols="30"
                                                rows="10"></textarea>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <span id="existing_deposit_modal_error" class="error-message" style="color:red"></span>
                            <span id="confirm-message" class="confirm-message">
                                Enter Total Deposit Amount  <input type="text" name="confirm_total_deposit_amount"
                                    id="confirm_total_deposit_amount">
                            </span>
                            <span class="mandatory-field">*</span>
                            <button type="button" id="confirm_admin_verify"
                                class="btn btn-primary confirm_admin_verify">Confirm</button>
                            <button type="button" id="close_model" class="btn btn-secondary close_model"
                                data-bs-dismiss="modal">Close</button>
                            <span id="verify_error" class="error-message" style="color:red"></span>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Banker Exist Reject Modal -->
            <div class="modal fade" id="rejectBankerModal{{ $item->id }}" tabindex="-1"
                aria-labelledby="exampleModalLabel" aria-hidden="true">
                <input type="hidden" id="new_deposit_banker_reject_id" class="form-control" value="{{ $item->id }}">
                <input type="hidden" id="new_platform_id" class="form-control"
                    value="{{ $item->platformDetail->platform_id }}">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">
                                Reject Deposit</h5>
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
                            <button type="button" id="confirm_banker_reject"
                                class="btn btn-primary confirm_banker_reject">Confirm</button>
                            <button type="button" id="close_model" class="btn btn-secondary close_model"
                                data-bs-dismiss="modal">Close</button>
                            <span id="verify_error" class="error-message" style="color:red"></span>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Admin Exist Verify Modal -->
            <div class="modal fade" id="rejectModal{{ $item->id }}" tabindex="-1"
                aria-labelledby="exampleModalLabel" aria-hidden="true">
                <input type="hidden" id="new_deposit_reject_id" class="form-control" value="{{ $item->id }}">
                <input type="hidden" id="new_platform_id" class="form-control"
                    value="{{ $item->platformDetail->platform_id }}">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">
                                Reject Deposit</h5>
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
            <!-- Verified Modal -->
            <div class="modal fade adminNewVerifyModal" id="adminNewVerifyModal{{ $item->id }}" tabindex="-1" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <input type="hidden" id="new_deposit_id" class="form-control" value="{{ $item->id }}">
                <input type="hidden" id="new_platform_id" class="form-control"
                    value="{{ $item->platformDetail->id }}">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">
                                    Verify Deposit</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="title">
                                           New Platform Creation
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
                                                    <td>Deposit Amount:</td>
                                                    <td><span id="deposit_amount" class="badge bg-warning text-dark fs-6" >{{ $item->deposit_amount }}</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Eligible To Bonus:</td>

                                                    <td>
                                                        @if ($item->is_bonus_eligible == 1)
                                                            <span class="badge bg-success fs-6">Yes</span>
                                                        @else
                                                            <span class="badge bg-danger fs-6">No</span>
                                                        @endif
                                                            </td>
                                            </tr>
                                            <tr>
                                                <td>Bonus <span class="mandatory-field">*</span>:</td>
                                                <td><input type="text" id="bonus-editor"
                                                        class="bonus-editor form-control" value="{{ $item->bonus }}">
                                            </tr>
                                            <tr>
                                                <td>Total Amount:</td>
                                                <td><span
                                                        id="total_deposit_amount" class="badge bg-warning text-dark fs-6">{{ $item->total_deposit_amount }}</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>UTR:</td>
                                                <td>{{ $item->utr }}</td>
                                            </tr>
                                            <tr>
                                                <td>Our Bank Detail:</td>
                                                <td>{{ $item->ourBankDetail->account_number }},
                                                    {{ $item->ourBankDetail->bank_name }}
                                                </td>

                                            </tr>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="title">
                                Deposit Confirm
                            </div>
                            <div class="row">
                                <div class="col-md-6">Deposit Amount</div>
                                <div class="col-md-6">
                                    <span class="badge bg-warning text-dark fs-6" id="current_deposit_amount">{{ $item->deposit_amount }}</span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <input type="checkbox" name="bonus_eligibility" id="bonus_eligibility"
                                        class="bonus_eligibility" value="is bonus eligibility">
                                    <label>Is
                                        Eligible for Bonus?</label>
                                </div>

                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    Remarks:
                                    <textarea name="deposit_admin_remarks" class="form-control" id="deposit_admin_remarks" cols="30"
                                        rows="4"></textarea>
                                </div>
                            </div>
                            <div class="title">
                                Create Account
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="user-id" class="col-form-label">User ID</label>
                                </div>
                                <div class="col-md-8 mb-2">
                                    <input type="text" class="form-control"
                                        value="{{ $item->platformDetail->platform_username }}" id="user-id">
                                    <span id="user-id-error" class="error-message" style="color:red"></span>
                                </div>
                                <div class="col-md-4">
                                    <label for="user-id" class="col-form-label">User
                                        Password</label>
                                </div>
                                <div class="col-md-8">
                                    <input type="text" class="form-control"
                                        value="{{ $item->platformDetail->platform_password }}" id="user-password">
                                    <span id="user-password-error" class="error-message" style="color:red"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <span id="existing_deposit_modal_error" class="error-message" style="color:red"></span>
                    <span id="confirm-message" class="confirm-message">
                        Enter Total Deposit Amount <input type="text" name="confirm_total_deposit_amount"
                            id="confirm_total_deposit_amount">
                    </span>
                    <span class="mandatory-field">*</span>
                    <button type="button" id="submit_platform_details"
                        class="btn btn-primary submit_platform_details">Confirm</button>
                    <button type="button" id="close_model" class="btn btn-secondary close_model"
                        data-bs-dismiss="modal">Close</button>
                    <span id="verify_error" class="error-message" style="color:red"></span>
                </div>
            </div>
        </div>
        </div>
        <!-- end -->
        </td>


        <td>{{ $item->utr }}</td>
        <td>
            <div>
                {{--  @dd($item->banker_status);  --}}
                <select style="width: 110px; text-align: center;" class="form-control rolling-type-select banker_admin"
                    id="banker_admin" name="banker_admin" @if ($item->admin_status === 'Verified') disabled @else enabled @endif
                    @cannot('Deposit Banker Enable') disabled @endcan required>
                    <option value="Pending" @if ($item->banker_status === 'Pending') selected @endif>Pending</option>
                    <option value="Verified" @if ($item->banker_status === 'Verified') selected @endif>Verified</option>
                    <option value="Rejected" @if ($item->banker_status === 'Rejected') selected @endif>Rejected</option>

                </select>
                <div class="mx-auto w-100">
                    <div class="modal fade " id="bankerRejectedModal{{ $item->id }}" tabindex="-1"
                        aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg" style="    --bs-modal-width: 700px;">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">
                                        Deposit Rejected </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-md-12">


                                            <form method="POST" action="{{ route('status_update', $item->id) }}">
                                                @csrf
                                                @method('PUT')

                                                <!-- Add a textarea field for the update -->
                                                <div class="mb-3 w-100">
                                                    <label for="updatedField">Updated Status</label>
                                                    <textarea class="form-control w-100" id="updatedField" name="remark" rows="4"></textarea>
                                                </div>

                                                <button type="submit" class="btn btn-primary">Update</button>
                                            </form>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
        </td>

        {{-- <td>

                                @can('Deposit Edit')
                                    <a href="{{ route('deposit.edit', $item->id) }}"><i
                                            class="fa-regular fa-pen-to-square"></i></a>
                                @endcan
                                @can('Deposit Delete')
                                    <a href="" data-id="{{ $item->id }}" data-bs-toggle="modal"
                                        data-bs-target="#exampleSmallModal{{ $item->id }}"><i
                                            class="fa-solid fa-trash-can"></i></a>
                                @endcan
                             </td> --}}
        <td> {{ $item->user->name ?? '' }}</td>


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
