$(document).ready(function() {

    //CHECKBOX FOR EMPLOYEE MANAGEMENT
    $("#checkedAllEmployee").change(function() {
        if (this.checked) {
            $(".checkSingleEmployee").each(function() {
                this.checked=true;
            });
        } else {
            $(".checkSingleEmployee").each(function() {
                this.checked=false;
            });
        }
    });

    $(".checkSingleEmployee").click(function () {
        if ($(this).is(":checked")) {
            var isAllChecked = 0;

            $(".checkSingleEmployee").each(function() {
                if (!this.checked)
                    isAllChecked = 1;
            });

            if (isAllChecked == 0) {
                $("#checkedAllEmployee").prop("checked", true);
            }     
        }
        else {
            $("#checkedAllEmployee").prop("checked", false);
        }
    });

    //CHECKBOX FOR CUSTOMER MANAGEMENT
    $("#checkedAllCustomer").change(function() {
        if (this.checked) {
            $(".checkSingleCustomer").each(function() {
                this.checked=true;
            });
        } else {
            $(".checkSingleCustomer").each(function() {
                this.checked=false;
            });
        }
    });

    $(".checkSingleCustomer").click(function () {
        if ($(this).is(":checked")) {
            var isAllChecked = 0;

            $(".checkSingleCustomer").each(function() {
                if (!this.checked)
                    isAllChecked = 1;
            });

            if (isAllChecked == 0) {
                $("#checkedAllCustomer").prop("checked", true);
            }     
        }
        else {
            $("#checkedAllCustomer").prop("checked", false);
        }
    });

    // CHECKBOX FOR FINANCE MANAGEMENT
    $("#checkedAllFinance").change(function() {
        if (this.checked) {
            $(".checkSingleFinance").each(function() {
                this.checked=true;
            });
        } else {
            $(".checkSingleFinance").each(function() {
                this.checked=false;
            });
        }
    });

    $(".checkSingleFinance").click(function () {
        if ($(this).is(":checked")) {
            var isAllChecked = 0;

            $(".checkSingleFinance").each(function() {
                if (!this.checked)
                    isAllChecked = 1;
            });

            if (isAllChecked == 0) {
                $("#checkedAllFinance").prop("checked", true);
            }     
        }
        else {
            $("#checkedAllFinance").prop("checked", false);
        }
    });

    //CHECKBOX FOR CARGO MANAGEMENT
    $("#checkedAllCargo").change(function() {
        if (this.checked) {
            $(".checkSingleCargo").each(function() {
                this.checked=true;
            });
        } else {
            $(".checkSingleCargo").each(function() {
                this.checked=false;
            });
        }
    });

    $(".checkSingleCargo").click(function () {
        if ($(this).is(":checked")) {
            var isAllChecked = 0;
            $(".checkSingleCargo").each(function() {
                if (!this.checked)
                    isAllChecked = 1;
            });

            if (isAllChecked == 0) {
                $("#checkedAllCargo").prop("checked", true);
            }     
        }
        else {
            $("#checkedAllCargo").prop("checked", false);
        }
    });

    //CHECKBOX FOR REPORTS
    $("#checkedAllReport").change(function() {
        if (this.checked) {
            $(".checkSingleReport").each(function() {
                this.checked=true;
            });
        } else {
            $(".checkSingleReport").each(function() {
                this.checked=false;
            });
        }
    });

    $(".checkSingleReport").click(function () {
        if ($(this).is(":checked")) {
            var isAllChecked = 0;
            $(".checkSingleReport").each(function() {
                if (!this.checked)
                    isAllChecked = 1;
            });

            if (isAllChecked == 0) {
                $("#checkedAllReport").prop("checked", true);
            }     
        }
        else {
            $("#checkedAllReport").prop("checked", false);
        }
    });

    $("#btnSaveRole").click(function () {
        //setting variables
        var id = $("#id").val();
        var name = $("#name").val();
        var createdby = $("#createdby").val();
        var description = $("#description").val();
        var maxrole = $("#rolemax").val();
        //CARGO DATA
        var barcode_series = $("#barcode_series").text();
        var booking = $("#booking").text();
        var reservation = $("#reservation").text();
        var warehouse_acceptance = $("#warehouse_acceptance").text();
        var loading = $("#loading").text();
        var in_transit = $("#in_transit").text();
        var unloading = $("#unloading").text();
        var delivery = $("#delivery").text();
        var warehouse_inventory = $("#warehouse_inventory").text();
        var box_releasing = $("#box_releasing").text();
        var barcode_releasing = $("#barcode_releasing").text();
        var trackntrace = $("#trackntrace").text();
        //CUSTOMER MGMT DATA
        var customer = $("#customer").text();
        var receiver = $("#receiver").text();
        var consignment = $("#consignment").text();
        //FINANCE MGMT
        var chart_accounts = $("#chart_accounts").text();
        var salary_compensation = $("#salary_compensation").text();
        var allowance_disbursement = $("#allowance_disbursement").text();
        var financial_liquidation = $("#financial_liquidation").text();
        var loan = $("#loan").text();
        var remittance = $("#remittance").text();
        var expenses = $("#expenses").text();
        //EMPLOYEE MGMT
        var employee = $("#employee").text();
        var user = $("#user").text();
        var tickets = $("#tickets").text();
        //REPORTS MGMT
        var report_incident = $("#report_incident").text();
        var report_cargo = $("#report_cargo").text();
        var report_sales = $("#report_sales").text();
        var report_box_purchase = $("#report_box_purchase").text();
        var report_box_disposed = $("#report_box_disposed").text();
        var report_branch_incentives = $("#report_branch_incentives").text();
        var report_exceptions = $("#report_exceptions").text();
        var report_box_aging = $("#report_box_aging").text();



        var data = {
            "data": [{
                id: id,
                name: name,
                description: description,
                maxrole: maxrole,
                barcode_series: barcode_series,
                reservation: reservation,
                booking: booking,
                warehouse_acceptance: warehouse_acceptance,
                loading: loading,
                in_transit: in_transit,
                unloading: unloading,
                delivery: delivery,
                warehouse_inventory: warehouse_inventory,
                box_releasing: box_releasing,
                barcode_releasing: barcode_releasing,
                trackntrace: trackntrace,
                customer: customer,
                receiver: receiver,
                consignment: consignment,
                chart_accounts: chart_accounts,
                salary_compensation: salary_compensation,
                allowance_disbursement: allowance_disbursement,
                financial_liquidation: financial_liquidation,
                loan: loan,
                remittance: remittance,
                expenses: expenses,
                employee: employee,
                user: user,
                tickets: tickets,
                report_incident: report_incident,
                report_cargo: report_cargo,
                report_sales: report_sales,
                report_box_purchase: report_box_purchase,
                report_box_disposed: report_box_disposed,
                report_branch_incentives: report_branch_incentives,
                report_exceptions: report_exceptions,
                report_box_aging: report_box_aging,
                createdby: createdby
            }]
        }

        $.ajax({
            type: 'POST',
            data: {
                data: JSON.stringify(data)
            },
            url: 'index.php?controller=role&action=save',
            success: function (data) {
                console.log(data);  
                document.getElementById("notification").style.display = "block";
                $("#header").html(`<i class="icon fa fa-check"></i> Save`);
                $("#message").html("Your created data was successfully saved!");
                $("#notificationtype").addClass("alert-success");
                setInterval(function () {
                    window.location.href = 'index.php?controller=role&action=list';
                }, 500);        
            },
        });
    });

});