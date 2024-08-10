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

        #viewImage {
            display: none;
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
                            <h6 class="mb-0 text-uppercase">Edit Income</h6>
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

                                        <form id="income-form" class="row g-3" method="POST" action="{{ route('income.update',$income->id) }}"
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
                                                <input type="text" name="title" value="{{ $income->title }}"
                                                    id="title" class="form-control" required />
                                                @error('title')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>


                                            <div class="col-md-6">
                                                <label for="date" class="form-label">Date<span
                                                        class="text-danger">*</span></label>
                                                <input type="date"
                                                    value="{{$income->date}}" name="date" id="date"
                                                    class="form-control" required />
                                                @error('date')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-6">
                                                <label class="form-label">Category<span
                                                        class="text-danger">*</span></label>
                                                <div>
                                                    <select class="form-control" required name="category_id"
                                                        id="category">
                                                        <option value="">Choose Category</option>
                                                        @foreach ($categories as $key => $value)
                                                            <option value="{{ $value->id }}"
                                                                @if ($income->category_id == $value->id)
                                                                selected
                                                            @endif >
                                                                {{ $value->category_name }}
                                                            </option>
                                                        @endforeach

                                                    </select>
                                                    @error('category_id')
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
                                                            <option value="{{ $value->id }}"
                                                                @if ($income->payment_mode_id == $value->id)
                                                                selected
                                                                @endif >
                                                                {{ $value->payment_mode_name }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('payment_mode_id')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <label for="attachment" class="form-label">Attachment<span
                                                        class="text-danger">*</span></label>
                                                <input type="file" name="attachment" id="fileInput"
                                                    class="form-control" />
                                                @error('attachment')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-6">
                                                <label for="amount" class="form-label">Amount<span
                                                        class="text-danger">*</span></label>
                                                <input type="text" name="amount" id="amount"
                                                    class="form-control" value="{{$income->amount}}" required />
                                                @error('amount')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>



                                            <div class="col-md-6">
                                                <label for="ref_no" class="form-label">Ref No<span
                                                        class="text-danger">*</span></label>
                                                <input type="text" name="ref_no" id="utr"
                                                    class="form-control" required value="{{$income->ref_no}}" />
                                                <span id="utr-exists"> </span>

                                                @error('ref_no')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>




                                            <div class="col-md-6">
                                                <label class="form-label">Select Bank<span
                                                        class="text-danger">*</span></label>
                                                <div>
                                                    <select class="form-control select2" required
                                                        name="our_bank_detail_id">
                                                        <option value="">Choose platform</option>
                                                        @foreach ($ourbank as $key => $value)
                                                            <option value="{{ $value->id }}"
                                                            @if ($income->our_bank_detail_id == $value->id)
                                                                selected
                                                            @endif
                                                              >
                                                                {{ $value->bank_name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error('our_bank_detail_id')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>



                                            <div class="col-6 " id="viewImage">
                                                <label for="" class="form-label"></label><br>

                                                <a type="button" data-note-id="1" type="button"
                                                    data-target="#offcanvas" data-bs-toggle="offcanvas"
                                                    data-bs-target="#offcanvasRight11" aria-controls="offcanvasRight">
                                                    <img src="" id="preview" alt="Image"
                                                        style="max-width: 100px; max-height: 100px;">
                                                </a>
                                                <br>
                                                <label for="" class="form-label" id="show-lable">Click To
                                                    view</label><br>

                                            </div>
                                            <div class="col-md-12">
                                                <label for="note" class="form-label">Note</label>
                                                <textarea name="note" required id="note" cols="20" rows="5" class="form-control">{{$income->note}}</textarea>
                                                @error('note')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-12">
                                            </div>
                                            <div class="col-12">
                                                <button type="submit" id="submitBtn" class="btn btn-light px-5">
                                                    Update
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

        <script>
            document.addEventListener("DOMContentLoaded", function() {
                document.getElementById('fileInput').addEventListener('change', function(event) {
                    const file = event.target.files[0];
                    const formData = new FormData();
                    formData.append('image', file);

                    const reader = new FileReader();
                    reader.onload = function() {
                        const imageDataUrl = reader.result;
                        document.getElementById('modalImage').src = imageDataUrl;
                        document.getElementById('preview').src = imageDataUrl;
                        document.getElementById('preview').style.display = '';
                        document.getElementById('show-lable').style.display = '';
                        document.getElementById('viewImage').style.display = 'block';

                        //test
                    };
                    reader.readAsDataURL(file);

                    sendImageForConversion(formData);
                });

                async function sendImageForConversion(formData) {
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
                            handleConversionResult(data); // Handle the conversion result
                        } else {
                            console.error('Error:', response.statusText);
                        }

                    } catch (error) {
                        // hideLoader();
                        console.error('Error:', error);
                    }
                }

                function handleConversionResult(data) {

                    const utrNumber = data.utr[0];
                    // console.log(data.amount);
                    const amount = parseInt(data.amount.replace(/,/g, ''), 10);

                    document.getElementById('utr').value = utrNumber;
                    // document.getElementById('amount').value = amount;
                    $('#utr').trigger('input');
                    // $('#amount').trigger('input');

                }
            });

            function showLoader() {
                document.getElementById('loader').style.display = 'block';
                document.getElementById('content').style.opacity = '0.5';
            }


            function hideLoader() {
                document.getElementById('loader').style.display = 'none';
                document.getElementById('content').style.opacity = '1';
            }
        </script>
        <script>
            $(document).ready(function() {
                $('#income-form').submit(function() {
                // Check if all required fields are filled
                    if ($(this).get(0).checkValidity()) {
                     // If all fields are filled, disable the submit button
                        $('#submitBtn').prop('disabled', true);
                    }
                });

                var delayTimer;

                $('#utr').on('input', function() {
                    var utr = $(this).val();

                    // Clear the previous delay timer
                    clearTimeout(delayTimer);

                    // Set a new delay timer to trigger the AJAX request after 1 second (1000 milliseconds)
                    delayTimer = setTimeout(function() {
                        if (utr.length > 0) {
                            $.ajax({
                                url: '/check-utr',
                                method: 'GET',
                                data: {
                                    utr: utr
                                },
                                success: function(response) {
                                    if (response.exists) {
                                        $('#utr-exists').html('UTR already exists').css(
                                            'background-color', 'white');
                                    } else {
                                        $('#utr-exists').html('');
                                    }
                                },
                            });
                        } else {
                            $('#utr-exists').text('');
                        }
                    }, 1000); // 1000 milliseconds (1 second)
                });
                // $('#form').submit(function(e) {
                //     e.preventDefault();
                //     let form = $(this);
                //     let url = form.attr('action');
                //     let data = form.serialize();

                //     $.ajax({
                //         type: 'POST',
                //         url: url,
                //         data: data,
                //         success: function(response) {
                //             if (response.success) {
                //                 // Form is valid; process the data here
                //             } else {
                //                 // Clear previous errors
                //                 $('#validation-errors').html('');

                //                 // Display validation errors
                //                 for (let field in response.errors) {
                //                     $('#validation-errors').append('<p>' + response.errors[field] +
                //                         '</p>');
                //                 }
                //             }
                //         }
                //     });
                // });


                // $('select[name="user_id"]').on('change', function() {
                //     var selectedOption = $(this).val();

                //     if (selectedOption === 'Existing') {
                //         // Make an Ajax request to fetch existing data
                //         $.ajax({
                //             type: 'GET',
                //             url: '/get-existing-data',
                //             success: function(data) {
                //                 // Handle the retrieved data
                //                 console.log(data); // You can update your UI with this data
                //             },
                //             error: function(xhr, status, error) {
                //                 console.error(error);
                //             }
                //         });
                //     } else {
                //         // Handle other cases if needed
                //     }
                // });
            });
        </script>
        <script>
            // $(document).ready(function() {
            //     var delayTimer;

            //     $('#utr').on('input', function() {
            //         var utr = $(this).val();

            //         // Clear the previous delay timer
            //         clearTimeout(delayTimer);

            //         // Set a new delay timer to trigger the AJAX request after 1 second (1000 milliseconds)
            //         delayTimer = setTimeout(function() {
            //             if (utr.length > 0) {
            //                 $.ajax({
            //                     url: '/check-utr',
            //                     method: 'GET',
            //                     data: {
            //                         utr: utr
            //                     },
            //                     success: function(response) {
            //                         if (response.exists) {
            //                             $('#utr-exists').html('UTR already exists').css(
            //                                 'background-color', 'white');
            //                         } else {
            //                             $('#utr-exists').html('');
            //                         }
            //                     },
            //                 });
            //             } else {
            //                 $('#utr-exists').text('');
            //             }
            //         }, 1000); // 1000 milliseconds (1 second)
            //     });

                $('#platform_type').on('change', function() {
                    var playerPlatformDropdown = $('#player_platform');
                    var playerPlatform = $('#platform');
                    playerPlatformDropdown.empty();
                    playerPlatform.empty();
                });

                // $('#platform_type').on('change', function() {
                //     var selectedType = $(this).val();

                //     if (selectedType === 'Existing') {
                //         var selectedUser = $('#user_id').val();

                //         // Send an Ajax request to fetch merged platform names
                //         $.ajax({
                //             url: '/get-platform-details',
                //             method: 'GET',
                //             success: function(data) {
                //                 var platformDetails = data.platformDetails;
                //                 console.log(platformDetails);

                //                 // Populate the "Platform" dropdown with merged platform names
                //                 var platformDetailsDropdown = $('#player_platform');
                //                 platformDetailsDropdown.empty();
                //                 platformDetailsDropdown.append(
                //                     '<option value="">Please Select</option>');

                //                 $.each(platformDetails, function(index, platform) {
                //                     platformDetailsDropdown.append('<option value="' +
                //                         platform.id +
                //                         '">' + platform.platform_username + '</option>');
                //                 });
                //             },
                //         });
                //     } else if (selectedType === 'New Platform') {
                //         // Send an Ajax request to fetch all available platform names
                //         $.ajax({
                //             url: '/get-all-platforms',
                //             method: 'GET',
                //             success: function(data) {
                //                 var allPlatformData = data.allPlatforms;
                //                 var userDetails = data.userDetails;
                //                 // Populate the "Platform" dropdown with all available platform names
                //                 var userDetailsDropdown = $('#player_platform');
                //                 userDetailsDropdown.empty();
                //                 userDetailsDropdown.append(
                //                     '<option value="">Please Select</option>');

                //                 $.each(userDetails, function(index, user) {
                //                     userDetailsDropdown.append('<option value="' +
                //                         user.id +
                //                         '">' + user.name + ',' + user.mobile +
                //                         '</option>');
                //                 });

                //                 var platformDropdown = $('#platform');
                //                 platformDropdown.empty();

                //                 $.each(allPlatformData, function(platformId, platformName) {
                //                     platformDropdown.append('<option value="' + platformId +
                //                         '">' + platformName + '</option>');
                //                 });
                //             },
                //         });
                //     } else {
                //         $('#platform').empty().hide();
                //     }
                // });


                var selectedType = $('#platform_type').val();

                $('#player_platform').select2({
                    placeholder: "Search for an option",
                    allowClear: true,
                    minimumInputLength: 3,
                    ajax: {
                        url: '/get-platform-details', // Replace with the URL of your server endpoint
                        dataType: 'json',
                        data: function(params) {
                            var query = {
                                search: params.term,
                                type: 'public',
                                selectedType: $('#platform_type').val(),
                            }
                            return query;
                        },
                        delay: 250, // Delay in milliseconds before sending the query
                        processResults: function(data) {
                            console.log("success");
                            console.log(data);
                            return {
                                results: data.results
                            };
                        },
                        cache: true // Enable caching for better performance
                    }
                });

                $('#player_platform').on('change', function() {
                    console.log("inside");
                    var selectedType = $('#platform_type').val();
                    // if (selectedType === 'Existing') {
                    var selectedId = $(this).val();


                    // Send an Ajax request to fetch merged platform names
                    $.ajax({
                        url: '/get-platform-user-details',
                        method: 'GET',
                        data: {
                            '0': selectedType,
                            '1': selectedId,
                        },
                        success: function(data) {
                            var platformDetails = data.platformDetails;
                            var platformDetailsDropdown = $('#platform');
                            console.log(platformDetails);
                            // Populate the "Platform" dropdown with merged platform names
                            if (selectedType == "Existing") {

                                var platform = platformDetails.platform;
                                var platformPlayer = platformDetails.player;
                                platformDetailsDropdown.empty();
                                platformDetailsDropdown.append(
                                    '<option value="' + platformDetails.platform_id + '">' +
                                    platform.name + '</option>');
                            } else if (selectedType == "New Platform") {
                                console.log(platformDetails);
                                var selectElement = $('#platform');

                                // Clear existing options
                                selectElement.empty();

                                // Add a default "Choose platform" option
                                selectElement.append('<option value="">Choose platform</option>');

                                // Populate the select with data from the server
                                $.each(platformDetails, function(index, platform) {
                                    selectElement.append($('<option></option>').attr(
                                        'value', platform.id).text(platform.name));
                                });
                            }
                            // $.each(platformDetails, function(index, platform) {
                            //     platformDetailsDropdown.append('<option value="' +
                            //         index +
                            //         '">' + platform.platform_username + '</option>');
                            // });
                        },
                    });
                    // }
                    //  else if (selectedType === 'New Platform') {
                    //     // Send an Ajax request to fetch all available platform names
                    //     $.ajax({
                    //         url: '/get-all-platforms',
                    //         method: 'GET',
                    //         success: function(data) {
                    //             var allPlatformData = data.allPlatforms;

                    //             // Populate the "Platform" dropdown with all available platform names
                    //             var platformDropdown = $('#platform');
                    //             platformDropdown.empty();

                    //             $.each(allPlatformData, function(platformId, platformName) {
                    //                 platformDropdown.append('<option value="' + platformId +
                    //                     '">' + platformName + '</option>');
                    //             });
                    //         },
                    //     });
                    // } else {
                    //     $('#platform').empty().hide();
                    // }
                });


                // $('#player_platform').on('change', function() {
                //     var selectedUserId = $(this).val();


                //     if (selectedUserId) {
                //         // Fetch user data based on the selected user ID
                //         $.ajax({
                //             url: '/get_user_info/' +
                //                 selectedUserId, // Adjust the URL to your actual route
                //             method: 'GET',
                //             success: function(data) {
                //                 var player = data.player;
                //                 console.log(player);
                //                 $('#user_name').text('Player Name: ' + player.name);
                //                 $('#user_mobile').text('Player Mobile: ' + player.mobile);
                //             },
                //             error: function(xhr, status, error) {
                //                 console.error("AJAX request failed with error: " + error);
                //             }
                //         });
                //     } else {
                //         $('#user_name').text('');
                //         $('#user_mobile').text('');
                //     }
                // });
            });
        </script>
        <script>
            $(document).ready(function() {
                $('#amount, #percentage').on('input', function() {
                    var amount = parseFloat($('#amount').val()) || 0;
                    var percentage = parseFloat($('#percentage').val()) || 0;
                    var result = amount + (amount * percentage / 100);
                    $('#result').val(result.toFixed(2));
                });
            });
        </script>
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
            // $(document).ready(function() {
            //     $('.select2').select2({
            //         placeholder: "-- Select an option --",
            //         allowClear: true
            //     });
            // });
        </script>
        {{-- <script src="assets/js/index.js"></script>
    <script>
        new PerfectScrollbar('.product-list');
        new PerfectScrollbar('.customers-list');
    </script> --}}
</body>

</html>
