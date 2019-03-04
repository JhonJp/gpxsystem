$(document).ready(function () {

    $('#boxtype').change(function () {
        var id = $('#boxtype').val();
        $.ajax({
            type: 'GET',
            url: 'api/boxtype/get_byid.php?id='+id,
            success: function (data) {
                $('#depositprice').val(data);        
            }
        });
    });

    $("#btnaddreserve").click(function (e) {
        e.preventDefault();

        if ($('#account_no').val() != null && $('#assigned_to').val() != null && $('#boxtype').val() != null) {
            var table = $("#reservetable");
            $('#reservetable tr:last').after(`
                <tr>
                    <td id="boxtype_id"><b>` + $('#boxtype option:selected').val() + `</b></td>
                    <td id="boxtype"><b>` + $('#boxtype option:selected').text() + `</b></td>
                    <td id="qty">` + $('#quantity').val() + `</td>
                    <td id="deposit">` + ($('#quantity').val() * $('#depositprice').val()) + `</td>
                    <td id="id" style="display:none"></td>
                </tr>
            `);

            var grandtotal = 0;

            table.find('tr').each(function (i, el) {
                var $tds = $(this).find('td');
                var total = $tds.eq(3).text();
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
            }, 500);

        }
    });

    $('#btnSave').click(function () {

        var reservationid = $('#reservationid').val();

        var data = {
            "data": [{
                reservationid: $('#reservationid').val(),
                account_no: $('#account_no').val(),
                grandtotal: $('#total').text(),
                assigned_to: $('#assigned_to').val(),
                status: $('#status').val()
            }]
        }

        var reservationListArr = [];
        $('#reservetable tr').each(function (index, value) {
            reservationListArr.push({
                boxtype_id: $('#boxtype_id', value).text(),
                boxtype: $('#boxtype', value).text(),
                qty: $('#qty', value).text(),
                deposit: $('#deposit', value).text()
            });
        });

        $.ajax({
            type: 'POST',
            data: {
                list: JSON.stringify(reservationListArr),
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
                }, 500);

            }
        });

    });

    $('#btnUpdate').click(function () {

        var reservationid = $('#reservationid').val();

        var data = {
            "data": [{
                reservationid: $('#reservationid').val(),
                account_no: $('#account_no').val(),
                grandtotal: $('#total').text(),
                assigned_to: $('#assigned_to').val(),
                status: $('#status').val()
            }]
        }

        var reservationListArr = [];
        $('#reservetable tr').each(function (index, value) {
            reservationListArr.push({
                boxtype_id: $('#boxtype_id', value).text(),
                boxtype: $('#boxtype', value).text(),
                qty: $('#qty', value).text(),
                deposit: $('#deposit', value).text(),
                id: $('#id', value).text()
            });
        });

        $.ajax({
            type: 'POST',
            data: {
                list: JSON.stringify(reservationListArr),
                data: JSON.stringify(data)
            },
            url: 'index.php?controller=reservation&action=update',
            success: function (data) {
                console.log(data);
                document.getElementById("notification").style.display = "block";
                $("#header").html(`<i class="icon fa fa-check"></i> Save`);
                $("#message").html("Your created data was successfully saved!");
                $("#notificationtype").addClass("alert-success");
                setInterval(function () {
                    window.location.href = 'index.php?controller=reservation&action=list';
                }, 500);

            }
        });

    });

});