$(document).ready(function () {
    var selectedRowId; // Variable to store the selected row's ID

    $("#benificiary-table").on("click", "tr", function () {
        var rowData = $("#benificiary-table").DataTable().row(this).data();
        // alert("hello");
        selectedRowId = rowData.id;
        // console.log("Selected Row Data:", rowData);
        // console.log("Selected Row ID:", selectedRowId);
    });

    $("#benificiary-table").DataTable({
        scrollY: "400px", // Set the desired height for vertical scrolling
        scrollX: true,
        scrollCollapse: true,
        processing: true,
        serverSide: true,
        ajax: {
            url: "/benificiary-report",
            type: "GET",
            data: function (d) {
                d.start_date = $("#benificiary_start_date").val();
                d.end_date = $("#benificiary_end_date").val();
                d.created_by = $("#benificiary_created_by").val();
                d.bank = $("#benificiary_bank").val();
                d.pending_banks=$('#benificiary_pending_bank').val();
            },
        },
        order: [[5, "desc"]], 
        rowId: "id",
        columns: [
            { data: "id" },
            {
                data: "user_name",
                name: "player.name",
                render: function (data, type, row) {
                    return (
                        '<a  href="#" class="offcanvasRightName"  data-note-id="' +
                        data +
                        '" data-target="#offcanvas"  data-bs-toggle="offcanvas"  data-bs-target="#offcanvasRightName"  aria-controls="offcanvasRight"  >' +
                        data +
                        "</a>"
                    );
                },
            },
            // { data: "benificary_details", name: "benificary_details" },
            { data: "ac_no", name: "account_number" }, // Assuming 'name' is the attribute in the Category model
            { data: "ifsc", name: "ifsc_code", searchable: false },
            { data: "bank_name", name: "bank_name", searchable: false },
            {
                data: "benificiary",searchable: false
            }, // Assuming 'name' is the attribute in the PaymentMode model
            {
                data: "created_at",searchable: false
            },
            {
                data: "action",searchable: false,
                render: function (data, type, row) {
                    return (
                        '<a  href="#" class="offcanvasRightName"  data-note-id="action" data-target="#offcanvas"  data-bs-toggle="offcanvas"  data-bs-target="#offcanvasRightName"  aria-controls="offcanvasRight"  >' +
                        data +
                        "</a>"
                    );
                },
            },

            // Assuming 'name' is the attribute in the User model
        ],
        rowCallback: function (row, data, index) {
            // Attach a click event handler to the 'image-link' class
            // console.log(data);
            $(row)
                .find(".exampleModal")
                .on("click", function (e) {
                    e.preventDefault();
                    var id = data.id;

                    $.ajax({
                        url: "/benificiary-banks-fetch",
                        type: "post",
                        data: {
                            // our_bank_detail_id: selectedPlatformId,
                            bank_details_id: id,
                        },
                        success: function (response) {
                            // Clear existing options from the dropdown
                            $("#our_bank_detail_id").empty();

                            // Add default option
                            $("#our_bank_detail_id").append(
                                '<option value="">Choose Banks</option>'
                            );

                            // Add options fetched from AJAX response
                            response.banks.forEach(function (bank) {
                                $("#our_bank_detail_id").append(
                                    '<option value="' +
                                    bank.id +
                                    '">' +
                                    bank.bank_name +
                                    "</option>"
                                );
                            });
                        },

                        error: function (xhr, status, error) {
                            console.error(xhr.responseText);
                            // Handle errors
                        },
                    });
                });
            $(row)
                .find(".offcanvasRightName")
                .on("click", function (e) {
                    e.preventDefault();
                    var id = data.id;
                    // console.log("id id");

                    // console.log(id);
                    $.ajax({
                        url: "/benificiary-show",
                        type: "post",
                        data: {
                            // our_bank_detail_id: selectedPlatformId,
                            bank_details_id: id,
                        },
                        success: function (response) {
                            var beneficiaryModalContent =
                                document.getElementById(
                                    "beneficiaryModalContent"
                                );
                            beneficiaryModalContent.innerHTML = "";

                            response.beneficiaries.benificiary.forEach(
                                function (beneficiary) {
                                    // var beneficiaryHTML =
                                    //     '<ul class="list-group list-group-flush">';
                                    var beneficiaryHTML =
                                        '<li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">';
                                    beneficiaryHTML +=
                                        '<h6 class="mb-0">' +
                                        beneficiary.our_bank_detail.bank_name +
                                        "</h6>";
                                    beneficiaryHTML +=
                                        '<span class="text-white">' +
                                        beneficiary.created_by.name +
                                        "</span>";
                                    beneficiaryHTML += "</li>";
                                    // beneficiaryHTML += "</ul>";
                                    // beneficiaryHTML += "<hr>"; // Add the divider after each list item

                                    beneficiaryModalContent.innerHTML +=
                                        beneficiaryHTML;
                                }
                            );
                        },

                        error: function (xhr, status, error) {
                            console.error(xhr.responseText);
                            // Handle errors
                        },
                    });
                });
        },

        footerCallback: function (row, data, start, end, display) {
            var api = this.api();

            $("#totalBenificiaryActive").text(
                api.ajax.json().active_benificiary_count.toFixed(2)
            );
            $("#totalBenificiaryInactive").text(
                api.ajax.json().inactive_benificiary_count.toFixed(2)
            );
        },
    });
    $("#applyBenificiaryFilterButton").on("click", function () {
        applyDateFilter();
    });

    function applyDateFilter() {
        // Redraw DataTables when the date filter is applied
        $("#benificiary-table").DataTable().ajax.reload();
    }
    $("#updateBtn").on("click", function (event) {
        event.preventDefault();
        var selectedPlatformId = $("#our_bank_detail_id").val();
        // console.log("Selected Row ID:", selectedRowId);
        // Make an Ajax request to update the bank account
        $.ajax({
            url: "/benificiary-update",
            type: "post",
            data: {
                our_bank_detail_id: selectedPlatformId,
                bank_details_id: selectedRowId,
            },
            success: function (response) {
                console.log(response);
                $('#exampleModal').modal('hide');
                if (response.status_code == 200) {
                    displayMessage($(".custom-alert"), "Success",
                        "Benificiary Added Successfully");
                }

                // console.log(response.message);
                // Optionally, update the UI to reflect the new status
            },
            error: function (xhr, status, error) {
                console.error(xhr.responseText);
                // Handle errors
            },
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
