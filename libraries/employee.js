$(document).ready(function () {

    $("#saveemployeeinfo").submit(function (e) {
        e.preventDefault();

        var data = $('#saveemployeeinfo').serialize();
        var employeeid = $('#employeeid').val();
        
        $.ajax({
            type: 'POST',
            dataType: 'text',
            data: data,
            url: 'index.php?controller=employee&action=save',
            success: function (data) {
                //console.log(data);

                document.getElementById("notification").style.display = "block";
                $("#header").html(`<i class="icon fa fa-check"></i> Save`);
                $("#message").html("Your created data was successfully saved!");
                $("#notificationtype").addClass("alert-success");

                setInterval(function () {
                    window.location.href = 'index.php?controller=employee&action=list';
                }, 2000);
            },
            error: function (error) {
               console.log("ERROR");
            }
        });

    });

    $('#btnAddCompany').click(function(e) {
        e.preventDefault();
        $('#work_experience tr:last').after(`
            <tr>
                <td id="company">` + $('#company').val() + `</td>
                <td id="position">` + $('#position').val() + `</td>
                <td id="from">` + $('#from').val() + `</td>
                <td id="to">` + $('#to').val() + `</td>                
                <td></td>                
            </tr>`);

        $('#modal-add').modal('hide');

        $('#company').val("");
        $('#position').val("");
        $('#from').val("");
        $('#to').val("");
        
    });
});