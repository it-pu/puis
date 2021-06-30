<div class="row">
    <div class="col-md-4 col-md-offset-4">
        <div class="well">
            <select class="form-control" id="filterStatus">
                <option value="">-- All Status --</option>
                <option value="1">Request / Waiting Action</option>
                <option value="2">Approved</option>
                <option value="-1">Rejected</option>
                <option value="-2">Canceled</option>
            </select>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div id="divLoadTable"></div>
    </div>
</div>

<script>
    $(document).ready(function() {
        loadDataBooking();
    });

    $('#filterStatus').change(function() {
        loadDataBooking();
    });

    function loadDataBooking() {

        $('#divLoadTable').html('<table id="tableFileFP" class="table table-striped table-centre">' +
            '            <thead>' +
            '            <tr>' +
            '                <th style="width: 1%;">No</th>' +
            '                <th style="width: 20%;">User</th>' +
            '                <th>Title</th>' +
            '                <th style="width: 25%;">Note By Admin</th>' +
            '                <th style="width: 10%;">Status</th>' +
            '                <th style="width: 20%;">Action</th>' +
            '            </tr>' +
            '            </thead>' +
            '        </table>');

        var filterStatus = $('#filterStatus').val();

        var data = {
            action: 'viewListBooking',
            Status: (filterStatus != '' && filterStatus != null) ? filterStatus : ''
        };

        var token = jwt_encode(data, 'UAP)(*');

        window.dataTable = $('#tableFileFP').DataTable({
            "processing": true,
            "serverSide": true,
            "iDisplayLength": 10,
            "ordering": false,
            "language": {
                "searchPlaceholder": "Search by NIM / NIP"
            },
            "ajax": {
                url: base_url_js + "api3/__crudLibraryBooking", // json datasource
                data: {
                    token: token
                },
                ordering: false,
                type: "post", // method  , by default get
                error: function() { // error handling
                    $(".employee-grid-error").html("");
                    $("#employee-grid").append('<tbody class="employee-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
                    $("#employee-grid_processing").css("display", "none");
                }
            }
        });

    }

    $(document).on('click', '.actionBooking', function() {

        $('#GlobalModal .modal-header').html('<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
            '<h4 class="modal-title">Action Panel</h4>');

        var id = $(this).attr('data-id');
        var biblio_id = $(this).attr('data-biblio');
        var status = $(this).attr('data-status');

        var labelAct = (parseInt(status) > 0) ? '<span class="label label-success">Accespted</span>' : '<span class="label label-danger">Rejected</span>';

        var htmlss = '<input id="formBooking_ID" class="hide" value="' + id + '" />' +
            '<input id="formBooking_status" class="hide" value="' + status + '" />' + labelAct +
            '<textarea class="form-control" id="notes_by_admin" rows="4" placeholder="Enter the reason here . . ."></textarea>';

        $('#GlobalModal .modal-footer').html('<button type="button" class="btn btn-success" onClick="updateBooking(' + id + ',' + biblio_id + ' ,' + status + ')">Submit</button><button type="button" class="btn btn-default" data-dismiss="modal">Close</button>');

        $('#GlobalModal .modal-body').html(htmlss);

        $('#GlobalModal').modal({
            'show': true,
            'backdrop': 'static'
        });

    });

    function updateBooking(id_booking, biblio_id, Status) {

        // console.log(id_booking, Status);
        // return false;

        if (confirm('Are you sure?')) {

            var notes_by_admin = $('#notes_by_admin').val();

            var form = new FormData();
            form.append("id_booking", id_booking);
            form.append("biblio_id", biblio_id);
            //biblio_id
            form.append("notes_by_admin", notes_by_admin);
            form.append("status", Status);

            var settings = {
                "async": true,
                "crossDomain": true,
                "url": url_rest_server + 'library/updateBooking',
                "method": "POST",
                "processData": false,
                "contentType": false,
                "mimeType": "multipart/form-data",
                "data": form
            }

            $.ajax(settings).done(function(response) {

                response = JSON.parse(response);

                if (response.status) {
                    window.dataTable.ajax.reload(null, false);
                    toastr.success('Data saved', 'Success');

                    $('#GlobalModal').modal('hide');
                } else {
                    toastr.warning('Failed to submit, please try again a few minutes', 'Warning');
                }




            });

        }

    }
</script>