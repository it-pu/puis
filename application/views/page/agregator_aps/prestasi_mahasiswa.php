<h3 align="center">Prestasi Mahasiswa</h3><br/>Prodi : <span id="viewProdiID"></span> | <span id="viewProdiName"></span>
<style>
    #dataTablesPAM tr th, #dataTablesPAM tr td {
        text-align: center;
    }
</style>

<div class="well">

    <div class="row">

        <div class="col-md-12">

            <div style="text-align: right;margin-bottom: 30px;">
                <button id="saveToExcel" class="btn btn-success"><i class="fa fa-file-excel-o margin-right"></i> Excel</button>
            </div>
            <div id="viewTable"></div>
            <p style="color: orangered;">*) Table prestasi mahasiswa mencakup laporan APS table 5 & 5a</p>

        </div>

    </div>

</div>                    
                    
    <script>
        var oTable;
        var oSettings;

        
        $('#saveToExcel').click(function () {

            $('select[name="dataTablesPAM_length"]').val(-1);

            oSettings[0]._iDisplayLength = oSettings[0].fnRecordsTotal();
            oTable.draw();

            setTimeout(function () {
                saveTable2Excel('dataTable2Excel');
            },1000);
        });

$(document).ready(function () {
    var firstLoad = setInterval(function () {
        var filterProdi = $('#filterProdi').val();
        if(filterProdi!='' && filterProdi!=null){
            loadPage();
            clearInterval(firstLoad);
        }
    },1000);
    setTimeout(function () {
        clearInterval(firstLoad);
    },5000);

});
$('#filterProdi').change(function () {
    var filterProdi = $('#filterProdi').val();
    if(filterProdi!='' && filterProdi!=null){
        loadPage();
    }
});
function loadPage() {
    var filterProdi = $('#filterProdi').val();
    if(filterProdi!='' && filterProdi!=null){
        $('#viewProdiID').html(filterProdi);
        $('#viewProdiName').html($('#filterProdi option:selected').text());
    $('#viewTable').html(' <table class="table dataTable2Excel" data-name="Prestasi-Akademik-Mahasiswa" id="dataTablesPAM">' +
        '                <thead>' +
        '                <tr>' +
        '                    <th rowspan="2" style="width: 1%;">No</th>' +
        '                    <th rowspan="2">Kegiatan</th>' +
        '                    <th rowspan="2" style="width: 10%;">Kategori</th>' +
        '                    <th rowspan="2" style="width: 15%;">Waktu</th>' +
        '                    <th colspan="3" style="width: 25%;">Tingkat</th>' +
        '                    <th rowspan="2" style="width: 20%;">Prestasi</th>' +
        '                </tr>' +
        '                <tr>' +
        '                   <th>Provinsi / Wilayah</th>' +
        '                   <th>Nasional</th>' +
        '                   <th>Internasional</th>' +
        '               </tr>' +
        '                </thead>' +
        '                <tbody id="listData"></tbody>' +
        '            </table>');

    var P = filterProdi.split('.');
    var ProdiID = P[0];
    var data = {
        action : 'viewDataPAM_APS',
        ProdiID : ProdiID,
        // Type : '0'
    };

    var token = jwt_encode(data,'UAP)(*');
    var url = base_url_js+'api3/__crudAgregatorTB5';
    
        $.post(url,{token:token},function (jsonResult) {

            if(jsonResult.length>0){
                $.each(jsonResult,function (i,v) {
                    var DataStudent = v.DataStudent;
                    var DataStudentToken =  jwt_encode(DataStudent,'UAP)(*');
                    var EventWr = '<a href = "javascript:void(0);" class = "datadetail" data = "'+DataStudentToken+'">'+v.Event+'</a>'

                    var Provinsi = (v.Level=='Provinsi') ? 'v' : '';
                    var Nasional = (v.Level=='Nasional') ? 'v' : '';
                    var Internasional = (v.Level=='Internasional') ? 'v' : '';
                    var lbl = (v.Type=='1' || v.Type==1)
                        ? '<span class="label label-success">Academic</span>'
                        : '<span class="label label-default">Non Academic</span>';

                    $('#listData').append('<tr>' +
                        '<td style="border-right: 1px solid #ccc;">'+(i+1)+'</td>' +
                        '<td style="text-align: left;">'+EventWr+'</td>' +
                        '<td style="text-align: center;">'+lbl+'</td>' +
                        '<td>'+moment(v.StartDate).format('DD-MM-YYYY')+'</td>' +
                        '<td>'+Provinsi+'</td>' +
                        '<td>'+Nasional+'</td>' +
                        '<td>'+Internasional+'</td>' +
                        '<td>'+v.Achievement+'</td>' +
                        '</tr>');

                });
            }


            oTable = $('#dataTablesPAM').DataTable();
            oSettings = oTable.settings();


        });

        newDescritionInput.getDescription();
    }
}

$(document).off('click', '.datadetail').on('click', '.datadetail',function(e) {
    var dt = $(this).attr('data');
    dt = jwt_decode(dt);
    // console.log(dt);
    var html =  '<div class = "row">'+
                    '<div class = "col-md-12">'+
                        '<table class = "table">'+
                            '<thead>'+
                                '<tr>'+
                                    '<td>No</td>'+
                                    '<td>NPM</td>'+
                                    '<td>Name</td>'+
                                '</tr>'+
                            '</thead>'+
                            '<tbody>';
            if (dt.length > 0) {
                for (var i = 0; i < dt.length; i++) {
                    html += '<tr>'+
                                '<td>'+ (parseInt(i)+1) + '</td>'+
                                '<td>'+ dt[i].NPM + '</td>'+
                                '<td>'+ dt[i].Name+'</td>'+
                            '</tr>';
                }
            }
            else
            {
                html += '<tr>'+
                            '<td colspan="3"><label>No Data Detail</label></td>'+
                        '</tr>';
            }


            html  += '</tbody></table></div></div>';

    $('#GlobalModal .modal-header').html('<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
        '<h4 class="modal-title">Detail</h4>');
    $('#GlobalModal .modal-body').html(html);
    $('#GlobalModal .modal-footer').html('<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>');
    $('#GlobalModal').modal({
        'show' : true,
        'backdrop' : 'static'
    });
})

$(document).on('click','.btnSaveDescription',function(e){
    const itsme =  $(this);
    newDescritionInput.saveDescription(itsme);
})
</script>