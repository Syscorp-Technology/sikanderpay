<!doctype html>
<html lang="en">

<head>
    @include('layouts.user_link')

    <style>
        .select2-container--default .select2-selection--single {
            background-color: rgb(0 0 0/15%);
            border: 1px solid rgb(255 255 255 / 15%);
            border-radius: 4px;
        }

        .select2-dropdown {
            background-color: #3c3c3c;
        }

        .select2-container--default .select2-search--dropdown .select2-search__field {
            border: 1px solid #aaa;
            background: #c1c1c173;
            border-radius: 5px;
            color: #fff;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #fff;
        }

        .select2-container--default .select2-selection--single {
            background-color: rgb(0 0 0/15%);
            border: 1px solid rgb(255 255 255 / 15%);
            border-radius: 4px;
            padding: 6px 0px 6px;
            height: 38px;
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
    <!--wrapper-->
    <div class="wrapper">
        {{--  sidebar  --}}
        @include('layouts.sidebar')

        @include('layouts.header')


        <div id="loader" class="loader"></div>

        <div class="page-wrapper">
            <div class="page-content">


                <div class="page-content">
                    <div class="row">
                        <div class="col-xl-12 mx-auto">
                            <h6 class="mb-0 text-uppercase">Transfer Create</h6>
                            <hr />
                            <div class="card border-top border-0 border-4 border-white">
                                {{-- <div class="user-details">
                                    <span class="user-name">
                                        Test
                                    </span>
                                    <span class="user-name">
                                        7894561230
                                    </span>
                                </div> --}}
                                <div class="card-body p-5">
                                    <div id="content">

                                        <form id="expense-form" class="row g-3" method="POST" action="{{ route('expense.store') }}"
                                            enctype="multipart/form-data">
                                            @csrf
                                            <!-- Your HTML structure -->
                                            <div>
                                                <p> <span id="user_name"></span></p>
                                                <p><span id="user_mobile"></span></p>
                                            </div>


                                            <div class="col-md-6">
                                                <label for="title" class="form-label">Title<span
                                                        class="text-danger">*</span></label>
                                                <input type="text" name="title" value="{{ old('title') }}"
                                                    id="title" class="form-control" required />
                                                @error('title')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>


                                            <div class="col-md-6">
                                                <label for="date" class="form-label">Date<span
                                                        class="text-danger">*</span></label>
                                                <input type="date" name="date"
                                                    value="{{ old('date', date('Y-m-d')) }}" id="date"
                                                    class="form-control" required />
                                                @error('date')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-6">
                                                <label class="form-label">Category<span
                                                        class="text-danger">*</span></label>
                                                <div>
                                                    <select class="form-control" required name="expense_category_id"
                                                        id="category">
                                                        <option value="">Choose Category</option>
                                                        @foreach ($categories as $key => $value)
                                                            <option
                                                                value="{{ $value->id }}"{{ old('expense_category_id') == $value->id ? 'selected' : '' }}>
                                                                {{ $value->expense_category_name }}
                                                            </option>
                                                        @endforeach

                                                    </select>
                                                    @error('expense_category_id')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>


                                            <div class="col-md-6">
                                                <label class="form-label">Payment Mode<span
                                                        class="text-danger">*</span></label>
                                                <div>
                                                    <select class="form-control" required name="payment_mode_id"
                                                        id="payment_mode">
                                                        <option value="">Select Payment Mode</option>
                                                        @foreach ($paymentModes as $key => $value)
                                                            <option
                                                                value="{{ $value->id }}"{{ old('payment_mode_id') == $value->id ? 'selected' : '' }}>
                                                                {{ $value->payment_mode_name }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('payment_mode_id')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            {{-- <div class="col-md-6">
                                                <label for="ref_no" class="form-label">Ref No</label>
                                                <input type="text" name="ref_no" id="amount"
                                                    class="form-control" />
                                                @error('ref_no')
                                                    <div class="error">{{ $message }}</div>
                                                @enderror
                                            </div> --}}
                                            {{-- <div class="col-md-6">
                                                <label for="attachment" class="form-label">Attachment</label>
                                                <input type="file" name="attachment" id="attachment"
                                                    class="form-control" />
                                                @error('attachment')
                                                    <div class="error">{{ $message }}</div>
                                                @enderror
                                            </div> --}}
                                            {{-- <div class="col-md-6">
                                                <label>Type<span class="text-danger">*</span></label>
                                                <div>
                                                    <select class="form-control" required name="type" id="type">
                                                        <option value="">Choose platform</option>
                                                        <option value="Income">Income</option>
                                                        <option value="Expence">Expence</option>
                                                    </select>
                                                </div>
                                            </div> --}}

                                            <div class="col-md-6">
                                                <label for="amount" class="form-label">Amount<span
                                                        class="text-danger">*</span></label>
                                                <input type="text" name="amount" value="{{ old('amount') }}"
                                                    id="amount" class="form-control" required />
                                                @error('amount')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-12">
                                                <label for="note" class="form-label">Note ( Bank Details Or Other
                                                    Info )<span class="text-danger">*</span></label>
                                                <textarea name="note" id="note" cols="20" rows="5" class="form-control" required>{{ old('note') }}</textarea>
                                                @error('note')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-12">
                                            </div>
                                            <div class="col-12">
                                                <button type="submit" id="submitBtn" class="btn btn-light px-5">
                                                    Submit
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>



        <div class="offcanvas offcanvas-end w-50" tabindex="-1" id="offcanvasRight11"
            aria-labelledby="offcanvasRightLabel">

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
                                        <img id="modalImage" src="" alt="Image" style="width: 100%;">

                                    </div>


                                    <div>


                                    </div>


                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            {{--  color change icon  --}}
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
        @include('layouts.user_script')


        {{--  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>  --}}
        <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

        <script type="text/javascript">
            var ct = 1;

            function new_link() {
                ct++;
                var div1 = document.createElement('div');
                div1.id = ct;
                // link to delete extended form elements
                var delLink = '<div style="text-align:right;margin-right:65px"><a href="javascript:delIt(' + ct +
                    ')">Del</a></div>';
                div1.innerHTML = document.getElementById('newlinktpl').innerHTML + delLink;
                document.getElementById('newlink').appendChild(div1);
            }
            // function to delete the newly added set of elements
            function delIt(eleId) {
                d = document;
                var ele = d.getElementById(eleId);
                var parentEle = d.getElementById('newlink');
                parentEle.removeChild(ele);
            }
        </script>
        <script>
            $(document).ready(function() {
                $('#expense-form').submit(function() {
                // Check if all required fields are filled
                    if ($(this).get(0).checkValidity()) {
                     // If all fields are filled, disable the submit button
                        $('#submitBtn').prop('disabled', true);
                    }
                });
            });
        </script>
        {{-- <script src="assets/js/index.js"></script>
    <script>
        new PerfectScrollbar('.product-list');
        new PerfectScrollbar('.customers-list');
    </script> --}}
</body>

</html>
