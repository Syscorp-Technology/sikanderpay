<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!--favicon-->
    <link rel="icon" href="{{ asset('assets/images/favicon-32x32.png') }}" type="image/png" />

    @include('layouts.user_link')
    <title>SikanderPlayX - Portal</title>
</head>

<body class="bg-theme bg-theme2">
    <!--wrapper-->
    <div class="wrapper">
        @include('layouts.sidebar')
        <!--start header -->
        @include('layouts.header')
        <!--end header -->
        <!--start page wrapper -->

        <!--start page wrapper -->
        <div class="page-wrapper">
            <div class="page-content">
                @if (session('success'))
                    <div id="success-message" class="alert border-0 alert-dismissible fade show py-2">
                        <div class="d-flex align-items-center">
                            <div class="font-35 text-white"><i class='bx bxs-check-circle'></i>
                            </div>
                            <div class="ms-3">
                                <h6 class="mb-0 text-white"> {{ session('success') }}</h6>
                            </div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                @if (Session('danger'))
                    <div class="alert border-0 alert-dismissible fade show" style="background-color: #d9534f">
                        <div class="text-white"> {{ session('danger') }}</div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="row justify-content-between align-items-center">
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-4">

                                <h6 class="mb-0 text-uppercase">Banks</h6>
                            </div>
                            @can('Branchs Show')
                                <div class="col-4">

                                    <h6 class="mb-0 text-uppercase"> AB : <span
                                            class="badge bg-success">{{ number_format($totalActiveBankBalance, 2) }}</span> </h6>
                                    <p class="mb-0 text-uppercase"><span class="badge ">(Active Bank
                                            Balance)</span> </p>


                                </div>
                                <div class="col-4">

                                    <h6 class="mb-0 text-uppercase">BB : <span
                                            class="badge bg-danger text-white">{{ number_format($inactiveBanksAmount, 2) }}</span>
                                    </h6>
                                    <p class="mb-0 text-uppercase"><span class="badge ">(Block Bank
                                            Balance)</span> </p>
                                </div>
                            @endcan

                        </div>
                    </div>

                    <div class="col-md-6 d-flex justify-content-end">

                        @can('Branchs Add')
                            <a type="button" class="btn btn-light" href="{{ route('ourbankdetail.create') }}   "
                                class="list-group-item">Add<i class="fa-solid fa-plus fs-18"></i></a>
                        @endcan
                    </div>
                </div>

                <hr />
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            @can('Branchs Show')
                                <table id="example" class="table table-striped table-bordered" style="width: 100%">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Bank Name</th>
                                            <th>Account Number</th>
                                            <th>IFSC</th>
                                            <th>Amount</th>
                                            <th>Limit</th>
                                            {{-- <th>Count</th> --}}
                                            <th>Remarks</th>
                                            <th>Status</th>
                                            <th>Action</th>

                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($bankdetail as $lead)
                                            <tr>
                                                <td>{{ $lead['id'] }}</td>
                                                <td>{{ $lead['bank_name'] }}</td>
                                                <td>{{ $lead['account_number'] }}</td>
                                                <td>{{ $lead['ifsc'] }}</td>
                                                <td>{{ number_format($lead['amount'], 2) }}</td>
                                                <td>{{ $lead['limit'] }}</td>
                                                {{-- <td>{{ $lead['count'] }}</td> --}}
                                                <td>{{ $lead['remarks'] }}</td>
                                                <td>
                                                    @if ($lead['status'] == 1)
                                                        <span class="badge rounded-pill bg-success">Active</span>
                                                    @elseif ($lead['status'] == 2)
                                                    <span class="badge rounded-pill bg-info">Temporary</span>

                                                    @elseif ($lead['status'] == 0)
                                                    <span class="badge rounded-pill bg-danger">Inactive</span>

                                                    @endif
                                                </td>
                                                <td>
                                                    @can('Branchs Edit')
                                                        <a href="{{ route('ourbankdetail.edit', $lead->id) }}"><i
                                                                class="fa-regular fa-pen-to-square"></i></a>
                                                    @endcan
                                                    @can('Branchs Branchs Delete')
                                                        <a href="" data-id="{{ $lead->id }}" data-bs-toggle="modal"
                                                            data-bs-target="#exampleSmallModal{{ $lead->id }}"><i
                                                                class="fa-solid fa-trash-can"></i></a>
                                                    @endcan

                                                    <a href="{{ route('our-bank.income', $lead->id) }}"><i
                                                            class="fa-solid fa-eye"></i></a>

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


    {{--  modal code  --}}
    @foreach ($bankdetail as $frame)
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
                            action="{{ route('branch.delete', ['id' => $frame->id]) }}">
                            @csrf
                            @method('DELETE')
                            {{-- <button type="submit" class="btn btn-danger">Delete</button> --}}
                        </form>
                    </div>
                    <div class="modal-footer">
                        <!-- Add a "Cancel" button that dismisses the modal -->
                        <button type="submit" class="btn btn-danger">Delete</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </div>
        </div>
        </div>
    @endforeach
    <!--end switcher-->

    @include('layouts.user_script')
    <script src="{{ asset('assets/js/pages/player.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const successMessage = document.getElementById('success-message');
            if (successMessage) {
                setTimeout(function() {
                    successMessage.style.display = 'none';
                }, 5000);
            }
        });
    </script>
    <script>
        $(document).ready(function() {
            $('#Transaction-History').DataTable({
                lengthMenu: [
                    [6, 10, 20, -1],
                    [6, 10, 20, 'Todos']
                ]
            });
        });
    </script>
    <script src="assets/js/index.js"></script>
    <script>
        new PerfectScrollbar('.product-list');
        new PerfectScrollbar('.customers-list');
    </script>
</body>

</html>
