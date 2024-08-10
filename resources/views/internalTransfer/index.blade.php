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
    <div id="loader" class="loader"></div>

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
                        <h6 class="mb-0 text-uppercase">Internal Transfer List</h6>
                    </div>

                    <div class="col-md-8 d-flex justify-content-end">

                        @can('InternalTransfer Add')
                            <a type="button" class="btn btn-light" href="{{ route('InternalTransfer.create') }}"
                                class="list-group-item">Add<i class="fa-solid fa-plus fs-18"></i></a>
                        @endcan

                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            @can('InternalTransfer Show')
                                <table id="example" class="table table-striped table-bordered" style="width: 100%">
                                    <thead>
                                        <tr>

                                            <th>ID</th>
                                            <th>Title</th>
                                            <th>Date</th>
                                            <th>From Bank</th>
                                            {{-- <th>Category</th> --}}
                                            {{-- <th>Payment Mode</th> --}}
                                            {{-- <th>Ref No</th> --}}
                                            <th>To Bank</th>
                                            <th>Amount</th>

                                            {{-- <th>ref No</th> --}}
                                            {{-- <th>banker Status</th> --}}
                                            {{-- <th>UTR Details</th> --}}
                                            <th>Supervisor</th>
                                            <th>Banker</th>
                                            <th>Created By</th>
                                            {{-- <th>Action</th> --}}

                                        </tr>
                                    </thead>
                                    <tbody id="deposit_tbody">
                                        @foreach ($internalTransfer as $key => $item)
                                            <tr data-id="{{ $item->id }}">
                                                <td>{{ $item->id }}</td>
                                                <td>

                                                    <a type="button" href="#a" data-bs-toggle="offcanvas"
                                                        data-bs-target="#offcanvasRight{{ $item->id }}"
                                                        aria-controls="offcanvasRight">{{ $item->title }}</a>

                                                    <div class="offcanvas offcanvas-end w-50" tabindex="-1"
                                                        id="offcanvasRight{{ $item->id }}"
                                                        aria-labelledby="offcanvasRightLabel">
                                                        <div class="offcanvas-header">

                                                            <button type="button" class="btn-close icon-close new-meth"
                                                                data-bs-dismiss="offcanvas" aria-label="Close"><i
                                                                    class="fa-regular fa-circle-xmark"></i></button>
                                                        </div>
                                                        <div class="offcanvas-body">
                                                            <ul class="list-group list-group-flush">
                                                                <li
                                                                    class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                                                    <h6 class="mb-0">From Bank</h6>
                                                                    <span
                                                                        class="badge bg-warning text-dark fs-6">{{ $item->bankFrom->bank_name }}</span>
                                                                </li>
                                                                <li
                                                                    class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                                                    <h6 class="mb-0">From Bank Account No</h6>
                                                                    <span
                                                                        class="text-white">{{ $item->bankFrom->account_number }}</span>
                                                                </li>


                                                                <li
                                                                    class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                                                    <h6 class="mb-0">To Bank</h6>
                                                                    <span
                                                                        class="badge bg-warning text-dark fs-6">{{ $item->bankTo->bank_name }}</span>
                                                                </li>
                                                                <li
                                                                    class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                                                    <h6 class="mb-0">To Bank Account No</h6>
                                                                    <span
                                                                        class="text-white">{{ $item->bankTo->account_number }}</span>
                                                                </li>

                                                                <li
                                                                    class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                                                    <h6 class="mb-0">Amount</h6>
                                                                    <span
                                                                        class="badge bg-warning text-dark fs-6">{{ $item->amount }}</span>
                                                                </li>

                                                                <li
                                                                    class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                                                    <h6 class="mb-0">Note</h6>
                                                                    <span class="text-white">{{ $item->remark }}</span>
                                                                </li>
                                                            </ul>

                                                        </div>
                                                    </div>


                                                </td>

                                                <td>{{ $item->date }}</td>
                                                {{-- <td>{{ $item->category->category_name }}</td> --}}

                                                {{-- <td>{{ $item->paymentMode->payment_mode_name }}</td> --}}
                                                {{-- <td>{{ $item->ref_no }}</td> --}}
                                                <td>{{ $item->bankFrom->bank_name }}</td>
                                                <td>{{ $item->bankTo->bank_name }}</td>
                                                <td>{{ $item->amount }}</td>




                                                <td>
                                                    <select style="width: 110px; text-align: center;"
                                                        class="form-control rolling-type-select superviser_status"  @cannot('Internal Transfer Superviser Enable') disabled @endcan
                                                        name="superviser_status" required>
                                                        <option value="Pending"
                                                            @if ($item->superviser_status === 'Pending') selected @endif>Pending
                                                        </option>
                                                        <option value="Verified"
                                                            @if ($item->superviser_status === 'Verified') selected @endif>Verified
                                                        </option>
                                                        <option value="Rejected"
                                                            @if ($item->superviser_status === 'Rejected') selected @endif>Rejected
                                                        </option>
                                                    </select>

                                                    <!-- Modal -->
                                                    <div class="modal fade" id="superviserModal{{ $item->id }}"
                                                        tabindex="-1" aria-labelledby="exampleModalLabel"
                                                        aria-hidden="true">
                                                        <div class="modal-dialog modal-lg">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h1 class="modal-title fs-5" id="exampleModalLabel">
                                                                        Superviser Approval</h1>
                                                                    <button type="button" class="btn-close"
                                                                        data-bs-dismiss="modal"
                                                                        aria-label="Close"></button>
                                                                </div>
                                                                <div class="modal-body">

                                                                    <div class="row">
                                                                        <ul class="list-group list-group-flush">
                                                                            <li
                                                                                class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                                                                <h6 class="mb-0">Title</h6>
                                                                                <span
                                                                                    class="text-white">{{ $item->title }}</span>
                                                                            </li>
                                                                            <li
                                                                                class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                                                                <h6 class="mb-0">Date</h6>
                                                                                <span
                                                                                    class="text-white">{{ $item->date }}</span>
                                                                            </li>
                                                                            <li
                                                                                class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                                                                <h6 class="mb-0">From Bank</h6>
                                                                                <span
                                                                                    class="text-white badge bg-success fs-6">{{ $item->bankFrom->bank_name }}</span>
                                                                            </li>
                                                                            <li
                                                                                class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                                                                <h6 class="mb-0">To Bank</h6>
                                                                                <span
                                                                                    class="text-white badge bg-success fs-6">{{ $item->bankTo->bank_name }}</span>
                                                                            </li>
                                                                            <li
                                                                                class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                                                                <h6 class="mb-0">Amount
                                                                                </h6>
                                                                                <span
                                                                                    class="badge bg-warning text-dark fs-6">{{ $item->amount }}</span>
                                                                            </li>


                                                                            <li
                                                                                class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                                                                <h6 class="mb-0">Created By</h6>
                                                                                <span
                                                                                    class="text-white">{{ $item->createdBy->name }}</span>
                                                                            </li>
                                                                            <li
                                                                                class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                                                                <h6 class="mb-0">Created At</h6>
                                                                                <span
                                                                                    class="text-white">{{ $item->created_at }}</span>
                                                                            </li>
                                                                            <li
                                                                                class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                                                                <h6 class="mb-0">Note</h6>
                                                                                <span
                                                                                    class="text-white">{{ $item->remark }}</span>
                                                                            </li>
                                                                        </ul>


                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary"
                                                                        data-bs-dismiss="modal">Close</button>
                                                                    <button type="button"
                                                                        class="btn btn-primary superviser_status_approval_conform">Confirm</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Modal -->
                                                    <div class="modal fade" id="superviserModalReject{{ $item->id }}"
                                                        tabindex="-1" aria-labelledby="exampleModalLabel"
                                                        aria-hidden="true">
                                                        <div class="modal-dialog modal-lg">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h1 class="modal-title fs-5" id="exampleModalLabel">
                                                                        Superviser Reject</h1>
                                                                    <button type="button" class="btn-close"
                                                                        data-bs-dismiss="modal"
                                                                        aria-label="Close"></button>
                                                                </div>
                                                                <div class="modal-body">

                                                                    <div class="content">
                                                                        <div class="row">
                                                                            <div class="col-6">
                                                                                <ul class="list-group list-group-flush">
                                                                                    <li
                                                                                        class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                                                                        <h6 class="mb-0">Title</h6>
                                                                                        <span
                                                                                            class="text-white">{{ $item->title }}</span>
                                                                                    </li>
                                                                                    <li
                                                                                        class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                                                                        <h6 class="mb-0">Date</h6>
                                                                                        <span
                                                                                            class="text-white">{{ $item->date }}</span>
                                                                                    </li>
                                                                                    <li
                                                                                        class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                                                                        <h6 class="mb-0">From Bank</h6>
                                                                                        <span
                                                                                            class="text-white badge bg-success fs-6 ">{{ $item->bankFrom->bank_name }}</span>
                                                                                    </li>
                                                                                    <li
                                                                                        class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                                                                        <h6 class="mb-0">To Bank</h6>
                                                                                        <span
                                                                                            class="text-white badge bg-success fs-6 ">{{ $item->bankTo->bank_name }}</span>
                                                                                    </li>
                                                                                    <li
                                                                                        class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                                                                        <h6 class="mb-0">Amount
                                                                                        </h6>
                                                                                        <span
                                                                                            class="badge bg-warning text-dark fs-6">{{ $item->amount }}</span>
                                                                                    </li>


                                                                                    <li
                                                                                        class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                                                                        <h6 class="mb-0">Created By</h6>
                                                                                        <span
                                                                                            class="text-white">{{ $item->createdBy->name }}</span>
                                                                                    </li>
                                                                                    <li
                                                                                        class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                                                                        <h6 class="mb-0">Created At</h6>
                                                                                        <span
                                                                                            class="text-white">{{ $item->created_at }}</span>
                                                                                    </li>
                                                                                    <li
                                                                                        class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                                                                        <h6 class="mb-0">Note</h6>
                                                                                        <span
                                                                                            class="text-white">{{ $item->remark }}</span>
                                                                                    </li>
                                                                                </ul>

                                                                            </div>
                                                                            <div class="col-6">
                                                                                <label for=""
                                                                                    class="form-label">Reason:<span
                                                                                    class="text-danger">*</span></label>
                                                                                <textarea class="form-control note" name="note" id="" cols="50" rows="10"></textarea>
                                                                                <span
                                                                                class="error-placeholder text-danger"
                                                                                id="remark_error{{ $item->id }}"></span>
                                                                            </div>
                                                                        </div>

                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary"
                                                                        data-bs-dismiss="modal">Close</button>
                                                                    <button type="button"
                                                                        class="btn btn-primary superviser_status_reject_conform">Confirm</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                </td>



                                                <td>
                                                    {{-- {{ $item->status }} --}}

                                                    <select style="width: 110px; text-align: center;"
                                                        class="form-control rolling-type-select banker_status"
                                                        name="banker_status"  @cannot('Internal Transfer Banker Enable') disabled @endcan
                                                        {{ $item->superviser_status == 'Verified' ? '' : 'disabled' }}
                                                        required>
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


                                                    <!-- Modal -->
                                                    <div class="modal fade" id="bankerModal{{ $item->id }}"
                                                        tabindex="-1" aria-labelledby="exampleModalLabel"
                                                        aria-hidden="true">
                                                        <div class="modal-dialog modal-lg">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h1 class="modal-title fs-5" id="exampleModalLabel">
                                                                        Banker Approval</h1>
                                                                    <button type="button" class="btn-close"
                                                                        data-bs-dismiss="modal"
                                                                        aria-label="Close"></button>
                                                                </div>
                                                                <div class="modal-body">

                                                                    <div class="content">
                                                                        <div class="row">
                                                                            <div class="col-6">
                                                                                <ul class="list-group list-group-flush">
                                                                                    <li
                                                                                        class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                                                                        <h6 class="mb-0">Title</h6>
                                                                                        <span
                                                                                            class="text-white">{{ $item->title }}</span>
                                                                                    </li>
                                                                                    <li
                                                                                        class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                                                                        <h6 class="mb-0">Date</h6>
                                                                                        <span
                                                                                            class="text-white">{{ $item->date }}</span>
                                                                                    </li>
                                                                                    <li
                                                                                        class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                                                                        <h6 class="mb-0">From Bank</h6>
                                                                                        <span
                                                                                            class="text-white badge bg-success fs-6 ">{{ $item->bankFrom->bank_name }}</span>
                                                                                    </li>
                                                                                    <li
                                                                                        class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                                                                        <h6 class="mb-0">To Bank</h6>
                                                                                        <span
                                                                                            class="text-white badge bg-success fs-6">{{ $item->bankTo->bank_name }}</span>
                                                                                    </li>
                                                                                    <li
                                                                                        class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                                                                        <h6 class="mb-0">Amount
                                                                                        </h6>
                                                                                        <span
                                                                                            class="badge bg-warning text-dark fs-6">{{ $item->amount }}</span>
                                                                                    </li>


                                                                                    <li
                                                                                        class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                                                                        <h6 class="mb-0">Created By</h6>
                                                                                        <span
                                                                                            class="text-white">{{ $item->createdBy->name }}</span>
                                                                                    </li>
                                                                                    <li
                                                                                        class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                                                                        <h6 class="mb-0">Created At</h6>
                                                                                        <span
                                                                                            class="text-white">{{ $item->created_at }}</span>
                                                                                    </li>
                                                                                    <li
                                                                                        class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                                                                        <h6 class="mb-0">Note</h6>
                                                                                        <span
                                                                                            class="text-white">{{ $item->remark }}</span>
                                                                                    </li>
                                                                                </ul>

                                                                            </div>
                                                                            <div class="col-6">

                                                                                <form action="">

                                                                                    <div class="row">
                                                                                        <div class="col-md-4 pt-2">
                                                                                            screenshot:</div>
                                                                                        <div class="col-md-8">

                                                                                            <label for="file-input"
                                                                                                class="drop-area">
                                                                                                <p>Click, drag and drop, or
                                                                                                    paste an
                                                                                                    image file here <span
                                                                                                        class="mandator-field">*</span>
                                                                                                </p>
                                                                                                <div id="image-preview">
                                                                                                    <img id="image"
                                                                                                        src="#"
                                                                                                        alt="Image Preview">
                                                                                                </div>
                                                                                            </label>

                                                                                            <input type="file"
                                                                                                class="file-input attachment"
                                                                                                accept="image/*">
                                                                                            <span
                                                                                                class="error-placeholder text-danger"
                                                                                                id="attachment_error{{ $item->id }}"></span>
                                                                                        </div>
                                                                                    </div>


                                                                                    <div class="row mt-3">
                                                                                        <div class="col-md-4 pt-2">UTR No:
                                                                                        </div>
                                                                                        <div class="col-md-8">
                                                                                            <input type="text"
                                                                                                class="form-control ref_no"
                                                                                                id="tr_utr">
                                                                                            <span id="validateError"
                                                                                                style="color: red"></span>
                                                                                            <span
                                                                                                class="error-placeholder text-danger"
                                                                                                id="no_balance{{ $item->id }}"></span>
                                                                                            <span
                                                                                                class="error-placeholder text-danger"
                                                                                                id="utr_error{{ $item->id }}"></span>
                                                                                        </div>


                                                                                    </div>


                                                                                </form>

                                                                            </div>
                                                                        </div>

                                                                    </div>

                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary"
                                                                        data-bs-dismiss="modal">Close</button>
                                                                    <button type="button"
                                                                        class="btn btn-primary banker_status_approval_conform">
                                                                        Confirm</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Modal -->
                                                    <div class="modal fade" id="bankerModalReject{{ $item->id }}"
                                                        tabindex="-1" aria-labelledby="exampleModalLabel"
                                                        aria-hidden="true">
                                                        <div class="modal-dialog modal-lg">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h1 class="modal-title fs-5" id="exampleModalLabel">
                                                                        Banker Reject</h1>
                                                                    <button type="button" class="btn-close"
                                                                        data-bs-dismiss="modal"
                                                                        aria-label="Close"></button>
                                                                </div>
                                                                <div class="modal-body">

                                                                    <div class="content">
                                                                        <div class="row">
                                                                            <div class="col-6">

                                                                                <ul class="list-group list-group-flush">
                                                                                    <li
                                                                                        class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                                                                        <h6 class="mb-0">Title</h6>
                                                                                        <span
                                                                                            class="text-white">{{ $item->title }}</span>
                                                                                    </li>
                                                                                    <li
                                                                                        class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                                                                        <h6 class="mb-0">Date</h6>
                                                                                        <span
                                                                                            class="text-white">{{ $item->date }}</span>
                                                                                    </li>
                                                                                    <li
                                                                                        class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                                                                        <h6 class="mb-0">From Bank</h6>
                                                                                        <span
                                                                                            class="text-white badge bg-success fs-6 ">{{ $item->bankFrom->bank_name }}</span>
                                                                                    </li>
                                                                                    <li
                                                                                        class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                                                                        <h6 class="mb-0">To Bank</h6>
                                                                                        <span
                                                                                            class="text-white badge bg-success fs-6">{{ $item->bankTo->bank_name }}</span>
                                                                                    </li>
                                                                                    <li
                                                                                        class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                                                                        <h6 class="mb-0">Amount
                                                                                        </h6>
                                                                                        <span
                                                                                            class="badge bg-warning text-dark fs-6">{{ $item->amount }}</span>
                                                                                    </li>


                                                                                    <li
                                                                                        class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                                                                        <h6 class="mb-0">Created By</h6>
                                                                                        <span
                                                                                            class="text-white">{{ $item->createdBy->name }}</span>
                                                                                    </li>
                                                                                    <li
                                                                                        class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                                                                        <h6 class="mb-0">Created At</h6>
                                                                                        <span
                                                                                            class="text-white">{{ $item->created_at }}</span>
                                                                                    </li>
                                                                                    <li
                                                                                        class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                                                                        <h6 class="mb-0">Note</h6>
                                                                                        <span
                                                                                            class="text-white">{{ $item->remark }}</span>
                                                                                    </li>
                                                                                </ul>

                                                                            </div>
                                                                            <div class="col-6">

                                                                                <label for=""
                                                                                    class="form-label">Reason:<span
                                                                                    class="text-danger">*</span></label>
                                                                                <textarea class="form-control note" name="note" id="" cols="50" rows="10"></textarea>
                                                                                <span
                                                                                class="error-placeholder text-danger"
                                                                                id="banker_remark_error{{ $item->id }}"></span>
                                                                            </div>

                                                                        </div>

                                                                    </div>

                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary"
                                                                        data-bs-dismiss="modal">Close</button>
                                                                    <button type="button"
                                                                        class="btn btn-primary banker_status_reject_conform">
                                                                        Confirm</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                </td>



                                                <td>{{ $item->createdBy->name }}</td>


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
        {{-- @include('layouts.footer') --}}
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


    <!-- canva end -->

    @include('layouts.user_script')

    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        var successAlertRoute = @json(route('success-alert'));
        // var adminStatus = "{{ url('admin_status') }}";
        // var platformDetailActive = "{{ url('platform_detail_active') }}";
        // var checkPlatformDetailExist = "{{ url('check_platform_detail_exist') }}";
    </script>
    <script>
        $(document).ready(function() {
            $(".superviser_status").on('change', function() {
                var rowId = $(this).closest('tr').data('id');
                var selectedValue = $(this).val();
                if (selectedValue === "Rejected") {
                    $('#superviserModalReject' + rowId).modal('show');

                } else if (selectedValue === "Verified") {
                    $('#superviserModal' + rowId).modal('show');
                }
            });


            $(".banker_status").on('change', function() {
                var rowId = $(this).closest('tr').data('id');

                var selectedValue = $(this).val();
                if (selectedValue === "Rejected") {
                    $('#bankerModalReject' + rowId).modal('show');

                } else if (selectedValue === "Verified") {
                    $('#bankerModal' + rowId).modal('show');
                }

            });


            $('.superviser_status_approval_conform').on('click', function() {
                rowId = $(this).closest('tr').data('id');
                var value = $(this).closest('tr').find(".superviser_status").val();
                // var noteValue = $(this).closest('tr').find('.note').val().trim();
                var $modalToClose = $('#superviserModal' + rowId);
                var type = 'superviser_status';

                $.ajax({
                    type: "POST",
                    dataType: "JSON",
                    url: "{{ route('internalTransfer.status') }}",
                    data: {
                        transferId: rowId,
                        status: value,
                        // note: (value === 'Rejected') ? noteValue : '',
                        type: type,
                    },
                    success: function(response) {
                        $modalToClose.modal('hide');
                        if (response.status_code == 200) {
                            displayMessage($(".custom-alert"), "Success",
                                "Status Updated Successfully");

                        }

                    },
                    error: function(xhr) {
                        // Handle the error if needed
                        console.log(xhr.responseText);
                    }
                });

            });

            $('.superviser_status_reject_conform').on('click', function() {
                rowId = $(this).closest('tr').data('id');
                var value = $(this).closest('tr').find(".superviser_status").val();
                // var noteValue = $(this).closest('tr').find('.note').val().trim();
                var $modalToClose = $('#superviserModalReject' + rowId);
                var type = 'superviser_status';



                var noteElement = $('#superviserModalReject' + rowId).find('.note');
                var noteValue = noteElement.val().trim();

                $.ajax({
                    type: "POST",
                    dataType: "JSON",
                    url: "{{ route('internalTransfer.status') }}",
                    data: {
                        transferId: rowId,
                        status: value,
                        remark: (value === 'Rejected') ? noteValue : '',
                        type: type,
                    },
                    success: function(response) {
                        $modalToClose.modal('hide');
                        if (response.status_code == 200) {
                            displayMessage($(".custom-alert"), "Success",
                                "Status Updated Successfully");

                        }

                    },
                    error: function(xhr) {
                        // Handle the error if needed
                        var errors = xhr.responseJSON.errors;

                        console.log(xhr.responseText);
                        for (var key in errors) {
                                if (errors.hasOwnProperty(key)) {
                                    errors[key].forEach(function(errorMsg) {
                                        // Append error messages to the appropriate location within your table row
                                        $('#' + key + '_error' + rowId).text(errorMsg);
                                    });
                                }
                            }
                    }
                });

            });


            $('.banker_status_approval_conform').on('click', function(event) {
                event.preventDefault();

                var $button = $(this); // Cache the button element
    $button.prop('disabled', true);
                rowId = $(this).closest('tr').data('id');
                var value = $(this).closest('tr').find(".banker_status").val();
                // var noteValue = $(this).closest('tr').find('.note').val().trim();
                var $modalToClose = $('#bankerModal' + rowId);
                var type = 'banker_status';
                // Get the attachment file from within the modal
                var attachmentFile = $modalToClose.find('.attachment').prop('files')[0];

                // Get the reference number from within the modal

                var refNo = $modalToClose.find('.ref_no').val();
                // var bankId = $modalToClose.find('.our_bank_detail').val();

                // var refNo = $modalToClose.find('.ref_no').val();

                // Create a FormData object to handle file upload
                var formData = new FormData();
                formData.append('transferId', rowId);
                formData.append('status', value);
                formData.append('type', type);
                formData.append('attachment', attachmentFile ? attachmentFile : '');
                formData.append('utr', refNo);
                // formData.append('our_bank_detail_id', bankId);

                $.ajax({
                    type: "POST",
                    dataType: "JSON",
                    url: "{{ route('internalTransfer.status') }}",
                    data: formData,
                    processData: false, // Prevent jQuery from processing data
                    contentType: false, // Prevent jQuery from setting contentType
                    success: function(response) {
                        $button.prop('disabled', false);

                        $modalToClose.modal('hide');
                        if (response.status_code == 200) {
                            displayMessage($(".custom-alert"), "Success",
                                "Status Updated Successfully");

                        }
                    },
                    error: function(xhr) {
                        $button.prop('disabled', false);
                        var errors = xhr.responseJSON.errors;
                        // Now, access the stored rowId variable here
                        console.log(errors);
                        // console.log('hello');
                        if (errors) {
                            // Clear any existing error messages
                            $('.error-placeholder').empty();
                            if (errors.hasOwnProperty('no_balance')) {
                                // Display the insufficient balance error message in an appropriate location
                                $('#no_balance' + rowId).text(errors.no_balance);
                            }
                            // Display all validation errors
                            for (var key in errors) {
                                if (errors.hasOwnProperty(key)) {
                                    errors[key].forEach(function(errorMsg) {
                                        // Append error messages to the appropriate location within your table row
                                        $('#' + key + '_error' + rowId).text(errorMsg);
                                    });
                                }
                            }
                        } else {
                            console.log('An error occurred.');
                        }
                    }
                });

            });


            $('.banker_status_reject_conform').on('click', function(event) {
                event.preventDefault();

                rowId = $(this).closest('tr').data('id');
                var value = $(this).closest('tr').find(".banker_status").val();
                var noteValue = $(this).closest('tr').find('.note').val().trim();
                var $modalToClose = $('#bankerModalReject' + rowId);
                var type = 'banker_status';

                var noteElement = $('#bankerModalReject' + rowId).find('.note');
                var noteValue = noteElement.val().trim();
                $.ajax({
                    type: "POST",
                    dataType: "JSON",
                    url: "{{ route('internalTransfer.status') }}",
                    data: {
                        transferId: rowId,
                        status: value,
                        remark: noteValue,
                        type: type,
                    },
                    success: function(response) {
                        $modalToClose.modal('hide');
                        if (response.status_code == 200) {
                            displayMessage($(".custom-alert"), "Success",
                                "Status Updated Successfully");

                        }

                    },
                    error: function(xhr) {
                        // Handle the error if needed
                        console.log(xhr.responseText);
                        var errors = xhr.responseJSON.errors;

console.log(xhr.responseText);
for (var key in errors) {
        if (errors.hasOwnProperty(key)) {
            errors[key].forEach(function(errorMsg) {
                // Append error messages to the appropriate location within your table row
                $('#' +'banker_'+ key + '_error' + rowId).text(errorMsg);
            });
        }
    }
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

            function displayError(element, errorMessage) {
                element
                    .html(errorMessage)
                    .css("display", "block")
                    .delay(3000)
                    .fadeOut(700);
            }


        });


        $('.file-input').on('change', function() {
            // var modalId = $(this).closest('.modal.show').attr('id');
            var modalId = $('.modal.show').attr('id');
            var file = this.files[0];
            console.log("file");
            console.log(file);
            if (file) {
                var reader = new FileReader();
                const formData = new FormData();
                formData.append('image', file);
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
                    $('#' + modalId + ' #tr_utr').val(utrNumber);

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
    </script>
    <script>
        $(document).ready(function() {
            $('#example').DataTable()
        });
    </script>
</body>

</html>
