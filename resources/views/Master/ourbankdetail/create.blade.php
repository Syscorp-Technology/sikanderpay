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




                <div class="row justify-content-between align-items-center">
                    <div class="col-md-4">
                        <h6 class="mb-0 text-uppercase">Create Bank</h6>
                    </div>


                </div>

                <hr />
                <div class="card">
                    <div class="card-body">
                        <form class="row g-3" method="POST" action="{{ route('ourbankdetail.store') }}">
                            @csrf
                            <div class="col-md-12">
                                <label for="inputtype" class="form-label">Type <span class="mandatory-field">*</span></label>
                              <select name="type" id="inputtype" class="form-control">
                                  {{-- <option value="">Inactive</option> --}}
                                <option value="Bank">Bank</option>
                                <option value="Gateway">Gateway</option>

                              </select>
                              @error('type')
                              <span class="error" style="color: red;">{{ $message }}</span>
                              @enderror
                            </div>

                            <div class="col-md-12" id="gateway-category-div" style="display: none;">
                                <label for="inputcategory" class="form-label">Gateway <span class="mandatory-field">*</span></label>
                              <select name="category" id="inputGateway" class="form-control">
                                  {{-- <option value="">Inactive</option> --}}
                                <option value="">Select Gateway</option>
                                @foreach($gatewayCategory as $category)
                                <option value="{{ $category->id }}">{{ $category->gateway_name }}</option>
                                  @endforeach
                              </select>
                              @error('category')
                              <span class="error" style="color: red;">{{ $message }}</span>
                              @enderror
                            </div>

                            <div class="col-md-12">
                                <label for="inputFirstName" class="form-label">Bank Name <span class="mandatory-field">*</span></label>
                                <input type="text" name="bank_name" class="form-control">
                                @error('bank_name')
                                <span class="error" style="color: red;">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-12">
                                <label for="inputLastName" class="form-label">Account Number <span class="mandatory-field">*</span></label>
                                <input type="text" name="account_number" class="form-control">
                                @error('account_number')
                                <span class="error" style="color: red;">{{ $message }}</span>
                                @enderror

                            </div>
                            <div class="col-md-12">
                                <label for="inputLastName" class="form-label">IFSC <span class="mandatory-field">*</span></label>
                                <input type="text" name="ifsc" class="form-control">
                                @error('ifsc')
                                <span class="error" style="color: red;">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-12">
                                <label for="inputAmount" class="form-label">Amount <span class="mandatory-field">*</span></label>
                                <input type="text" id="inputAmount" name="amount" class="form-control">
                                @error('amount')
                                <span class="error" style="color: red;">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-12">
                                <label for="inputLimit" class="form-label">Limit</label>
                                <input type="number" name="limit" class="form-control">
                                @error('limit')
                                <span class="error" style="color: red;">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-12">
                                <label for="inputStatus" class="form-label">Status <span class="mandatory-field">*</span></label>
                              <select name="status" id="inputStatus" class="form-control">
                                  <option value="0">Inactive</option>
                                <option value="1">Active</option>
                                <option value="2">Temporary</option>

                              </select>
                              @error('status')
                              <span class="error" style="color: red;">{{ $message }}</span>
                              @enderror
                            </div>
                            <div class="col-md-12">
                                <label for="inputLastName" class="form-label">Remark</label>
                                <input type="text" name="remarks" class="form-control">
                                @error('remarks')
                                <span class="error" style="color: red;">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-light px-5">
                                    Submit
                                </button>
                            </div>
                        </form>
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
    <!--end switcher-->

    @include('layouts.user_script')

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
    <script>
        $(document).ready(function() {
            $('#inputtype').change(function() {
                var selectedType=$(this).val();
                // var selectedType=$(this).val();
                if(selectedType =='Gateway'){
                    $('#gateway-category-div').show();
                }else {
                    $('#gateway-category-div').hide();
                }
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
