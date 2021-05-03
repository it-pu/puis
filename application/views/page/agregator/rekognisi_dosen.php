<style>
    #dataTablesLuaran tr th,
    #dataTablesLuaran tr td {
        text-align: center;
    }
</style>

<div class="well">
    <div class="row">

        <div class="col-md-12">
            <p style="color:#3968c6;"><b> Rekognisi Dosen</b></p>
            <div style="text-align: right;margin-bottom: 20px;" class="inputBtn">
                <!-- <button class="btn btn-primary form-data-add" id="addRekognisiDosenMDL"><i class="fa fa-plus margin-right"></i> Rekognisi Dosen</button> -->
            </div>
            <div style="text-align: right;">
                <!--                <button onclick="saveTable2Excel('dataTable2Excel')" class="btn btn-success"><i class="fa fa-file-excel-o margin-right"></i> Excel</button>-->
                <button id="saveToExcel" class="btn btn-success"><i class="fa fa-file-excel-o margin-right"></i> Excel</button>
            </div>
            <div style="margin-top: 20px;" id="viewTable"></div>
        </div>
    </div>
</div>

<script>
    var oTable;
    var oSettings;

    $('#addRekognisiDosenMDL').click(function() {

        $('#GlobalModal .modal-header').html('<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
            '<h4 class="modal-title">Tambah Data Rekognisi Dosen</h4>');

        var body = '<div class="row">' +
            '    <div class="col-md-12">' +
            '        <div class="well">' +
            '           <input id="formID" class="hide">' +
            '          <div class="form-group">' +
            '                <label>Dosen</label> ' +
            '                <select class="full-width-fix" size="5" id="nip"><option></option></select>' +
            '            </div>' +
            '          <div class="form-group">' +
            '                <label>Bidang Keahlian </label> ' +
            '                <input class="form-control" id="bidang_keahlian" />' +
            '            </div>' +
            '          <div class="form-group">' +
            '                <label>Rekognisi </label> ' +
            '                <input class="form-control" id="rekognisi" />' +
            '            </div>' +
            '          <div class="form-group">' +
            '                <label>Bukti Pendukung </label> ' +
            '                <input class="form-control" id="BuktiPendukungName" />' +
            '            </div>' +
            '          <div class="form-group">' +
            '                <label>Bukti Pendukung Upload</label> ' +
            '                <input type="file" data-style="fileinput" class="input" name="BuktiPendukungUpload">' +
            '            </div>' +
            '<div class="form-group">' +
            ' <label>Tingkat</label>' +
            '<select class="form-control input" ID ="Tingkat">' +
            ' <option value="Wilayah">Wilayah</option>' +
            ' <option value="Nasional">Nasional</option>' +
            ' <option value="Internasional">Internasional</option>' +
            '</select>' +
            '</div>' +
            '            <div class="form-group">' +
            '                <label>Tahun </label> ' +
            '                 <input type="number"  class="form-control" id="tahun"/>' +
            '            </div>' +
            '        </div>' +
            '    </div>' +
            '    ' +
            '</div>';



        $('#GlobalModal .modal-body').html(body);

        loadSelectOptionLecturersSingle_NIDN('#nip', '');
        $('#nip').select2({
            allowClear: true
        });

        $('#GlobalModal .modal-footer').html('<button type="button" class="btn btn-default" data-dismiss="modal">Close</button> ' +
            '<button class="btn btn-success" style="text-align: right;" id="btnSaveRekognisi">Save</button>');

        $('#GlobalModal').modal({
            'show': true,
            'backdrop': 'static'
        });

        $('#btnSaveRekognisi').click(function() {
            // Form
            var form_data = new FormData();
            var formID = $('#formID').val();
            var nip = $('#nip').val();
            var bidang_keahlian = $('#bidang_keahlian').val();
            var rekognisi = $('#rekognisi').val();
            var tahun = $('#tahun').val();
            var BuktiPendukungName = $('#BuktiPendukungName').val();
            var Tingkat = $('#Tingkat option:selected').val();
            var S_file = $('.input[name="BuktiPendukungUpload"]');

            if (nip != '' && nip != null &&
                bidang_keahlian != '' && bidang_keahlian != null &&
                rekognisi != '' && rekognisi != null &&
                tahun != '' && tahun != null) {

                loading_buttonSm('#btnSaveRekognisi');

                var data = {
                    action: 'save_rekognisi_dosen',
                    ID: (formID != '' && formID != null) ? formID : '',
                    dataForm: {
                        NIP: nip,
                        Bidang_keahlian: bidang_keahlian,
                        Rekognisi: rekognisi,
                        Tahun: tahun,
                        BuktiPendukungName: BuktiPendukungName,
                        Tingkat: Tingkat,
                    }
                };

                var token = jwt_encode(data, 'UAP)(*');
                var url = base_url_js + 'api3/__crudAgregatorTB3';
                form_data.append('token', token);

                // for upload
                if (S_file.length) {
                    var UploadFile = S_file[0].files;
                    form_data.append("BuktiUpload[]", UploadFile[0]);
                }

                $.ajax({
                    type: "POST",
                    url: url,
                    data: form_data, // Data sent to server, a set of key/value pairs (i.e. form fields and values)
                    contentType: false, // The content type used when sending data to the server.
                    cache: false, // To unable request pages to be cached
                    processData: false,
                    dataType: "json",
                    success: function(result) {
                        if (result == 0 || result == '0') {
                            toastr.error('Maaf, Gagal simpan data !', 'Error');
                        } else {
                            loadRekognisiDosen();
                            $('#GlobalModal').modal('hide');
                            toastr.success('Data saved', 'Success');
                            $('#nama_dosen').val('');
                            $('#bidang_keahlian').val('');
                            $('#rekognisi').val('');
                            $('#tahun').val('');
                        }

                        setTimeout(function() {
                            $('#GlobalModal').modal('hide');
                        }, 500);
                    },
                    error: function(data) {
                        toastr.error("Connection Error, Please try again", 'Error!!');
                        // $(el).prop('disabled',false).html('Save');
                    }
                })

                // $.post(url,{token:token},function (result) {

                //     if(result==0 || result=='0'){
                //         toastr.error('Maaf, Gagal simpan data !','Error');
                //     }
                //     else {
                //         loadRekognisiDosen();
                //         $('#GlobalModal').modal('hide');
                //         toastr.success('Data saved','Success');
                //         $('#nama_dosen').val('');
                //         $('#bidang_keahlian').val('');
                //         $('#rekognisi').val('');
                //         $('#tahun').val('');
                //     }

                //     setTimeout(function () {
                //         $('#GlobalModal').modal('hide');
                //     },500);
                // });

            } else {
                toastr.error('All form required', 'Error');
            }
        });

    });


    $(document).ready(function() {

        window.act = "<?= $accessUser; ?>";

        if (parseInt(act) <= 0) {
            $('.form-data-add').remove();
        } else {}

        loadRekognisiDosen();
        newDescritionInput.getDescription();
    });

    function loadRekognisiDosen() {

        $('#viewTable').html(' <table class="table table-striped dataTable2Excel" data-name="Rekognisi_dosen" id="dataTablesLuaran">' +
            '    <thead>  ' +
            '     <tr>   ' +
            '        <th style="width: 1%;">No</th>  ' +
            '        <th style="">Nama Dosen</th>  ' +
            '        <th style="width: 20%;">Keahlian</th>  ' +
            '        <th style="width: 20%;">Rekognisi</th>  ' +
            '        <th style="width: 10%;">Tahun</th>  ' +
            '        <th style="width: 10%;">Tingkat</th>  ' +
            '        <th style="width: 10%;">Sertifikat</th>  ' +
            '        <th style="width: 10%;">Status Approval</th>  ' +
            '        <th class="noExl" style="width: 5%;"><i class="fa fa-cog"></i></th>  ' +
            '    </tr>  ' +
            '    </thead>  ' +
            '       <tbody id="listData"></tbody>   ' +
            '    </tfoot> ' +
            '    </table>');


        var data = {
            action: 'readDataRekognisiDosen'
        };

        var token = jwt_encode(data, 'UAP)(*');
        var url = base_url_js + 'api3/__crudAgregatorTB3';

        $.post(url, {
            token: token
        }, function(jsonResult) {

            if (jsonResult.length > 0) {

                for (var i = 0; i < jsonResult.length; i++) {
                    var v = jsonResult[i];

                    var btnAct = '<div class="btn-group inputBtn">' +
                        '  <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">' +
                        '    <i class="fa fa-edit"></i> <span class="caret"></span>' +
                        '  </button>' +
                        '  <ul class="dropdown-menu">' +
                        // '    <li><a href="javascript:void(0);" class="btnActEdit" data-id="'+v.ID+'" data-no="'+i+'">Edit</a></li>' +
                        // '    <li role="separator" class="divider"></li>' +
                        '    <li><a href="javascript:void(0);" class="btnActRemove" data-id="' + v.ID + '" data-no="' + i + '">Remove</a></li>' +
                        '  </ul>' +
                        '</div>';
                    var labelStatusApv = "";
                    if (v.isApproved == 1) {
                        labelStatusApv = "<span class='label label-info'>Wait approval</span>";
                    } else if (v.isApproved == 2) {
                        labelStatusApv = "<span class='label label-primary'>Approved</span>";
                    } else if (v.isApproved == 3) {
                        labelStatusApv = "<span class='label label-danger'>Rejected</span>";
                    } else {
                        labelStatusApv = "UNKNOW";
                    }
                    var labelApvBy = "";
                    if (v.isApproved == 2 && (v.approvedBy != "" || v.approvedBy)) {
                        labelApvBy = "<br><small>Approved by " + v.approvedBy + "</small>";
                    }
                    if (v.isApproved == 3) {
                        labelApvBy = "<br><small>Rejected by " + v.UpdatedBy + "</small>";
                    }
                    var vBtnAct = "";
                    if (v.approvedBy) {
                        var splitApproved = v.approvedBy.split("/");
                        console.log(splitApproved);
                        vBtnAct = ((splitApproved[0] == "<?= $this->session->userdata('NIP') ?>") ? btnAct : '')
                    }

                    var BuktiPendukungUpload = (v.BuktiPendukungUpload != '' && v.BuktiPendukungUpload != null) ? '<a class="btn btn-xs btn-primary" target="_blank" href="' + base_url_js + 'uploads/Agregator/Aps/' + v.BuktiPendukungUpload + '">View PDF</a>' : '-';
                    $('#listData').append('<tr>' +
                        '   <td style="border-right: 1px solid #ccc;">' + (i + 1) + '</td>' +
                        '   <td style="text-align: left;">' + v.Name + '</td>' +
                        '   <td style="text-align: left;">' + v.Bidang_keahlian + '</td>' +
                        '   <td style="text-align: left;">' + v.Rekognisi + '</td>' +
                        '   <td>' + v.Tahun + '</td>' +
                        '   <td style="text-align: left;">' + v.Tingkat + '</td>' +
                        '<td>' + BuktiPendukungUpload + '</td>' +
                        '   <td>' + labelStatusApv + labelApvBy + '</td>' +
                        '   <td class="noExl" style="text-align: left;border-left: 1px solid #ccc;">' + vBtnAct + '</td>' +
                        '</tr>');

                    // var total = parseInt(jsonResult.length);
                    //total = total + parseInt(v2.dataEmployees.length);
                }

            }


            oTable = $('#dataTablesLuaran').DataTable();
            oSettings = oTable.settings();


        });

    }

    $('#saveToExcel').click(function() {

        $('select[name="dataTablesLuaran_length"]').val(-1);

        oSettings[0]._iDisplayLength = oSettings[0].fnRecordsTotal();
        oTable.draw();

        setTimeout(function() {
            saveTable2Excel('dataTable2Excel');
        }, 1000);
    });

    $(document).on('click', '.btnActRemove', function() {
        if (confirm('Are you sure?')) {
            var ID = $(this).attr('data-id');

            var data = {
                action: 'removeDataRekognisiDosen',
                ID: ID
            };

            var token = jwt_encode(data, 'UAP)(*');
            var url = base_url_js + 'api3/__crudAgregatorTB3';
            $.post(url, {
                token: token
            }, function(result) {
                toastr.success('Data removed', 'Success');
                loadRekognisiDosen();
            });

        }
    });

    $(document).on('click', '.btnSaveDescription', function(e) {
        const itsme = $(this);
        newDescritionInput.saveDescription(itsme);
    })
</script>