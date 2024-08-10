<html>

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!--favicon-->
    <link rel="icon" href="{{ asset('assets/images/favicon-32x32.png') }}" type="image/png" />

    @include('layouts.user_link')
    <title>SikanderPlayx</title>

</head>

<body class="bg-theme bg-theme2">
    @include('components.success-alert')
    <!--wrapper-->
    <div class="wrapper"> {{-- sidebar --}}
        @include('layouts.sidebar')
        @include('layouts.header')
        {{-- body content --}}
        <div class="page-wrapper">
            <div class="page-content">
                <div class="row justify-content-between align-items-center">
                    <div class="col-md-4">
                        <h6 class="mb-0 text-uppercase">All Deposit</h6>
                    </div>
                    @can('User Add')
                        <div class="col-md-8 d-flex justify-content-end">
                            <a type="button" class="btn btn-light" href="{{ route('deposit.create') }} "
                                class="list-group-item">Add <i class="fa-solid fa-plus fs-18"></i></a>
                        </div>
                    @endcan
                </div> @can('User Show')
                    <div class="card">
                        <div class="card-body">
                            <table id="all-deposits-table" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>UTR</th>
                                        <th>User Name</th>
                                        <th>Name</th>
                                        <th>Deposit Bank</th>
                                        <th>D.Amount</th>
                                        <th>Bonus</th>
                                        <th>Image</th>
                                        <th>Timeline</th>
                                        <th>TD.Amount</th>
                                        <th>Admin Status</th>
                                        <th>Banker Status</th>
                                        <th>CC Status</th>
                                        <th>Created By</th>
                                        <th>Action</th>
                                        <!-- Add more table headers for other columns -->
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                @endcan
            </div>
        </div>
        <div class="overlay toggle-icon"></div>
        <!--end overlay-->
        <!--Start Back To Top Button--> <a href="javaScript:;" class="back-to-top"><i
                class='bx bxs-up-arrow-alt'></i></a>
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

    <!-- delete modal -->
    {{-- <button class="delete-btn" data-id="123">Delete User</button> --}}

    <!-- Delete Modal -->
    {{-- <div class="modal" id="myModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"> Delete Confirmation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this item?
                <span class="close">&times;</span>
                <p>User ID: <span id="userIdInModal"></span></p>
                <form method="POST" action="" id="deleteForm">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="id" value="">
                    <button type="submit" class="btn btn-danger">Delete</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </form>
            </div>
        </div>
    </div>
</div> --}}
    {{-- <div id="deleteConfirmationModal" class="modal">
        <div class="modal-dialog modal-sm">
            <div class="modal-body">
                <p>Are you sure you want to delete this item?</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-default modal-close">Cancel</button>
                <button class="btn btn-danger" id="confirmDelete">Delete</button>
            </div>
        </div>
    </div> --}}

    <div class="modal fade" id="deleteConfirmationModal" tabindex="-1" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Profile Details</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Do you want to delete the record?
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary modal-close" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-danger" id="confirmDelete">Delete</button>
                </div>
            </div>
        </div>
    </div>


    </div>



    <!-- Add this to your HTML body section -->
    <div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="imageModalLabel">Image Preview</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Image will be displayed here -->
                </div>
            </div>
        </div>
    </div>


    <div class="offcanvas offcanvas-end w-50" tabindex="-1" id="offcanvasRight1" aria-labelledby="offcanvasRightLabel">

        <div class="offcanvas-header">

            <button type="button" class="btn-close icon-close new-meth" data-bs-dismiss="offcanvas"
                aria-label="Close"><i class="fa-regular fa-circle-xmark"></i></button>
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
                                    <div id="preview">

                                    </div>
                                    {{-- <img id="preview" src=""
                                    alt="Image"
                                    style="width: 100%;"> --}}

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




    <div class="offcanvas offcanvas-end w-50" tabindex="-1" id="offcanvasRight2"
        aria-labelledby="offcanvasRightLabel">

        <div class="offcanvas-header">

            <button type="button" class="btn-close icon-close new-meth" data-bs-dismiss="offcanvas"
                aria-label="Close"><i class="fa-regular fa-circle-xmark"></i></button>
        </div>
        <div class="offcanvas-body">
            <div class="card-body p-5">
                <div class="card-heaer-cus" style="margin: 20px 0;">
                    <div class="h5">TimeLine
                    </div>
                </div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                        <h6 class="mb-0">Created By
                        </h6>
                        <span class="text-white" id="created_by"></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                        <h6 class="mb-0">Created At
                        </h6>
                        <span class="text-white" id="created_at"></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                        <h6 class="mb-0">Banker
                        </h6>
                        <span class="text-white" id="banker"></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                        <h6 class="mb-0">Banker Update
                        </h6>
                        <span class="text-white" id="banker_at"></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                        <h6 class="mb-0">
                            Admin User</h6>
                        <span class="text-white" id="admin"></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                        <h6 class="mb-0">Admin Update
                        </h6>
                        <span class="text-white" id="admin_at"></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                        <h6 class="mb-0">
                            CustomerCare User
                        </h6>
                        <span class="text-white" id="cc"></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                        <h6 class="mb-0">Customer Care Update
                        </h6>
                        <span class="text-white" id="cc_at"></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                        <h6 class="mb-0">Status</h6>
                        <span class="text-white" id="status"></span>
                    </li>

                </ul>
            </div>
        </div>

    </div>





    @include('layouts.user_script')
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        var successAlertRoute = @json(route('success-alert'));
    </script>
    <script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/5/tinymce.min.js"></script>

    <script src="{{ asset('assets/js/pages/backend-datatables.js') }}"></script>
    <script src="{{ asset('assets/js/copy-platformdetails.js') }}"></script>
</body>

</html>
