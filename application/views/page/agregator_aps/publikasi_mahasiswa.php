Prodi : <span id="viewProdiID"></span> | <span id="viewProdiName"></span>
<div class="well">
    <div class="row">

        <div class="col-md-12">
            <div style="text-align: right;margin-bottom: 30px;">
                <button onclick="saveTable2Excel('dataTable2Excel')" class="btn btn-success"><i class="fa fa-file-excel-o margin-right"></i> Excel</button>
            </div>
            <div id="viewTable" class="table-responsive">
                <table class="table dataTable2Excel" data-name="Publikasi-ilmiah-dtps" id="dataTablesPID">
                    <thead>
                        <tr>
                            <th rowspan="2">No</th>
                            <th rowspan="2">Jenis Publikasi</th>
                            <th colspan="5" style="text-align: center;">Jumlah Judul</th>
                            <th rowspan="2">Jumlah</th>
                        </tr>
                        <tr id = "JS_TS">
                            
                        </tr>
                    </thead>
                    <tbody></tbody>
                    <tfoot></tfoot>
                </table>
            </div>
            
        </div>

    </div>
</div>                    

<script>
var App_publikasi_mhs = {
    LoadAjaxTable : function(){
        // fill tahun
        var arr_ts =  App_publikasi_mhs.FillTS();
        var selector = $('#dataTablesPID tbody');
        var url = base_url_js+"rest3/__get_APS_CrudAgregatorTB8";
        var ProdiID = $('#filterProdi option:selected').val();
        var data = {
               mode : 'Publikasi_ilmiah_mhs',
               auth : 's3Cr3T-G4N',
               ProdiID : ProdiID,
               arr_ts : arr_ts,
        };

        var token = jwt_encode(data,'UAP)(*');
        $.post(url,{ token:token },function (resultJson) {
            selector.empty();
            var html = '';
            var arr_tot_row = [];
            for (var i = 0; i < arr_ts.length; i++) {
                arr_tot_row.push(0);
            }
            arr_tot_row.push(0); // for total
            // console.log(arr_tot_row);
            for (var i = 0; i < resultJson.length; i++) {
                var r = resultJson[i];
                html += '<tr>';
                for (var j = 0; j < r.length; j++) {
                    // console.log(r);
                    if (j <= 1) {
                        html += '<td>'+r[j]+'</td>';
                    }
                    else if(j > 1 && j <=(r.length) - 2 )
                    {
                        // console.log(r[j]);
                    html += '<td>'+'<a href = "javascript:void(0);" class = "datadetailpublikasi" data = "'+r[j].token+'">'+r[j].total+'</a>'+'</td>';
                    }
                    else
                    {
                        html += '<td>'+r[j]+'</td>';
                    }
                   
                   // startIndex = 2 untill endIndex
                   if (j >= 2 && j <=(r.length) - 1 ) {
                    var ind = j - 2;
                    if (j==(r.length) - 1) {
                       arr_tot_row[ind] = arr_tot_row[ind] + r[j]; 
                    }
                    else{
                       arr_tot_row[ind] = arr_tot_row[ind] + r[j].total; 
                    }
                    
                   }
                }
                html += '</tr>';
            }
            selector.html(html);
            // make tfooter
            var selector2 = $('#dataTablesPID tfoot');
            selector2.empty();
            html = '';
            html = '<tr style = "font-weight:600;">'+
                        '<td colspan = "2" style = "text-align:center;">Jumlah</td>';
            for (var i = 0; i < arr_tot_row.length; i++) {
                html += '<td>'+arr_tot_row[i]+'</td>';
            }            
            html += '</tr>';
            selector2.html(html);
        }).fail(function() {
            toastr.error("Connection Error, Please try again", 'Error!!');
        }).always(function() {
            newDescritionInput.getDescription();
        });

    },

    FillTS : function(){
       var arr = [];
       var YearNow = <?php echo date('Y') ?>;
       var YearTs2 = YearNow - 4;
       var selector = $('#JS_TS');
       selector.empty(); 
       for (var i = YearTs2; i <= YearNow; i++) {
           selector.append(
                '<td>'+i+'</td>'
            );
           arr.push(i);
       }
       return arr;
    },

    loaded : function(){
        var firstLoad = setInterval(function () {
            var filterProdi = $('#filterProdi').val();
            if(filterProdi!='' && filterProdi!=null){
                $('#viewProdiID').html(filterProdi);
                $('#viewProdiName').html($('#filterProdi option:selected').text());
                App_publikasi_mhs.LoadAjaxTable();
                clearInterval(firstLoad);
            }
        },1000);
        setTimeout(function () {
            clearInterval(firstLoad);
        },5000);
    }

};

$(document).ready(function () {
   App_publikasi_mhs.loaded();

});
$('#filterProdi').change(function () {
    var filterProdi = $('#filterProdi').val();
    if(filterProdi!='' && filterProdi!=null){
        $('#viewProdiID').html(filterProdi);
        $('#viewProdiName').html($('#filterProdi option:selected').text());
        App_publikasi_mhs.LoadAjaxTable();
    }
});


$(document).off('click', '.datadetailpublikasi').on('click', '.datadetailpublikasi',function(e) {
    var v = parseInt($(this).html());
    if (v > 0) {
        var dt = $(this).attr('data');
        dt = jwt_decode(dt);
        console.log(dt);
        // console.log(dt);
        var html =  '<div class = "row">'+
                        '<div class = "col-md-12">'+
                            '<table class = "table">'+
                                '<thead>'+
                                    '<tr>'+
                                        '<td>No</td>'+
                                        '<td>Judul</td>'+
                                        '<td>Nama Mahasiswa</td>'+
                                        '<td>Tanggal Terbit</td>'+
                                    '</tr>'+
                                '</thead>'+
                                '<tbody>';
                if (dt.length > 0) {
                    for (var i = 0; i < dt.length; i++) {

                        var dataAnggota = dt[i]["dataAnggota"];
                        var htmldataAnggota = '';
                        if (dataAnggota.length > 0) {
                            htmldataAnggota += '<ul>';
                            for (var j = 0; j < dataAnggota.length; j++) {
                                htmldataAnggota += '<li style = "margin-left:-30px;">'+dataAnggota[j].Nama+'</li>';
                            }

                            htmldataAnggota += '</ul>';
                        }

                        html += '<tr>'+
                                    '<td>'+ (parseInt(i)+1) + '</td>'+
                                    '<td>'+ dt[i].Judul + '</td>'+
                                    '<td>'+ htmldataAnggota + '</td>'+
                                    '<td>'+ dt[i].Tgl_terbit+ '</td>'+
                                '</tr>';
                    }
                }
                else
                {
                    html += '<tr>'+
                                '<td colspan="4"><label>No Data Detail</label></td>'+
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
    }
})

$(document).on('click','.btnSaveDescription',function(e){
    const itsme =  $(this);
    newDescritionInput.saveDescription(itsme);
})
</script>