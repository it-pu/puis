<style>
    #dataTable tr th,
    #dataTable tr td,
    #dataTable_2 tr th,
    #dataTable_2 tr td {
        text-align: center;
    }

    #tableLect tr th,
    #tableLect tr td,
    #tableLect_2 tr th,
    #tableLect_2 tr td {
        text-align: center;
    }

    .tdJml {
        background: lightyellow;
    }
</style>


<div class="well">
    <div class="row">

        <div class="col-md-12">


            <div style="text-align: right;">
                <button onclick="saveTable2Excel('dataTable2Excel')" class="btn btn-success"><i class="fa fa-file-excel-o margin-right"></i> Excel</button>
            </div>
            <table class="table dataTable2Excel" id="dataTable" data-name="kecukupan_dosen">
                <thead>
                    <tr>
                        <th rowspan="2" style="vertical-align : middle;text-align:center;width: 1%;">No</th>
                        <th rowspan="2" style="vertical-align : middle;text-align:center;">Program Studi</th>
                        <th colspan="3">Pendidikan Tertinggi</th>
                        <th rowspan="2" style="vertical-align : middle;text-align:center;width: 10%;">Jumlah</th>
                    </tr>
                    <tr>
                        <th style="width: 10%;">Doktor</th>
                        <th style="width: 10%;">Magister</th>
                        <th style="width: 10%;">Profesi</th>
                    </tr>
                </thead>
                <tbody id="listTable"></tbody>
            </table>
        </div>

    </div>
</div>

<div class="well">
    <div class="row">

        <div class="col-md-12">


            <h3 style="font-weight: bold;"><i class="fa fa-hashtag" style="color: #ff9800;"></i> Dengan Sub Prodi</h3>

            <button onclick="saveTable2Excel('dataTable2Excel_2')" class="btn btn-success pull-right"><i class="fa fa-file-excel-o margin-right"></i> Excel</button>

            <table class="table table-centre dataTable2Excel_2" id="dataTable_2" data-name="kecukupan_dosen_dengan_sub_prodi">
                <thead>
                    <tr>
                        <th rowspan="2" style="vertical-align : middle;text-align:center;width: 1%;">No</th>
                        <th rowspan="2" style="vertical-align : middle;text-align:center;">Program Studi</th>
                        <th colspan="3">Pendidikan Tertinggi</th>
                        <th rowspan="2" style="vertical-align : middle;text-align:center;width: 10%;">Jumlah</th>
                    </tr>
                    <tr>
                        <th style="width: 10%;">Doktor</th>
                        <th style="width: 10%;">Magister</th>
                        <th style="width: 10%;">Profesi</th>
                    </tr>
                </thead>
                <tbody id="listTable_2"></tbody>
            </table>
        </div>

    </div>
</div>



<script>
    $(document).ready(function() {
        loadKecukupanDosen();
        newDescritionInput.getDescription();
    });

    $("#btndownloaadExcel").click(function() {

        var akred = "0";
        var url = base_url_js + 'agregator/excel-kecukupan-dosen';
        data = {
            akred: 0
        }
        var token = jwt_encode(data, "UAP)(*");
        FormSubmitAuto(url, 'POST', [{
            name: 'token',
            value: token
        }, ]);
    })

    function loadKecukupanDosen() {
        var url = base_url_js + 'api3/__getKecukupanDosen';

        $.getJSON(url, function(jsonResult) {

            $('#listTable').empty();
            if (jsonResult.length > 0) {
                var p = 0;
                var m = 0;
                var d = 0;
                var j = 0;

                var p_2 = 0;
                var m_2 = 0;
                var d_2 = 0;
                var j_2 = 0;
                $.each(jsonResult, function(i, v) {

                    var edu = '';
                    var totalLec = 0;
                    $.each(v.dataLecturers, function(i2, v2) {
                        var det = v2.Details.length;
                        totalLec = totalLec + det;

                        var toModal = {
                            Prodi: v.Name,
                            Level: v2.Level,
                            Details: v2.Details
                        };
                        var tokenLect = jwt_encode(toModal, 'UAP)(*');
                        var viewLecSum = (det > 0) ? '<a href="javascript:void(0);" class="showDetailLect" data-lec="' + tokenLect + '">' + det + '</a>' : det;
                        edu = edu + '<td>' + viewLecSum + '</td>';
                        //console.log(edu);

                        if (i2 == 2) {
                            p = p + det
                        }

                        if (i2 == 1) {
                            m = m + det
                        }

                        if (i2 == 0) {
                            d = d + det
                        }

                    });

                    edu = edu + '<th class="tdJml" style="border-left: 1px solid #CCCCCC;">' + totalLec + '</th>';
                    j = j + totalLec;

                    $('#listTable').append('<tr>' +
                        '<td style="border-right: 1px solid #CCCCCC;">' + (i + 1) + '</td>' +
                        '<td style="text-align: left;">' + v.Code + ' - ' + v.Name + '</td>' + edu + ' ' +
                        '</tr>');



                    // dengan sub prodi
                    var edu_2 = '';
                    var totalLec_2 = 0;

                    $.each(v.dataLecturersSub, function(i3, v3) {

                        var det = v3.Details.length;
                        totalLec_2 = totalLec_2 + det;

                        var toModal = {
                            Prodi: v.Name,
                            Level: v3.Level,
                            Details: v3.Details
                        };
                        var tokenLect = jwt_encode(toModal, 'UAP)(*');
                        var viewLecSum = (det > 0) ? '<a href="javascript:void(0);" class="showDetailLect" data-lec="' + tokenLect + '">' + det + '</a>' : det;
                        edu_2 = edu_2 + '<td>' + viewLecSum + '</td>';

                        if (i3 == 2) {
                            p_2 = p_2 + det
                        }

                        if (i3 == 1) {
                            m_2 = m_2 + det
                        }

                        if (i3 == 0) {
                            d_2 = d_2 + det
                        }

                    });

                    edu_2 = edu_2 + '<th class="tdJml" style="border-left: 1px solid #CCCCCC;">' + totalLec_2 + '</th>';
                    j_2 = j_2 + totalLec_2;

                    $('#listTable_2').append('<tr>' +
                        '<td style="border-right: 1px solid #CCCCCC;">' + (i + 1) + '</td>' +
                        '<td style="text-align: left;">' + v.Code + ' - ' + v.Name + '</td>' + edu_2 + ' ' +
                        '</tr>');

                });

                $('#listTable').append('<tr>' +
                    '<th colspan="2" class="tdJml">Jumlah</th>' +
                    '<th class="tdJml">' + d + '</th>' +
                    '<th class="tdJml">' + m + '</th>' +
                    '<th class="tdJml">' + p + '</th>' +
                    '<th class="tdJml">' + j + '</th>' +
                    '</tr>');


                $('#listTable_2').append('<tr>' +
                    '<th colspan="2" class="tdJml">Jumlah</th>' +
                    '<th class="tdJml">' + d_2 + '</th>' +
                    '<th class="tdJml">' + m_2 + '</th>' +
                    '<th class="tdJml">' + p_2 + '</th>' +
                    '<th class="tdJml">' + j_2 + '</th>' +
                    '</tr>');
            }

        });
    }

    $(document).on('click', '.showDetailLect', function() {
        var tokenLect = $(this).attr('data-lec');
        var d = jwt_decode(tokenLect, 'UAP)(*');

        var tr = '';
        if (d.Details.length > 0) {
            $.each(d.Details, function(i, v) {

                var NID = (v.NIDN != '' && v.NIDN != null && v.NIDN != 0 && v.NIDN != '0') ? v.NIDN : '-';
                NID = (v.NIDK != '' && v.NIDK != null && v.NIDK != 0 && v.NIDK != '0') ? v.NIDK : NID;

                tr = tr + '<tr>' +
                    '<td style="border-right: 1px solid #ccc;">' + (i + 1) + '</td>' +
                    '<td>' + NID + '</td>' +
                    '<td style="text-align: left;">' + v.Name + '</td>' +
                    '</tr>';
            });
        }


        $('#GlobalModal .modal-header').html('<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
            '<h4 class="modal-title">' + d.Level + ' - ' + d.Prodi + '</h4>');
        $('#GlobalModal .modal-body').html('<div class="row">' +
            '    <div class="col-md-12">' +
            '        <table class="table table-striped" id="tableLect" style="margin-bottom: 0px;">' +
            '            <thead>' +
            '            <tr>' +
            '                <th style="width: 5%;">No</th>' +
            '                <th style="width: 25%;">NIDN / NIDK</th>' +
            '                <th>Name</th>' +
            '            </tr>' +
            '            </thead>' +
            '            <tbody>' + tr + '</tbody>' +
            '        </table>' +
            '    </div>' +
            '</div>');
        $('#GlobalModal .modal-footer').html('<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>');
        $('#GlobalModal').modal({
            'show': true,
            'backdrop': 'static'
        });

    });

    $(document).on('click', '.btnSaveDescription', function(e) {
        const itsme = $(this);
        newDescritionInput.saveDescription(itsme);
    })
</script>