$(document).ready(function () {

    
    $('#boxtype').change(function () {
        $('#depositprice').val($('#boxtype').val());
    });

    $("#btnaddreserve").click(function (e) {
        e.preventDefault();

        if ($('#shipper').val() != null && $('#salesdriver').val() != null && $('#boxtype').val() != null) {
            //ADD 
            $('#reservetable tr:last').after(`
            <tr>
                <td id="boxtype">` + $('#boxtype option:selected').text() + `</td>
                <td id="qty">` + $('#quantity').val() + `</td>
                <td id="deposit">` + ($('#quantity').val() * $('#depositprice').val()) + `</td>
                <td></td>
            </tr>
            `);

            var grandtotal = 0;
            var table = $("#reservetable");
            table.find('tr').each(function (i, el) {
                var $tds = $(this).find('td');
                var total = $tds.eq(2).text();
                grandtotal += Number(total);
            });
            $('#total').html(grandtotal);

        } else {

            $("#header").html(`<i class="icon fa fa-warning"></i> Warning`);
            $("#message").html(`Please select mandatory fields!`);
            $("#notificationtype").addClass("alert-warning");
            document.getElementById("notification").style.display = "block";
            setInterval(function () {
                document.getElementById("notification").style.display = "none";
            }, 3000);

        }
    });

    $('#btnSave').click(function () {
       
        var account_no = $('#shipper').val();
        var grandtotal = $('#total').text();
        var employeeid = $('#salesdriver').val();

        var data = {
            "data": [{
                account_no: account_no,
                grandtotal: grandtotal,
                employeeid: employeeid
            }]
        }

        var jsonObj = [];
        
        $('#reservetable tr').each(function (index, value) {
            var boxtype = $('#boxtype', value).text();
            var qty = $('#qty', value).text();
            var deposit = $('#deposit', value).text();
            jsonObj.push({ 
                boxtype: boxtype,
                qty: qty,
                deposit: deposit
            });           
        });

        $.ajax({
            type: 'POST',
            data: {
                list: JSON.stringify(jsonObj),
                data: JSON.stringify(data)
            },
            url: 'index.php?controller=reservation&action=save',
            success: function (data) {
                console.log(data);  
                document.getElementById("notification").style.display = "block";
                $("#header").html(`<i class="icon fa fa-check"></i> Save`);
                $("#message").html("Your created data was successfully saved!");
                $("#notificationtype").addClass("alert-success");
                setInterval(function () {
                    window.location.href = 'index.php?controller=reservation&action=list';
                }, 3000);        
            },
            error: function (request) {
                $("#header").html(`<i class="icon fa fa-danger"></i> Error`);
                $("#message").html(request);
                $("#notificationtype").addClass("alert-danger");
                document.getElementById("notification").style.display = "block";
                setInterval(function () {
                    document.getElementById("notification").style.display = "none";
                }, 3000);
            }
        });
        
    });


});