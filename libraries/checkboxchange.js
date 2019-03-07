//CARGO MANAGEMENT CHECKBOX
    
    //barcode series checkbox
    $('#barcode_series').on('change', function(){
        $('#barcode_series').val(this.checked ? 1 : 0);
        document.getElementById('barcode_series').innerHTML = this.value.toString();
    });
    //reservation checkbox
    $('#reservation').on('change', function(){
        $('#reservation').val(this.checked ? 1 : 0);
        document.getElementById('reservation').innerHTML = this.value.toString();
    });
    //booking checkbox
    $('#booking').on('change', function(){
        $('#booking').val(this.checked ? 1 : 0);
        document.getElementById('booking').innerHTML = this.value.toString();
    });

    //warehouse_acceptance checkbox
    $('#warehouse_acceptance').on('change', function(){
        $('#warehouse_acceptance').val(this.checked ? 1 : 0);
        document.getElementById('warehouse_acceptance').innerHTML = this.value.toString();
    });
    
    //loading checkbox
    $('#loading').on('change', function(){
        $('#loading').val(this.checked ? 1 : 0);
        document.getElementById('loading').innerHTML = this.value.toString();
    });
    
    //in_transit checkbox
    $('#in_transit').on('change', function(){
        $('#in_transit').val(this.checked ? 1 : 0);
        document.getElementById('in_transit').innerHTML = this.value.toString();
    });
    
    //unloading checkbox
    $('#unloading').on('change', function(){
        $('#unloading').val(this.checked ? 1 : 0);
        document.getElementById('unloading').innerHTML = this.value.toString();
    });
    
    //delivery checkbox
    $('#delivery').on('change', function(){
        $('#delivery').val(this.checked ? 1 : 0);
        document.getElementById('delivery').innerHTML = this.value.toString();
    });
    
    //warehouse_inventory checkbox
    $('#warehouse_inventory').on('change', function(){
        $('#warehouse_inventory').val(this.checked ? 1 : 0);
        document.getElementById('warehouse_inventory').innerHTML = this.value.toString();
    });
    
    //box_releasing checkbox
    $('#box_releasing').on('change', function(){
        $('#box_releasing').val(this.checked ? 1 : 0);
        document.getElementById('box_releasing').innerHTML = this.value.toString();
    });
    
    //barcode_releasing checkbox
    $('#barcode_releasing').on('change', function(){
        $('#barcode_releasing').val(this.checked ? 1 : 0);
        document.getElementById('barcode_releasing').innerHTML = this.value.toString();
    });
    
    //trackntrace checkbox
    $('#trackntrace').on('change', function(){
        $('#trackntrace').val(this.checked ? 1 : 0);
        document.getElementById('trackntrace').innerHTML = this.value.toString();
    });

// CUSTOMER MANAGEMENT

    //customer checkbox
    $('#customer').on('change', function(){
        $('#customer').val(this.checked ? 1 : 0);
        document.getElementById('customer').innerHTML = this.value.toString();
    });
    //receiver checkbox
    $('#receiver').on('change', function(){
        $('#receiver').val(this.checked ? 1 : 0);
        document.getElementById('receiver').innerHTML = this.value.toString();
    });
    //consignment checkbox
    $('#consignment').on('change', function(){
        $('#consignment').val(this.checked ? 1 : 0);
        document.getElementById('consignment').innerHTML = this.value.toString();
    });

//FINANCE MANAGEMENT

    //chart_accounts checkbox
    $('#chart_accounts').on('change', function(){
        $('#chart_accounts').val(this.checked ? 1 : 0);
        document.getElementById('chart_accounts').innerHTML = this.value.toString();
    });
    //salary_compensation checkbox
    $('#salary_compensation').on('change', function(){
        $('#salary_compensation').val(this.checked ? 1 : 0);
        document.getElementById('salary_compensation').innerHTML = this.value.toString();
    });
    //allowance_disbursement checkbox
    $('#allowance_disbursement').on('change', function(){
        $('#allowance_disbursement').val(this.checked ? 1 : 0);
        document.getElementById('allowance_disbursement').innerHTML = this.value.toString();
    });
    //allowance_disbursement checkbox
    $('#financial_liquidation').on('change', function(){
        $('#financial_liquidation').val(this.checked ? 1 : 0);
        document.getElementById('financial_liquidation').innerHTML = this.value.toString();
    });
    //loan checkbox
    $('#loan').on('change', function(){
        $('#loan').val(this.checked ? 1 : 0);
        document.getElementById('loan').innerHTML = this.value.toString();
    });
    //remittance checkbox
    $('#remittance').on('change', function(){
        $('#remittance').val(this.checked ? 1 : 0);
        document.getElementById('remittance').innerHTML = this.value.toString();
    });
    //remittance checkbox
    $('#expenses').on('change', function(){
        $('#expenses').val(this.checked ? 1 : 0);
        document.getElementById('expenses').innerHTML = this.value.toString();
    });

//EMPLOYEE MANAGEMENT

    //employee checkbox
    $('#employee').on('change', function(){
        $('#employee').val(this.checked ? 1 : 0);
        document.getElementById('employee').innerHTML = this.value.toString();
    });
    //user checkbox
    $('#user').on('change', function(){
        $('#user').val(this.checked ? 1 : 0);
        document.getElementById('user').innerHTML = this.value.toString();
    });
    //tickets checkbox
    $('#tickets').on('change', function(){
        $('#tickets').val(this.checked ? 1 : 0);
        document.getElementById('tickets').innerHTML = this.value.toString();
    });

//REPORTS MANAGEMENT

    //report_incident checkbox
    $('#report_incident').on('change', function(){
        $('#report_incident').val(this.checked ? 1 : 0);
        document.getElementById('report_incident').innerHTML = this.value.toString();
    });
    //report_cargo checkbox
    $('#report_cargo').on('change', function(){
        $('#report_cargo').val(this.checked ? 1 : 0);
        document.getElementById('report_cargo').innerHTML = this.value.toString();
    });
    //report_sales checkbox
    $('#report_sales').on('change', function(){
        $('#report_sales').val(this.checked ? 1 : 0);
        document.getElementById('report_sales').innerHTML = this.value.toString();
    });
    //report_box_purchase checkbox
    $('#report_box_purchase').on('change', function(){
        $('#report_box_purchase').val(this.checked ? 1 : 0);
        document.getElementById('report_box_purchase').innerHTML = this.value.toString();
    });
    //report_box_disposed checkbox
    $('#report_box_disposed').on('change', function(){
        $('#report_box_disposed').val(this.checked ? 1 : 0);
        document.getElementById('report_box_disposed').innerHTML = this.value.toString();
    });
    //report_box_disposed checkbox
    $('#report_branch_incentives').on('change', function(){
        $('#report_branch_incentives').val(this.checked ? 1 : 0);
        document.getElementById('report_branch_incentives').innerHTML = this.value.toString();
    });
    //report_exceptions checkbox
    $('#report_exceptions').on('change', function(){
        $('#report_exceptions').val(this.checked ? 1 : 0);
        document.getElementById('report_exceptions').innerHTML = this.value.toString();
    });
    //report_box_aging checkbox
    $('#report_box_aging').on('change', function(){
        $('#report_box_aging').val(this.checked ? 1 : 0);
        document.getElementById('report_box_aging').innerHTML = this.value.toString();
    });