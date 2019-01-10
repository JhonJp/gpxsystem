$(document).ready(function () {


    $('#reservation_no').change(function () {
        var reservation_no = $('#reservation_no').val();
        $.ajax({
            type: 'GET',
            url: 'api/booking/getcustomer.php?reservation_no='+reservation_no,
            success: function (data) {
                console.log(data[0][0]);
                $("#customer").val(data[0][0]);
                /*
                var select = document.getElementById("customer");
                var length = select.options.length;
                for (i = 0; i < length; i++) {
                    select.options[i] = null;
                }
                $.each(data, function(i, item) {
                    $("#customer").append('<option value="'+item[0]+'">'+item[1]+'</option>');
                });
                */
            }
        });
    });
});