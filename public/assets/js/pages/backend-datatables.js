$(document).ready(function () {
    function displayError(element, errorMessage) {
        element
            .html(errorMessage)
            .css("display", "block")
            .delay(3000)
            .fadeOut(700);
    }

    $("#all-deposits-table").DataTable({
        scrollY: "400px", // Set the desired height for vertical scrolling
        scrollX: true,

        scrollCollapse: true, // Enable collapsing scrollbar
        processing: true,
        serverSide: true,
        ajax: {
            url: "/all-deposit-datas",
            type: "GET",
        },
        columns: [
            { data: "id", name: "id" },
            { data: "utr", name: "utr" },
            { data: "player_name", name: "platformDetail.platform_username" },
            { data: "name", name: "name" },
            {data:'deposit_bank',name:'deposit_bank'},
            { data: "deposit_amount", name: "deposit_amount" },
            { data: "bonus", name: "bonus" },
            {
                data: "image",
                name: "image",
                searchable: false,

                render: function (data, type, row) {
                    return (
                        '<a type="button" href="#" class="offcanvasRight1"  data-note-id="' +
                        "icon" +
                        '" data-target="#offcanvas"  data-bs-toggle="offcanvas"  data-bs-target="#offcanvasRight1"  aria-controls="offcanvasRight"  ><i class=" bi bi-file-image fs-2" style="color:rgb(33 213 0)"></i></a>'
                    );
                },
            },
            {
                data: "timeline",
                name: "timeline",
                searchable: false,

                render: function (data, type, row) {
                    // console.log(data);
                    return (
                        '<a type="button" href="#" class="offcanvasRight2"  data-note-id="' +
                        data +
                        '" data-target="#offcanvas"  data-bs-toggle="offcanvas"  data-bs-target="#offcanvasRight2"  aria-controls="offcanvasRight"  ><span class="badge rounded-pill bg-info text-dark">TimeLine</span></a>'
                    );
                },
            },
            { data: "total_deposit_amount", name: "total_deposit_amount" },
            // { data: 'timeLine', name: 'timeline' },
            { data: "admin_status", name: "admin_status" },
            { data: "banker_status", name: "banker_status" },
            {
                data: "isInformed",
                name: "isInformed",
                searchable: false,
                render: function (data, type, full, meta) {
                    return data == 1 ? "Verified" : "Not Verified";
                },
            },
            {
                data: "created_by",
                name: "user",
                searchable: false,
            },
            { data: "actions", name: "actions" },
        ],
        // console.log();

        rowCallback: function (row, data, index) {
            console.log(data);
            $(row)
                .find(".offcanvasRight2")
                .on("click", function (e) {
                    e.preventDefault();
                    // 0h 0m 0s
                    var createdAt = new Date(
                        data.approval_time_line.created_at
                    );
                    var bankerTime = data.approval_time_line.banker_status_at
                        ? new Date(data.approval_time_line.banker_status_at)
                        : undefined;
                    var adminTime = data.approval_time_line.admin_status_at
                        ? new Date(data.approval_time_line.admin_status_at)
                        : undefined;
                    var ccTime = data.approval_time_line.cc_status_at
                        ? new Date(data.approval_time_line.cc_status_at)
                        : undefined;

                    // Calculate time differences
                    var bankerDiff =
                        bankerTime instanceof Date &&
                        !isNaN(bankerTime.getTime())
                            ? timeBetween(createdAt, bankerTime)
                            : "0h 0m 0s";
                    var adminDiff =
                        adminTime instanceof Date && !isNaN(adminTime.getTime())
                            ? timeBetween(bankerTime, adminTime)
                            : "0h 0m 0s";
                    var ccDiff =
                        ccTime instanceof Date && !isNaN(ccTime.getTime())
                            ? timeBetween(adminTime, ccTime)
                            : "0h 0m 0s";

                    // Display time differences
                    $("#created_at").text(formatDateTime(createdAt));
                    $("#banker_at").text(bankerDiff);
                    $("#admin_at").text(adminDiff);
                    $("#cc_at").text(ccDiff);

                    $("#created_by").text(data.approval_time_line.created_by.name);
                    $("#admin").text(data.approval_time_line.admin_user ? data.approval_time_line.admin_user.name : ''); // Assuming 'admin_user' contains user details
                    $("#banker").text(data.approval_time_line.banker_user ? data.approval_time_line.banker_user.name : ''); // Assuming 'banker_user' contains user details
                    $("#cc").text(data.approval_time_line.cc_user ? data.approval_time_line.cc_user.name : ''); // Assuming 'cc_user' contains user details
                    $("#status").text(data.approval_time_line.status);
                });
            // Attach a click event handler to the 'image-link' class
            $(row)
                .find(".offcanvasRight1")
                .on("click", function (e) {
                    e.preventDefault();

                    $("#preview").html(
                        '<img src="' +
                            data.image +
                            '" alt="Image" class="img-fluid">'
                    );

                    // $('#imageModal').modal('show');
                });
        },
    });

    function timeBetween(a, b) {
        var timeDiff = Math.abs(b.getTime() - a.getTime());

        var hours = Math.floor(timeDiff / (1000 * 60 * 60));
        var minutes = Math.floor((timeDiff % (1000 * 60 * 60)) / (1000 * 60));
        var seconds = Math.floor((timeDiff % (1000 * 60)) / 1000);

        return hours + "h " + minutes + "m " + seconds + "s";
    }

    function formatDateTime(dateString) {
        // Parse the date string
        var date = new Date(dateString);

        // Get the day, month, year, hour, minute, second, and AM/PM indicator
        var day = date.getDate();
        var month = date.toLocaleString("default", { month: "short" });
        var year = date.getFullYear();
        var hour = date.getHours();
        var minute = date.getMinutes();
        var second = date.getSeconds();
        var ampm = hour >= 12 ? "PM" : "AM";

        // Convert hour to 12-hour format
        hour = hour % 12;
        hour = hour ? hour : 12; // Handle midnight (0 hours)

        // Format the date
        var formattedDate = `${day} ${month} ${year} ${hour}:${minute}:${second} ${ampm}`;

        return formattedDate;
    }
    function formatTimeDifference(timeDifference) {
        var seconds = Math.abs(Math.floor(timeDifference / 1000));
        var minutes = Math.floor(seconds / 60);
        var hours = Math.floor(minutes / 60);

        return hours + "h " + (minutes % 60) + "m " + (seconds % 60) + "s";
    }

    $(document).on("click", ".delete-btn", function () {
        // Get the ID of the row you want to delete
        var id = $(this).data("id");

        // Set the ID as a data attribute in the confirmation modal
        $("#deleteConfirmationModal").data("id", id);

        // Open the confirmation modal
        $("#deleteConfirmationModal").modal("show");
    });

    $("#confirmDelete").on("click", function () {
        var id = $("#deleteConfirmationModal").data("id");

        // Make an AJAX request to delete the item
        $.ajax({
            url: "/delete-item/" + id, // Replace with your actual delete endpoint
            type: "DELETE",
            success: function (response) {
                // Refresh the DataTable
                $("#all-deposits-table").DataTable().ajax.reload();
                displayError($("#success-message"), "Data Deleted Success");
                console.log("data deleted successfully");
                $("#deleteConfirmationModal").modal("hide");
            },
        });

        // Close the confirmation modal
    });

    $(".close").click(function () {
        $("#myModal").modal("hide");
    });

    // Add this JavaScript code
    $(document).on("click", ".image-link", function () {
        var imageUrl = $(this).data("image");
        $("#imageModal .modal-body").html(
            '<img src="' + imageUrl + '" alt="Image" class="img-fluid">'
        );
    });

    $(document).on("click", ".edit-btn", function () {
        var userId = $(this).data("id");
        // Redirect to the edit page with the user's ID
        window.location.href = "/UserRegister-edit/" + userId;
    });


    $("#all-withdraw-table").DataTable({
        scrollY: "400px", // Set the desired height for vertical scrolling
        scrollX: true,

        scrollCollapse: true, // Enable collapsing scrollbar
        processing: true,
        serverSide: true,
        ajax: {
            url: "/all-withdraw-datas",
            type: "GET",
        },
        columns: [
            { data: "id", name: "id" },
            {
                data: "player_name",
                name: "platformDetail.platform_username",
                searchable: true,
            },
            { data: "account_number", name: "bank.account_number" },
            { data: "bank_name", name: "bank.bank_name" },
            {data:'withdraw_bank',name:'ourBankDetail.bank_name',   searchable: false,},
            {
                data: "timeline",
                name: "timeline",
                searchable: false,

                render: function (data, type, row) {
                    return (
                        '<a type="button" href="#" class="offcanvasRight2"  data-note-id="' +
                        data +
                        '" data-target="#offcanvas"  data-bs-toggle="offcanvas"  data-bs-target="#offcanvasRight2"  aria-controls="offcanvasRight"  ><span class="badge rounded-pill bg-info text-dark">TimeLine</span></a>'
                    );
                },
            },
            { data: "withdraw_utr", name: "withdrawUtr.utr" },
            { data: "platform_name", name: "platform_name" },
            { data: "amount", name: "amount" },
            { data: "rolling_type", name: "rolling_type" },
            {
                data: "image",
                name: "image",
                searchable: false,

                render: function (data, type, row) {
                    return (
                        '<a type="button" href="#" class="offcanvasRight1"  data-note-id="' +
                        'icon' +
                        '" data-target="#offcanvas"  data-bs-toggle="offcanvas"  data-bs-target="#offcanvasRight1"  aria-controls="offcanvasRight"  ><i class=" bi bi-file-image fs-2" style="color:rgb(33 213 0)"></i></a>'
                    );
                },
            },
            { data: "d_chips", name: "d_chips" },
            { data: "admin_status", name: "admin_status" },
            { data: "banker_status", name: "banker_status" },
            {
                data: "isInformed",
                name: "isInformed",
                searchable: false,
                render: function (data, type, full, meta) {
                    return data == 1 ? "Verified" : "Not Verified";
                },
            },
            {
                data: "created_by",
                name: "employee.name",
                searchable: false,
            },
        ],

        rowCallback: function (row, data, index) {
            // console.log(data);
            $(row)
                .find(".offcanvasRight2")
                .on("click", function (e) {
                    e.preventDefault();
                    // 0h 0m 0s
                    var createdAt = new Date(
                        data.approval_time_line.created_at
                    );
                    var bankerTime = data.approval_time_line.banker_status_at
                        ? new Date(data.approval_time_line.banker_status_at)
                        : undefined;
                    var adminTime = data.approval_time_line.admin_status_at
                        ? new Date(data.approval_time_line.admin_status_at)
                        : undefined;
                    var ccTime = data.approval_time_line.cc_status_at
                        ? new Date(data.approval_time_line.cc_status_at)
                        : undefined;

                    // Calculate time differences
                    var adminDiff =
                        adminTime instanceof Date && !isNaN(adminTime.getTime())
                            ? timeBetween(createdAt, adminTime)
                            : "0h 0m 0s";
                    var bankerDiff =
                        bankerTime instanceof Date &&
                        !isNaN(bankerTime.getTime())
                            ? timeBetween(adminTime, bankerTime)
                            : "0h 0m 0s";
                    var ccDiff =
                        ccTime instanceof Date && !isNaN(ccTime.getTime())
                            ? timeBetween(bankerTime, ccTime)
                            : "0h 0m 0s";

                    // Display time differences
                    $("#created_at").text(formatDateTime(createdAt));
                    $("#banker_at").text(bankerDiff);
                    $("#admin_at").text(adminDiff);
                    $("#cc_at").text(ccDiff);

                    $("#created_by").text(data.approval_time_line.created_by.name);
                    $("#admin").text(data.approval_time_line.admin_user ? data.approval_time_line.admin_user.name : ''); // Assuming 'admin_user' contains user details
                    $("#banker").text(data.approval_time_line.banker_user ? data.approval_time_line.banker_user.name : ''); // Assuming 'banker_user' contains user details
                    $("#cc").text(data.approval_time_line.cc_user ? data.approval_time_line.cc_user.name : ''); // Assuming 'cc_user' contains user details
                    $("#status").text(data.approval_time_line.status);
                });

            $(row)
                .find(".offcanvasRight1")
                .on("click", function (e) {
                    e.preventDefault();

                    $("#preview").html(
                        '<img src="' +
                            data.image +
                            '" alt="Image" class="img-fluid">'
                    );
                });
            // Attach a click event handler to the 'image-link' class
            // $(row).find('.image-link').on('click', function (e) {
            //     e.preventDefault();
            //     // Get the image URL from the 'data-image' attribute
            //     var imageUrl = $(this).data('image');
            //     // Open a modal with the larger image
            //     $('#imageModal .modal-body').html('<img src="' + imageUrl + '" alt="Large Image" style="max-width: 100%;">');
            //     $('#imageModal').modal('show');
            // });
        },
    });

    $("#all-deposits-report-table").DataTable({
        scrollY: "400px", // Set the desired height for vertical scrolling
        scrollX: true,

        scrollCollapse: true, // Enable collapsing scrollbar
        processing: true,
        serverSide: true,
        ajax: {
            url: "/all-deposit-reports",
            type: "GET",
            data: function (d) {
                d.start_date = $("#start_date").val();
                d.end_date = $("#end_date").val();
                d.created_by = $("#created_by").val();
                d.platform = $("#platform").val();
            },
        },
        dom: "Bfrtip", // Add 'B' to enable export button
        buttons: [
            "copy",
            "csv",
            "excel",
            "pdf",
            "print",
            {
                text: "Export All Search Results",
                action: function (e, dt, node, config) {
                    window.location.href =
                        "/export-all-search-results?" +
                        $.param(dt.ajax.params());
                },
            },
        ],
        order: [[0, "desc"]],

        columns: [
            { data: "id", name: "id" },
            {
                data: "created_at",
                name: "created_at",
                render: function (data, type, full, meta) {
                    // Format the date using a library like Moment.js or directly in JavaScript
                    return data ? new Date(data).toLocaleDateString() : "-";
                },
            },
            { data: "utr", name: "utr" },
            { data: "player_name", name: "platform_username" },
            {data:"deposit_bank",name:'deposit_bank'},
            { data: "deposit_amount", name: "deposit_amount" },
            { data: "bonus", name: "bonus" },
            {
                data: "image",
                name: "image",
                searchable: false,
                render: function (data, type, row) {
                    return (
                        '<a type="button" href="#" class="offcanvasRight1"  data-note-id="' +
                        "icon" +
                        '" data-target="#offcanvas"  data-bs-toggle="offcanvas"  data-bs-target="#offcanvasRight1"  aria-controls="offcanvasRight"  ><i class=" bi bi-file-image fs-2" style="color:rgb(33 213 0)"></i></a>'
                    );
                },
            },
            { data: "total_deposit_amount", name: "total_deposit_amount" },
            { data: "admin_status", name: "admin_status" },
            { data: "banker_status", name: "banker_status" },
            {
                data: "isInformed",
                name: "isInformed",
                searchable: false,
                render: function (data, type, full, meta) {
                    return data == 1 ? "Verified" : "Not Verified";
                },
            },

            {
                data: "created_by",
                name: "created_by",
                // searchable: false,
            },
            // {
            //     data: null,
            //     name: 'actions',
            //     searchable: false,
            //     render: function (data, type, row) {
            //         return '<button class="action-btn delete-btn" data-id="' + data.id + '"><i class="fa-solid fa-trash"></i></button>';
            //     }
            // }
            // Add more columns for other fields
        ],

        rowCallback: function (row, data, index) {
            // Attach a click event handler to the 'image-link' class
            $(row)
                .find(".offcanvasRight1")
                .on("click", function (e) {
                    e.preventDefault();

                    $("#preview").html(
                        '<img src="' +
                            data.image +
                            '" alt="Image" class="img-fluid">'
                    );
                    // $('#imageModal').modal('show');
                });
        },

        footerCallback: function (row, data, start, end, display) {
            var api = this.api();
            $("#totalRecords").text(api.ajax.json().totalRecords);
            $("#totalDepositAmount").text(
                api.ajax.json().totalDepositAmount.toFixed(2)
            );
            $("#totalBonus").text(api.ajax.json().totalBonusAmount.toFixed(2));
            $("#totalTotalDepositAmount").text(
                api.ajax.json().totalTotalDepositAmount.toFixed(2)
            );

            $("#totalWithdrawRecords").text(
                api.ajax.json().totalWithdrawRecords.toFixed(2)
            );
            $("#totalWithdrawAmount").text(
                api.ajax.json().totalWithdrawAmount.toFixed(2)
            );
        },
        // Display total records information
        // infoCallback: function (settings, start, end, max, total, pre) {
        //     var api = this.api();
        //     var pageInfo = api.page.info();
        //     console.log("total Records");
        //     console.log(total);
        //     $('#total_records').text(total);
        // },
    });
    $("#applyFilterButton").on("click", function () {
        applyDateFilter();
    });

    function applyDateFilter() {
        // Redraw DataTables when the date filter is applied
        $("#all-deposits-report-table").DataTable().ajax.reload();
    }

    $("#all-withdraw-report-table").DataTable({
        scrollY: "400px", // Set the desired height for vertical scrolling
        scrollX: true,

        scrollCollapse: true, // Enable collapsing scrollbar
        processing: true,
        serverSide: true,
        ajax: {
            url: "/all-withdraw-reports",
            type: "GET",
            data: function (d) {
                d.start_date = $("#withdraw_start_date").val();
                d.end_date = $("#withdraw_end_date").val();
                d.created_by = $("#withdraw_created_by").val();
                d.platform = $("#withdraw_platform").val();
            },
        },
        dom: "Bfrtip", // Add 'B' to enable export button
        buttons: [
            "copy",
            "csv",
            "excel",
            "pdf",
            "print",
            {
                text: "Export All Search Results",
                action: function (e, dt, node, config) {
                    window.location.href =
                        "/export-all-withdraw-results?" +
                        $.param(dt.ajax.params());
                },
            },
        ],
        order: [[0, "desc"]],

        columns: [
            { data: "id", name: "id" },
            {
                data: "created_at",
                name: "created_at",
                render: function (data, type, full, meta) {
                    // Format the date using a library like Moment.js or directly in JavaScript
                    return data ? new Date(data).toLocaleDateString() : "-";
                },
            },
            { data: "player_name", name: "platformDetail.platform_username" },
            // {data:'withdraw_bank',name:'withdraw_bank'}
            { data: "bank_name", name: "bank.bank_name" },
            { data: "account_number", name: "account_number" },
            { data: "withdraw_utr", name: "withdraw_utr" },
            {data:'withdraw_bank',name:'ourBankDetail.bank_name'},

            { data: "amount", name: "amount" },
            { data: "d_chips", name: "d_chips" ,},
            {
                data: "image",
                name: "image",
                searchable: false,

                render: function (data, type, row) {
                    return (
                        '<a type="button" href="#" class="offcanvasRight1"  data-note-id="' +
                        "icon" +
                        '" data-target="#offcanvas"  data-bs-toggle="offcanvas"  data-bs-target="#offcanvasRight1"  aria-controls="offcanvasRight"  ><i class=" bi bi-file-image fs-2" style="color:rgb(33 213 0)"></i></a>'
                    );
                },
            },
            { data: "admin_status", name: "admin_status" },
            { data: "banker_status", name: "banker_status" },
            {
                data: "isInformed",
                name: "isInformed",
                searchable: false,
                render: function (data, type, full, meta) {
                    return data == 1 ? "Verified" : "Not Verified";
                },
            },

            { data: "created_by", name: "created_by" },
        ],

        rowCallback: function (row, data, index) {
            console.log(data);
            // Attach a click event handler to the 'image-link' class
            $(row)
                .find(".offcanvasRight1")
                .on("click", function (e) {
                    e.preventDefault();

                    $("#preview").html(
                        '<img src="' +
                            data.image +
                            '" alt="Image" class="img-fluid">'
                    );
                });
        },

        footerCallback: function (row, data, start, end, display) {
            var api = this.api();
            $("#totalWithdrawRecords").text(api.ajax.json().totalRecords);
            $("#totalWithdrawAmount").text(
                api.ajax.json().totalWithdrawAmount.toFixed(2)
            );

            $("#totalDepositAmount").text(
                api.ajax.json().totalDepositAmount.toFixed(2)
            );
            $("#totalBonus").text(api.ajax.json().totalBonus.toFixed(2));
            $("#totalTotalDepositAmount").text(
                api.ajax.json().totalTotalDepositAmount.toFixed(2)
            );
            $("#totalDepositRecords").text(
                api.ajax.json().totalDepositRecords.toFixed(2)
            );
            $("#totalBonusAmount").text(
                api.ajax.json().totalBonusAmount.toFixed(2)
            );
        },
    });
    $("#applyWithdrawFilterButton").on("click", function () {
        applyWithdrawDateFilter();
    });

    function applyWithdrawDateFilter() {
        // Redraw DataTables when the date filter is applied
        $("#all-withdraw-report-table").DataTable().ajax.reload();
    }

    // $("#banker_Status").on("change", function () {
    //     alert("hello");
    // });

    $("#income-report").DataTable({
        scrollY: "400px", // Set the desired height for vertical scrolling
        scrollX: true,
        scrollCollapse: true,
        processing: true,
        serverSide: true,
        ajax: {
            url: "/all-incomes-report",
            type: "GET",
            data: function (d) {
                d.start_date = $("#income_start_date").val();
                d.end_date = $("#income_end_date").val();
                d.created_by = $("#income_created_by").val();
            },
        },
        dom: "Bfrtip", // Add 'B' to enable export button
        buttons: [
            "copy",
            "csv",
            "excel",
            "pdf",
            "print",
            {
                text: "Export All Search Results",
                action: function (e, dt, node, config) {
                    window.location.href =
                        "/export-all-income-results?" +
                        $.param(dt.ajax.params());
                },
            },
        ],
        order: [[0, "desc"]],

        columns: [
            { data: "id" },
            { data: "title" },
            // { data: "note" },
            { data: "category.category_name", searchable: false }, // Assuming 'name' is the attribute in the Category model
            { data: "date", searchable: false },
            { data: "amount", searchable: false },
            {
                data: "paymentMode",
                name: "paymentMode.payment_mode_name",
                searchable: false,
            }, // Assuming 'name' is the attribute in the PaymentMode model
            {
                data: "ourBankDetail",
                name: "ourBankDetail.bank_name",
            },
            { data: "ref_no" },
            {
                data: "attachment",
                searchable: false,
                render: function (data, type, row) {
                    return (
                        '<a type="button" href="#" class="offcanvasRight1"  data-note-id="' +
                        "icon" +
                        '" data-target="#offcanvas"  data-bs-toggle="offcanvas"  data-bs-target="#offcanvasRight1"  aria-controls="offcanvasRight"  ><i class=" bi bi-file-image fs-2" style="color:rgb(33 213 0)"></i></a>'
                    );
                },
            },
            { data: "banker_status" },

            { data: "createdBy", searchable: false },
            {data:'actions'},

            // Assuming 'name' is the attribute in the User model
        ],
        rowCallback: function (row, data, index) {
            // Attach a click event handler to the 'image-link' class
            // console.log(data);
            $(row)
                .find(".offcanvasRight1")
                .on("click", function (e) {
                    e.preventDefault();

                    $("#preview").html(
                        '<img src="' +
                            "/storage/" +
                            data.attachment +
                            '" alt="Image" class="img-fluid">'
                    );
                });
        },
        footerCallback: function (row, data, start, end, display) {
            var api = this.api();
            $("#IncomerecordsCount").text(api.ajax.json().IncomerecordsCount);
            $("#totalIncomeAmount").text(
                api.ajax.json().totalIncomeAmount.toFixed(2)
            );
        },
    });

    $("#applyIncomeFilterButton").on("click", function () {
        applyIncomeDateFilter();
    });

    function applyIncomeDateFilter() {

        // Redraw DataTables when the date filter is applied
        $("#income-report").DataTable().ajax.reload();
    }

    $("#expense-report").DataTable({
        scrollY: "400px", // Set the desired height for vertical scrolling
        scrollX: true,

        scrollCollapse: true,
        processing: true,
        serverSide: true,
        ajax: {
            url: "/all-expense-report",
            type: "GET",
            data: function (d) {
                d.start_date = $("#expense_start_date").val();
                d.end_date = $("#expense_end_date").val();
                d.created_by = $("#expense_created_by").val();
                // d.platform = $("#expense_platform").val();
            },
        },
        dom: "Bfrtip", // Add 'B' to enable export button
        buttons: [
            "copy",
            "csv",
            "excel",
            "pdf",
            "print",
            {
                text: "Export All Search Results",
                action: function (e, dt, node, config) {
                    window.location.href =
                        "/export-all-expense-results?" +
                        $.param(dt.ajax.params());
                },
            },
        ],
        order: [[0, "desc"]],

        columns: [
            { data: "id" },
            { data: "title" },
            // { data: "note" },
            {
                data: "expense_category_name",
                searchable: false,
            }, // Assuming 'name' is the attribute in the Category model
            { data: "date", searchable: false },
            { data: "amount", searchable: false },
            {
                data: "paymentMode",
                name: "paymentMode.payment_mode_name",
                searchable: false,
            },
            {
                data: "ourBankDetail",
                name: "ourBankDetail.bank_name",
            },
            { data: "ref_no" },
            {
                data: "attachment",
                searchable: false,
                render: function (data, type, row) {
                    return (
                        '<a type="button" href="#" class="offcanvasRight1"  data-note-id="' +
                        "icon" +
                        '" data-target="#offcanvas"  data-bs-toggle="offcanvas"  data-bs-target="#offcanvasRight1"  aria-controls="offcanvasRight"  ><i class=" bi bi-file-image fs-2" style="color:rgb(33 213 0)"></i></a>'
                    );
                },
            },
            { data: "financier_status", searchable: false },
            { data: "operation_head_status", searchable: false },
            { data: "superviser_status", searchable: false },

            { data: "banker_status", searchable: false },

            { data: "createdBy", searchable: false },
            {data:'actions'},

            // Assuming 'name' is the attribute in the User model
        ],
        rowCallback: function (row, data, index) {
            // Attach a click event handler to the 'image-link' class
            // console.log(data);
            $(row)
                .find(".offcanvasRight1")
                .on("click", function (e) {
                    e.preventDefault();

                    $("#preview").html(
                        '<img src="' +
                            "/storage/" +
                            data.attachment +
                            '" alt="Image" class="img-fluid">'
                    );
                });
        },
        footerCallback: function (row, data, start, end, display) {
            var api = this.api();
            $("#TotalExpenseRecords").text(api.ajax.json().TotalExpenseRecords);
            $("#totalExpenseAmount").text(
                api.ajax.json().totalExpenseAmount.toFixed(2)
            );
        },
    });

    $("#applyExpenseFilterButton").on("click", function () {
        applyExpenseDateFilter();
    });

    function applyExpenseDateFilter() {
        // Redraw DataTables when the date filter is applied
        $("#expense-report").DataTable().ajax.reload();
    }

    $("#all-internal-transfer-report").DataTable({
        scrollY: "400px", // Set the desired height for vertical scrolling
        scrollX: true,

        scrollCollapse: true,
        processing: true,
        serverSide: true,
        ajax: {
            url: "/all-internal-transfer-report",
            type: "GET",
            data: function (d) {
                d.start_date = $("#internal_start_date").val();
                d.end_date = $("#internal_end_date").val();
                d.created_by = $("#internal_created_by").val();
                // d.platform = $("#expense_platform").val();
            },
        },
        dom: "Bfrtip", // Add 'B' to enable export button
        buttons: [
            "copy",
            "csv",
            "excel",
            "pdf",
            "print",
            {
                text: "Export All Search Results",
                action: function (e, dt, node, config) {
                    window.location.href =
                        "/export-all-internal-transfer-results?" +
                        $.param(dt.ajax.params());
                },
            },
        ],
        order: [[0, "desc"]],

        columns: [
            { data: "id" },
            { data: "title" },
            // { data: "note" },
            // { data: "bank_to" }, // Assuming 'name' is the attribute in the Category model
            { data: "date", searchable: false },
            { data: "bankFrom", name: "bankFrom.bank_name" },
            { data: "bankTo", name: "bankTo.bank_name" }, // Assuming 'name' is the attribute in the Category model
            // Assuming 'name' is the attribute in the Category model

            { data: "amount", searchable: false },

            { data: "utr" },
            {
                data: "attachment",
                searchable: false,
                render: function (data, type, row) {
                    return (
                        '<a type="button" href="#" class="offcanvasRight1"  data-note-id="' +
                        "icon" +
                        '" data-target="#offcanvas"  data-bs-toggle="offcanvas"  data-bs-target="#offcanvasRight1"  aria-controls="offcanvasRight"  ><i class=" bi bi-file-image fs-2" style="color:rgb(33 213 0)"></i></a>'
                    );
                },
            },

            { data: "superviser_status", searchable: false },

            { data: "banker_status", searchable: false },

            { data: "createdBy", searchable: false },
            {data:'actions'},

            // Assuming 'name' is the attribute in the User model
        ],
        rowCallback: function (row, data, index) {
            // Attach a click event handler to the 'image-link' class
            console.log(data);
            $(row)
                .find(".offcanvasRight1")
                .on("click", function (e) {
                    e.preventDefault();

                    $("#preview").html(
                        '<img src="' +
                            "/storage/" +
                            data.attachment +
                            '" alt="Image" class="img-fluid">'
                    );
                });
        },
        footerCallback: function (row, data, start, end, display) {
            var api = this.api();

            $("#totalTransferCount").text(api.ajax.json().totalTransferCount);
            $("#totalTransferAmount").text(
                api.ajax.json().totalTransferAmount.toFixed(2)
            );
        },
    });

    $("#applyInternalFilterButton").on("click", function () {
        applyInternalDateFilter();
    });

    function applyInternalDateFilter() {
        // Redraw DataTables when the date filter is applied
        $("#all-internal-transfer-report").DataTable().ajax.reload();
    }

    $("#our-bank-all-income-table").DataTable({
        scrollY: "400px", // Set the desired height for vertical scrolling
        scrollX: true,
        scrollCollapse: true,
        processing: true,
        serverSide: true,
        ajax: {
            url: "/our-bank-all-incomes",
            type: "GET",
            data: function (d) {
                d.bankId = $("#bankId").val();
            },
        },
        order: [[0, "desc"]],

        columns: [
            { data: "id" },
            { data: "title" },
            // { data: "note" },
            { data: "category.category_name", searchable: false }, // Assuming 'name' is the attribute in the Category model
            { data: "date", searchable: false },
            { data: "amount", searchable: false },
            {
                data: "paymentMode",
                name: "paymentMode.payment_mode_name",
                searchable: false,
            }, // Assuming 'name' is the attribute in the PaymentMode model
            {
                data: "ourBankDetail",
                name: "ourBankDetail.bank_name",
            },
            { data: "ref_no" },
            {
                data: "attachment",
                searchable: false,
                render: function (data, type, row) {
                    return (
                        '<a type="button" href="#" class="offcanvasRight1"  data-note-id="' +
                        "icon" +
                        '" data-target="#offcanvas"  data-bs-toggle="offcanvas"  data-bs-target="#offcanvasRight1"  aria-controls="offcanvasRight"  ><i class=" bi bi-file-image fs-2" style="color:rgb(33 213 0)"></i></a>'
                    );
                },
            },
            { data: "banker_status" },

            { data: "createdBy", searchable: false },

            // Assuming 'name' is the attribute in the User model
        ],
        rowCallback: function (row, data, index) {
            // Attach a click event handler to the 'image-link' class
            // console.log(data);
            $(row)
                .find(".offcanvasRight1")
                .on("click", function (e) {
                    e.preventDefault();

                    $("#preview").html(
                        '<img src="' +
                            "/storage/" +
                            data.attachment +
                            '" alt="Image" class="img-fluid">'
                    );
                });
        },
        // footerCallback: function (row, data, start, end, display) {
        //     var api = this.api();
        //     $("#IncomerecordsCount").text(api.ajax.json().IncomerecordsCount);
        //     $("#totalIncomeAmount").text(
        //         api.ajax.json().totalIncomeAmount.toFixed(2)
        //     );
        // },
    });

    $("#our-bank-all-expense-table").DataTable({
        scrollY: "400px", // Set the desired height for vertical scrolling
        scrollX: true,
        scrollCollapse: true,
        processing: true,
        serverSide: true,
        ajax: {
            url: "/our-bank-all-expense",
            type: "GET",
            data: function (d) {
                d.bankId = $("#bankId").val();
            },
        },
        order: [[0, "desc"]],

        columns: [
            { data: "id" },
            { data: "title" },
            // { data: "note" },
            {
                data: "expense_category",
                searchable: false,
            }, // Assuming 'name' is the attribute in the Category model
            { data: "date", searchable: false },
            { data: "amount", searchable: false },
            {
                data: "paymentMode",
                name: "paymentMode.payment_mode_name",
                searchable: false,
            }, // Assuming 'name' is the attribute in the PaymentMode model
            {
                data: "ourBankDetail",
                name: "ourBankDetail.bank_name",
            },
            { data: "ref_no" },
            {
                data: "attachment",
                searchable: false,
                render: function (data, type, row) {
                    return (
                        '<a type="button" href="#" class="offcanvasRight1"  data-note-id="' +
                        'icon' +
                        '" data-target="#offcanvas"  data-bs-toggle="offcanvas"  data-bs-target="#offcanvasRight1"  aria-controls="offcanvasRight"  ><i class=" bi bi-file-image fs-2" style="color:rgb(33 213 0)"></i></a>'
                    );
                },
            },
            { data: "financier_status", searchable: false },
            { data: "operation_head_status", searchable: false },
            { data: "superviser_status", searchable: false },
            { data: "banker_status" ,searchable:false},

            { data: "createdBy", searchable: false },
            // Assuming 'name' is the attribute in the User model
        ],
        rowCallback: function (row, data, index) {
            // Attach a click event handler to the 'image-link' class
            console.log(data);
            $(row)
                .find(".offcanvasRight1")
                .on("click", function (e) {
                    e.preventDefault();

                    $("#preview").html(
                        '<img src="' +
                            "/storage/" +
                            data.attachment +
                            '" alt="Image" class="img-fluid">'
                    );
                });
        },
        // footerCallback: function (row, data, start, end, display) {
        //     var api = this.api();
        //     $("#IncomerecordsCount").text(api.ajax.json().IncomerecordsCount);
        //     $("#totalIncomeAmount").text(
        //         api.ajax.json().totalIncomeAmount.toFixed(2)
        //     );
        // },
    });

    $("#our-bank-all-internal-bank-sender").DataTable({
        scrollY: "400px", // Set the desired height for vertical scrolling
        scrollX: true,

        scrollCollapse: true,
        processing: true,
        serverSide: true,
        ajax: {
            url: "/our-bank-all-internal-bank-sender",
            type: "GET",
            data: function (d) {
                d.bankId = $("#bankId").val();

                // d.platform = $("#expense_platform").val();
            },
        },
        order: [[0, "desc"]],

        columns: [
            { data: "id" },
            { data: "title" },
            // { data: "note" },
            // { data: "bank_to" }, // Assuming 'name' is the attribute in the Category model
            { data: "date", searchable: false },
            { data: "bankFrom", name: "bankFrom.bank_name" },
            { data: "bankTo", name: "bankTo.bank_name" }, // Assuming 'name' is the attribute in the Category model
            // Assuming 'name' is the attribute in the Category model

            { data: "amount", searchable: false },

            { data: "utr" },
            {
                data: "attachment",
                searchable: false,
                render: function (data, type, row) {
                    return (
                        '<a type="button" href="#" class="offcanvasRight1"  data-note-id="' +
                        "icon" +
                        '" data-target="#offcanvas"  data-bs-toggle="offcanvas"  data-bs-target="#offcanvasRight1"  aria-controls="offcanvasRight"  ><i class=" bi bi-file-image fs-2" style="color:rgb(33 213 0)"></i></a>'
                    );
                },
            },

            { data: "superviser_status", searchable: false },

            { data: "banker_status", searchable: false },

            { data: "createdBy", searchable: false },

            // Assuming 'name' is the attribute in the User model
        ],
        rowCallback: function (row, data, index) {
            // Attach a click event handler to the 'image-link' class
            console.log(data);
            $(row)
                .find(".offcanvasRight1")
                .on("click", function (e) {
                    e.preventDefault();

                    $("#preview").html(
                        '<img src="' +
                            "/storage/" +
                            data.attachment +
                            '" alt="Image" class="img-fluid">'
                    );
                });
        },
    });

    $("#our-bank-all-internal-bank-reciver").DataTable({
        scrollY: "400px", // Set the desired height for vertical scrolling
        scrollX: true,

        scrollCollapse: true,
        processing: true,
        serverSide: true,
        ajax: {
            url: "/our-bank-all-internal-bank-reciver",
            type: "GET",
            data: function (d) {
                d.bankId = $("#bankId").val();

                // d.platform = $("#expense_platform").val();
            },
        },
        order: [[0, "desc"]],

        columns: [
            { data: "id" },
            { data: "title" },
            // { data: "note" },
            // { data: "bank_to" }, // Assuming 'name' is the attribute in the Category model
            { data: "date", searchable: false },
            { data: "bankFrom", name: "bankFrom.bank_name" },
            { data: "bankTo", name: "bankTo.bank_name" }, // Assuming 'name' is the attribute in the Category model
            // Assuming 'name' is the attribute in the Category model

            { data: "amount", searchable: false },

            { data: "utr" },
            {
                data: "attachment",
                searchable: false,
                render: function (data, type, row) {
                    return (
                        '<a type="button" href="#" class="offcanvasRight1"  data-note-id="' +
                        "icon" +
                        '" data-target="#offcanvas"  data-bs-toggle="offcanvas"  data-bs-target="#offcanvasRight1"  aria-controls="offcanvasRight"  ><i class=" bi bi-file-image fs-2" style="color:rgb(33 213 0)"></i></a>'
                    );
                },
            },

            { data: "superviser_status", searchable: false },

            { data: "banker_status", searchable: false },

            { data: "createdBy", searchable: false },
            // Assuming 'name' is the attribute in the User model
        ],
        rowCallback: function (row, data, index) {
            // Attach a click event handler to the 'image-link' class
            console.log(data);
            $(row)
                .find(".offcanvasRight1")
                .on("click", function (e) {
                    e.preventDefault();

                    $("#preview").html(
                        '<img src="' +
                            "/storage/" +
                            data.attachment +
                            '" alt="Image" class="img-fluid">'
                    );
                });
        },
    });

    $("#our-bank-all-deposit").DataTable({
        scrollY: "400px", // Set the desired height for vertical scrolling
        scrollX: true,

        scrollCollapse: true, // Enable collapsing scrollbar
        processing: true,
        serverSide: true,
        ajax: {
            url: "/our-bank-all-deposit",
            type: "GET",
            data: function (d) {
                d.bankId = $("#bankId").val();

                // d.platform = $("#expense_platform").val();
            },
        },
        order: [[0, "desc"]],

        columns: [
            { data: "id", name: "id" },
            { data: "utr", name: "utr" },
            { data: "player_name", name: "platformDetail.platform_username" },
            { data: "name", name: "name" },
            {data:"created_at",name:'created_at'},
            { data: "deposit_amount", name: "deposit_amount" },
            // { data: "bonus", name: "bonus" },
            {
                data: "image",
                name: "image",
                searchable: false,

                render: function (data, type, row) {
                    return (
                        '<a type="button" href="#" class="offcanvasRight1"  data-note-id="' +
                        "icon" +
                        '" data-target="#offcanvas"  data-bs-toggle="offcanvas"  data-bs-target="#offcanvasRight1"  aria-controls="offcanvasRight"  ><i class=" bi bi-file-image fs-2" style="color:rgb(33 213 0)"></i></a>'
                    );
                },
            },

            // { data: "total_deposit_amount", name: "total_deposit_amount" },
            // { data: 'timeLine', name: 'timeline' },
            { data: "admin_status", name: "admin_status" },
            { data: "banker_status", name: "banker_status" },
            {
                data: "isInformed",
                name: "isInformed",
                searchable: false,
                render: function (data, type, full, meta) {
                    return data == 1 ? "Verified" : "Not Verified";
                },
            },
            {
                data: "created_by",
                name: "user",
                searchable: false,
            },
        ],
        // console.log();

        rowCallback: function (row, data, index) {
            // Attach a click event handler to the 'image-link' class
            $(row)
                .find(".offcanvasRight1")
                .on("click", function (e) {
                    e.preventDefault();

                    $("#preview").html(
                        '<img src="' +
                            data.image +
                            '" alt="Image" class="img-fluid">'
                    );

                    // $('#imageModal').modal('show');
                });
        },
    });

    $("#our-bank-all-withdraw").DataTable({
        scrollY: "400px", // Set the desired height for vertical scrolling
        scrollX: true,

        scrollCollapse: true, // Enable collapsing scrollbar
        processing: true,
        serverSide: true,
        ajax: {
            url: "/our-bank-all-withdraw",
            type: "GET",
            data: function (d) {
                d.bankId = $("#bankId").val();

                // d.platform = $("#expense_platform").val();
            },
        },
        order: [[0, "desc"]],

        columns: [
            { data: "id", name: "id" },
            {
                data: "player_name",
                name: "platformDetail.platform_username",
                searchable: true,
            },
            { data: "account_number", name: "bank.account_number" },
            {data:"created_at",name:'created_at'},

            { data: "bank_name", name: "bank.bank_name" },
            // {
            //     data: "timeline",
            //     name: "timeline",
            //     searchable: false,

            //     render: function (data, type, row) {
            //         return (
            //             '<a type="button" href="#" class="offcanvasRight2"  data-note-id="' +
            //             data +
            //             '" data-target="#offcanvas"  data-bs-toggle="offcanvas"  data-bs-target="#offcanvasRight2"  aria-controls="offcanvasRight"  ><button class="btn btn-info">TimeLine</button>'
            //         );
            //     },
            // },
            { data: "withdraw_utr", name: "withdrawUtr.utr" },
            { data: "platform_name", name: "platform_name" },

            { data: "amount", name: "amount" },
            // { data: "rolling_type", name: "rolling_type" },
            {
                data: "image",
                name: "image",
                searchable: false,

                render: function (data, type, row) {
                    return (
                        '<a type="button" href="#" class="offcanvasRight1"  data-note-id="' +
                        "icon" +
                        '" data-target="#offcanvas"  data-bs-toggle="offcanvas"  data-bs-target="#offcanvasRight1"  aria-controls="offcanvasRight"  ><i class=" bi bi-file-image fs-2" style="color:rgb(33 213 0)"></i></a>'
                    );
                },
            },
            // { data: "d_chips", name: "d_chips" },
            { data: "admin_status", name: "admin_status" },
            { data: "banker_status", name: "banker_status" },
            {
                data: "isInformed",
                name: "isInformed",
                searchable: false,
                render: function (data, type, full, meta) {
                    return data == 1 ? "Verified" : "Not Verified";
                },
            },
            {
                data: "created_by",
                name: "employee.name",
                searchable: false,
            },
        ],

        rowCallback: function (row, data, index) {
            // console.log(data);
            $(row)
                .find(".offcanvasRight1")
                .on("click", function (e) {
                    e.preventDefault();

                    $("#preview").html(
                        '<img src="' +
                            data.image +
                            '" alt="Image" class="img-fluid">'
                    );
                });
        },
    });
});
// our-bank-all-income-table
