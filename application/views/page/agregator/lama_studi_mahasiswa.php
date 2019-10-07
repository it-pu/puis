<!-- <h1>lama_studi_mahasiswa</h1> -->


<style>
    /*#dataTablesPAM tr th, #dataTablesPAM tr td {
        text-align: center;
    }*/

    #dataTablesPAM tr th{
        text-align: center;
    }
</style>

<div class="well">

    <div class="row">
        <div class="col-md-12">
            <div style="text-align: right;">
                <button onclick="saveTable2Excel('dataTable2Excel')" class="btn btn-success"><i class="fa fa-file-excel-o margin-right"></i> Excel</button>
                <!-- <button id="saveToExcel" class="btn btn-success"><i class="fa fa-file-excel-o margin-right"></i> Excel</button> -->
            </div>
            <br/>
            <div id="viewTable"></div>
        </div>
        <p style="color: orangered;">
            *) Tanggal Lulus - Tanggal SK penerimaan mahasiswa (hitungan dalam bulan)

        </p>
    </div>
</div>
<script>

    $(document).ready(function () {
        loadDataPAM();
    });

    function loadDataPAM() {
        var HtmlTable = ' <table class="table dataTable2Excel" id="dataTablesPAM" data-name="TblLamaStudiMahasiswa">' +
            '                <thead>' +
			'                <tr>    ' +
			'                    <th colspan="2" style="border-right: 1px solid #ccc;"></th> ' +
			'                    <th colspan="3" style="border-right: 1px solid #ccc;">Jumlah Lulusan pada</th> ' +
			'                    <th colspan="3" style="border-right: 1px solid #ccc;">Rata-rata Masa Studi Lulusan pada</th>  ' +
			'                    <th style="border-right: 1px solid #ccc;"></th>  ' +
			'                </tr>';



        var data = {
            action : 'viewLamaStudy',
            Type : '1'
        };

        var token = jwt_encode(data,'UAP)(*');
        var url = base_url_js+'api3/__crudAgregatorTB5';

        $.post(url,{token:token},function (jsonResult) {
        	// console.log(jsonResult);
            var header = jsonResult.header;
            var arr_total = [];
            for (var i = 0; i < header.length; i++) {
                 HtmlTable += '<td style = "text-align: center">'+header[i]+'</td>';
                 if (i >= 2 && i <= 4) { // define total Jumlah PS dan Jumlah Lulusan pada
                     arr_total.push(0);
                 }
            }

            HtmlTable+= '</tr></thead><tbody id = "listData"></tbody>';
            HtmlTable+= '</table>';
            $('#viewTable').html(HtmlTable);

            var body = jsonResult.body;
            if (body.length > 0) {
                var htmlBody = '';
                for (var i = 0; i < body.length; i++) {
                    htmlBody += '<tr>';
                    var No = parseInt(i) + 1;
                    var arr_body = body[i];
                    htmlBody += '<td>'+No+'</td>';
                    for (var j = 0; j < arr_body.length; j++) {
                        if(j < 4){
                            if (j == 0) {
                                htmlBody += '<td style = "text-align: center">'+arr_body[j].show+'</td>';
                            }
                            else
                            {
                                htmlBody += '<td style = "text-align: center"><a href = "javascript:void(0);" class = "datadetail" data = "'+arr_body[j].data+'">'+arr_body[j].show+'</a></td>';
                            }
                            if (j >= 1) { // isi total Jumlah PS dan Jumlah Lulusan pada
                                arr_total[(j-1)] = parseInt(arr_total[(j-1)]) + parseInt(arr_body[j].show);
                            }
                        }
                        else
                        {
                            RataMasaStudy = getCustomtoFixed(arr_body[j],1);
                            htmlBody += '<td style = "text-align: center">'+RataMasaStudy+'</td>';
                        }
                    }

                    htmlBody += '</tr>';
                }

                $('#listData').append(htmlBody);
                // console.log(arr_total);
                var tbl = $('#listData').closest('table');
                var isian = '';
                for (var i = 0; i < arr_total.length; i++) {
                    isian += '<td style = "text-align: center">'+arr_total[i]+'</td>';
                }
                tbl.append(
                    '<tfoot>'+
                        '<tr>'+
                            '<td colspan = "2">Jumlah</td>'+
                            isian+
                        '</tr>'+
                    '</tfoot>'
                    );

            }

        });
    }

    $(document).off('click', '.datadetail').on('click', '.datadetail',function(e) {
        var v = parseInt($(this).html());
        if (v > 0) {
            var dt = $(this).attr('data');
            // console.log(dt);
            dt = jwt_decode(dt);
            var html =  '<div class = "row">'+
                            '<div class = "col-md-12">'+
                                '<table class = "table">'+
                                    '<thead>'+
                                        '<tr>'+
                                            '<td>No</td>'+
                                            '<td>NPM</td>'+
                                            '<td>NAMA</td>'+
                                        '</tr>'+
                                    '</thead>'+
                                    '<tbody>';
                    for (var i = 0; i < dt.length; i++) {
                        html += '<tr>'+
                                    '<td>'+ (parseInt(i)+1) + '</td>'+
                                    '<td>'+ dt[i].NPM + '</td>'+
                                    '<td>'+ dt[i].Name + '</td>'+
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

</script>
