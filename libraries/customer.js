$(document).ready(function () {

    $("#savecustomer").submit(function (e) {
        e.preventDefault();

        var id = $('#id').val();
        var customertype = $('#type').val();

        if(customertype == 'receiver'){
        //RECEIVER
        var data = $('#savecustomer').serializeArray();     
        var jsonObj = [];        
        $('#sub_receiver tr').each(function (index, value) {
            var fname = $('#fname', value).text();
            var lname = $('#lname', value).text();
            var relation = $('#relation', value).text();
            var address = $('#address', value).text();
            jsonObj.push({ 
                fname: fname,
                lname: lname,
                relation: relation,
                address: address
            });           
        });

        $.ajax({
            type: 'POST',
            data: {
                data : JSON.stringify(data),
                list : JSON.stringify(jsonObj)
            },
            url: 'index.php?controller=customer&action=save',
            success: function (data) {
                console.log(data);
                
                document.getElementById("notification").style.display = "block";
                $("#header").html(`<i class="icon fa fa-check"></i> Save`);
                $("#message").html("Your created data was successfully saved!");
                $("#notificationtype").addClass("alert-success");

                setInterval(function () {
                    window.location.href = 'index.php?controller='+ customertype +'&action=list';
                }, 2000);
                
            },
            error: function (error) {
               console.log("ERROR");
            }
        });
        }
        else{
            //CUSTOMER
            var data = $('#savecustomer').serialize(); 
            $.ajax({
                type: 'POST',
                dataType: 'text',
                data: data,
                url: 'index.php?controller=customer&action=save&id=' + id + '&type=' + customertype,
                success: function (data) {
                   
                    document.getElementById("notification").style.display = "block";
                    $("#header").html(`<i class="icon fa fa-check"></i> Save`);
                    $("#message").html("Your created data was successfully saved!");
                    $("#notificationtype").addClass("alert-success");
    
                    setInterval(function () {
                        window.location.href = 'index.php?controller='+ customertype +'&action=list';
                    }, 2000);
                },
                error: function (error) {
                   console.log("ERROR");
                }
            });
            
        }
        
    });

    $('#btnSubReceiverAdd').click(function(e) {
        e.preventDefault();


        $('#sub_receiver tr:last').after(`
            <tr>
                <td id="fname">` + $('#sub_firstname').val() + `</td>
                <td id="lname">` + $('#sub_lastname').val() + `</td>
                <td id="relation">` + $('#sub_relation').val() + `</td>
                <td id="address">` + $('#sub_address').val() + `</td>                
            </tr>`);

        $('#modal-add').modal('hide');

        $('#sub_firstname').val("");
        $('#sub_lastname').val("");
        $('#sub_relation').val("");
        $('#sub_address').val("");
        
        
    });

    $('#province').change(function () {
        var provCode = $('#province').val();
        $.ajax({
            type: 'GET',
            url: 'api/receiver/getcity.php?provCode='+provCode,
            success: function (data) {
                var select = document.getElementById("city");
                var length = select.options.length;
                for (i = 0; i < length; i++) {
                    select.options[i] = null;
                }
                $.each(data, function(i, item) {
                    $("#city").append('<option value="'+item[0]+'">'+item[1]+'</option>');
                });
            }
        });
    });

    $('#city').change(function () {
        var citymunCode = $('#city').val();
        console.log(citymunCode);
        $.ajax({
            type: 'GET',
            url: 'api/receiver/getbarangay.php?citymunCode='+citymunCode,
            success: function (data) {
                console.log(data);
                var select = document.getElementById("barangay");
                var length = select.options.length;
                for (i = 0; i < length; i++) {
                    select.options[i] = null;
                }
                $.each(data, function(i, item) {
                    $("#barangay").append('<option value="'+item[0]+'">'+item[1]+'</option>');
                });
            }
        });
    });

});