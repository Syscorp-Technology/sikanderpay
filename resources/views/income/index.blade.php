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
                        <h6 class="mb-0 text-uppercase">Income List</h6>
                    </div>

                    <div class="col-md-8 d-flex justify-content-end">

                        @can('Income Add')
                            <a type="button" class="btn btn-light" href="{{ route('income.create') }}"
                                class="list-group-item">Add<i class="fa-solid fa-plus fs-18"></i></a>
                        @endcan

                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            @can('Income Show')
                                <table id="example" class="table table-striped table-bordered" style="width: 100%">
                                    <thead>
                                        <tr>

                                            <th>ID</th>
                                            <th>Title</th>
                                            <th>Date</th>
                                            <th>Category</th>
                                            <th>Payment Mode</th>
                                            <th>Ref No</th>
                                            <th>Image</th>
                                            <th>Bank</th>
                                            <th>Amount</th>

                                            {{-- <th>ref No</th> --}}
                                            {{-- <th>banker Status</th> --}}
                                            {{-- <th>UTR Details</th> --}}

                                            <th>Banker</th>
                                            <th>Created By</th>
                                            {{-- <th>Action</th> --}}

                                        </tr>
                                    </thead>
                                    <tbody id="deposit_tbody">
                                        @foreach ($income as $key => $item)
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
                                                                    <h6 class="mb-0">Bank</h6>
                                                                    <span
                                                                        class="badge bg-warning text-dark">{{ $item->ourBankDetail->bank_name }}</span>
                                                                </li>
                                                                <li
                                                                    class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                                                    <h6 class="mb-0">Bank Account No</h6>
                                                                    <span
                                                                        class="text-white">{{ $item->ourBankDetail->account_number }}</span>
                                                                </li>
                                                                <li
                                                                    class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                                                    <h6 class="mb-0">Amount</h6>
                                                                    <span
                                                                        class="badge bg-warning text-dark">{{ $item->amount }}</span>
                                                                </li>
                                                                <li
                                                                    class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                                                    <h6 class="mb-0">Ref No</h6>
                                                                    <span class="text-white">{{ $item->ref_no }}</span>
                                                                </li>
                                                                <li
                                                                    class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                                                    <h6 class="mb-0">Note</h6>
                                                                    <span class="text-white">{{ $item->note }}</span>
                                                                </li>
                                                            </ul>

                                                        </div>
                                                    </div>


                                                </td>

                                                <td>{{ $item->date }}</td>
                                                <td>{{ $item->category->category_name }}</td>

                                                <td>{{ $item->paymentMode->payment_mode_name }}</td>
                                                <td>{{ $item->ref_no }}</td>
                                                <td>

                                                    @if ($item->attachment)
                                                        <a data-bs-toggle="offcanvas"
                                                            data-bs-target="#offcanvasRightAttachment{{ $item->id }}"
                                                            aria-controls="offcanvasRight">
                                                            <img src="{{ asset('storage/' . $item->attachment) }}"
                                                                alt="Image" style="max-width: 50px; max-height: 50px;">
                                                        </a>
                                                    @else
                                                        No Image
                                                    @endif


                                                    <div class="offcanvas offcanvas-end w-50" tabindex="-1"
                                                        id="offcanvasRightAttachment{{ $item->id }}"
                                                        aria-labelledby="offcanvasRightLabel">
                                                        <div class="offcanvas-header">

                                                            <button type="button" class="btn-close icon-close new-meth"
                                                                data-bs-dismiss="offcanvas" aria-label="Close"><i
                                                                    class="fa-regular fa-circle-xmark"></i></button>
                                                        </div>
                                                        <div class="offcanvas-body">
                                                            {{-- <div class="model-body">
                                                                --}}
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
                                                                                    <img src="{{ asset('storage/' . $item->attachment) }}"
                                                                                        alt="Image">
                                                                                </div>

                                                                            </div>


                                                                            <div>


                                                                            </div>


                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                    </div>
                            </div>
                            </td>
                            <td>{{ $item->ourBankDetail->bank_name }}</td>
                            <td>{{ $item->amount }}</td>

                            <td>


                                <select style="width: 110px; text-align: center;"
                                    class="form-control rolling-type-select banker_Status banker_status"
                                    name="banker_status"  @cannot('Income Banker Enable') disabled @endcan required>
                                    <option value="Pending" @if ($item->banker_status === 'Pending') selected @endif>
                                        Pending
                                    </option>
                                    <option value="Verified" @if ($item->banker_status === 'Verified') selected @endif>
                                        Verified
                                    </option>
                                    <option value="Rejected" @if ($item->banker_status === 'Rejected') selected @endif>
                                        Rejected
                                    </option>
                                </select>


                                <!-- Modal -->
                                <div class="modal fade" id="exampleModal{{ $item->id }}" tabindex="-1"
                                    aria-labelledby="exampleModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5" id="exampleModalLabel">
                                                    Income Approval</h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">

                                                {{-- <form action=""> --}}
                                                <div class="row">

                                                    <div class="col-8">
                                                        <ul class="list-group list-group-flush">
                                                            <li
                                                                class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                                                <h6 class="mb-0">Title</h6>
                                                                <span class="text-white">{{ $item->title }}</span>
                                                            </li>
                                                            <li
                                                                class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                                                <h6 class="mb-0">Date</h6>
                                                                <span class="text-white">{{ $item->date }}</span>
                                                            </li>
                                                            <li
                                                                class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                                                <h6 class="mb-0">Category</h6>
                                                                <span
                                                                    class="text-white">{{ $item->category->category_name }}</span>
                                                            </li>
                                                            <li
                                                                class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                                                <h6 class="mb-0">Payment Mode</h6>
                                                                <span
                                                                    class="text-white">{{ $item->paymentMode->payment_mode_name }}</span>
                                                            </li>
                                                            <li
                                                                class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                                                <h6 class="mb-0">Ref No</h6>
                                                                <span
                                                                    class="text-white  badge bg-success fs-6">{{ $item->ref_no }}</span>
                                                            </li>
                                                            <li
                                                                class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                                                <h6 class="mb-0">Bank</h6>
                                                                <span
                                                                    class="text-white badge bg-success fs-6">{{ $item->ourBankDetail->bank_name }}</span>
                                                            </li>
                                                            <li
                                                                class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                                                <h6 class="mb-0">Amount</h6>
                                                                <span
                                                                    class="text-dark badge bg-warning fs-6">{{ $item->amount }}</span>
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
                                                                <span class="text-white">{{ $item->created_at }}</span>
                                                            </li>
                                                            <li
                                                                class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                                                <h6 class="mb-0">Note</h6>
                                                                <span class="text-white">{{ $item->note }}</span>
                                                            </li>
                                                        </ul>

                                                    </div>
                                                    <div class="col-4">
                                                        @if ($item->attachment)
                                                            <img src="{{ asset('storage/' . $item->attachment) }}"
                                                                alt="Image" style="width: 100%;">
                                                        @else
                                                            No Image
                                                        @endif
                                                    </div>


                                                </div>
                                                {{-- </form> --}}
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Close</button>
                                                <button type="button" class="btn btn-primary conform ">Confirm
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>




                                <div class="modal fade" id="RejectModal{{ $item->id }}" tabindex="-1"
                                    aria-labelledby="exampleModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5" id="exampleModalLabel">
                                                    Income Reject</h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">

                                                <form action="">
                                                    <div class="row">
                                                        <div class="col-8">
                                                            <ul class="list-group list-group-flush">
                                                                <li
                                                                    class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                                                    <h6 class="mb-0">Title</h6>
                                                                    <span class="text-white">{{ $item->title }}</span>
                                                                </li>
                                                                <li
                                                                    class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                                                    <h6 class="mb-0">Date</h6>
                                                                    <span class="text-white">{{ $item->date }}</span>
                                                                </li>
                                                                <li
                                                                    class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                                                    <h6 class="mb-0">Category</h6>
                                                                    <span
                                                                        class="text-white">{{ $item->category->category_name }}</span>
                                                                </li>
                                                                <li
                                                                    class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                                                    <h6 class="mb-0">Payment Mode
                                                                    </h6>
                                                                    <span
                                                                        class="text-white">{{ $item->paymentMode->payment_mode_name }}</span>
                                                                </li>
                                                                <li
                                                                    class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                                                    <h6 class="mb-0">Ref No</h6>
                                                                    <span
                                                                        class="text-white badge bg-success">{{ $item->ref_no }}</span>
                                                                </li>
                                                                <li
                                                                    class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                                                    <h6 class="mb-0">Bank</h6>
                                                                    <span
                                                                        class="text-white badge bg-success">{{ $item->ourBankDetail->bank_name }}</span>
                                                                </li>
                                                                <li
                                                                    class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                                                    <h6 class="mb-0">Amount</h6>
                                                                    <span
                                                                        class="badge bg-warning text-dark">{{ $item->amount }}</span>
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
                                                                    <span class="text-white">{{ $item->note }}</span>
                                                                </li>
                                                            </ul>

                                                        </div>
                                                        <div class="col-4">
                                                            @if ($item->attachment)
                                                                <img src="{{ asset('storage/' . $item->attachment) }}"
                                                                    alt="Image" style="width: 70%;">
                                                            @else
                                                                No Image
                                                            @endif
                                                        </div>
                                                        <div class="col-12">
                                                            <label for="" class="form-label">Reason:<span
                                                                class="text-danger">*</span></label>
                                                            <textarea name="note" id="note" class="note form-control" cols="80" rows="5" placeholder="Enter Reject Reason"></textarea>
                                                            <span
                                                            class="error-placeholder text-danger"
                                                            id="note_error{{ $item->id }}"></span>
                                                        </div>
                                                    </div>

                                                </form>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Close</button>
                                                <button type="button" class="btn btn-primary conform ">Confirm
                                                </button>
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
            $(".banker_status").on('change', function() {
                var rowId = $(this).closest('tr').data('id');
                var selectedValue = $(this).val();

                if (selectedValue === 'Rejected') {
                    // $('.note').val('');
                    $('#RejectModal' + rowId).modal('show');
                } else if (selectedValue === 'Verified') {
                    // $('.note').val('');
                    $('#exampleModal' + rowId).modal('show');
                }
            });

            // $('.modal').on('click', '.btn-secondary[data-bs-dismiss="modal"]', function() {
            //     $(this).closest('.modal').find('.conform').click();
            // });

            $('.conform').on('click', function(event) {
                event.preventDefault();
                var $button = $(this); // Cache the button element
    $button.prop('disabled', true);
                var rowId = $(this).closest('tr').data('id');
                var value = $(this).closest('tr').find(".banker_status").val();
                var noteValue = $(this).closest('tr').find('.note').val().trim();
                var $modalToClose = (value === 'Rejected') ? $('#RejectModal' + rowId) : $('#exampleModal' +
                    rowId);
                // console.log(noteValue);
                // RejectModal
                $.ajax({
                    type: "POST",
                    dataType: "JSON",
                    url: "{{ route('income.banker.status') }}",
                    data: {
                        incomeId: rowId,
                        status: value,
                        note: (value === 'Rejected') ? noteValue : '',
                        // type: 'Verified'
                    },
                    success: function(response) {
                        $modalToClose.modal('hide');
    $button.prop('disabled', false);

                        if (response.status_code == 200) {

                            displayMessage($(".custom-alert"), "Success",
                                "Status Updated Successfully");


                            // Close the modal after a brief delay (e.g., 2 seconds)
                            // setTimeout(function() {
                            //     $modalToClose.modal('hide');
                            // }, 2000);
                            // }

                        }

                    },
                    error: function(xhr) {
    $button.prop('disabled', false);
                        // Handle the error if needed
                        console.log(xhr.responseText);
                        var errors = xhr.responseJSON.errors;

// console.log(xhr.responseText);
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
    </script>
    <script>
        $(document).ready(function() {
            $('#example').DataTable()
        });
    </script>
</body>

</html>
