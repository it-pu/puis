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
    </div>
</div>

<script>
    $(document).ready(function () {
        loadIPK();
        newDescritionInput.getDescription();
    });

    function loadIPK() {

        var data = {
            action : 'viewIPK',
        };

        var token = jwt_encode(data,'UAP)(*');
        var url = base_url_js+'api3/__crudAgregatorTB5';

        $.post(url,{token:token},function (jsonResult) {
            var HtmlTable ='<table class ="table dataTable2Excel" data-name="TblviewIPK">'+
                                '<thead>'+
                                    '<tr>'+
                                        '<th colspan="2" style="border-right: 1px solid #ccc;"></th>'+
                                        '<th style="border-right: 1px solid #ccc; text-align: center"> Jumlah PS </th>'+
                                        '<th colspan="'+jsonResult.countYear+'" style="border-right: 1px solid #ccc; text-align: center">Jumlah Lulusan pada</th>'+
                                        '<th colspan="'+jsonResult.countYear+'" style="border-right: 1px solid #ccc; text-align: center">Rata-rata IPK Lulusan pada</th>'+
                                        '<th style="border-right: 1px solid #ccc;"></th>'+
                                    '</tr>'+
                                    '<tr>';

           var header = jsonResult.header;
           var arr_total = [];
           for (var i = 0; i < header.length; i++) {
                HtmlTable += '<td>'+header[i]+'</td>';
                if (i >= 2 && i <= jsonResult.ci) { // define total Jumlah PS dan Jumlah Lulusan pada
                    arr_total.push(0);
                }
            }

            HtmlTable+= '</tr></thead><tbody id = "listData"></tbody>';
            HtmlTable+= '</table>';
            $('#viewTable').html(HtmlTable);
            var body = jsonResult.body;
            var htmlBody = '';
            for (var i = 0; i < body.length; i++) {
                htmlBody += '<tr>';
                var No = parseInt(i) + 1;
                var arr_body = body[i];
                // console.log(arr_body);
                htmlBody += '<td>'+No+'</td>';
                for (var j = 0; j < arr_body.length; j++) {
                    if(j < jsonResult.ci){
                        // htmlBody += '<td>'+arr_body[j]+'</td>';
                        if (j == 0) {
                            htmlBody += '<td>'+arr_body[j].show+'</td>';
                        }
                        else
                        {
                            if (j == 1) {
                                htmlBody += '<td><a href = "javascript:void(0);" class = "datadetailPS" data = "'+arr_body[j].data+'">'+arr_body[j].show+'</a></td>';
                            }
                            else
                            {
                                htmlBody += '<td><a href = "javascript:void(0);" class = "datadetail" data = "'+arr_body[j].data+'">'+arr_body[j].show+'</a></td>';
                            }

                        }
                        if (j >= 1) { // isi total Jumlah PS dan Jumlah Lulusan pada
                            arr_total[(j-1)] = parseInt(arr_total[(j-1)]) + parseInt(arr_body[j].show);
                        }
                    }
                    else
                    {
                        IPK = getCustomtoFixed(arr_body[j].show,2);
                        htmlBody += '<td>'+IPK+'</td>';
                    }
                }

                htmlBody += '</tr>';
            }

            $('#listData').append(htmlBody);

            // console.log(arr_total);
            var tbl = $('#listData').closest('table');
            var isian = '';
            for (var i = 0; i < arr_total.length; i++) {
                isian += '<td>'+arr_total[i]+'</td>';
            }
            tbl.append(
                '<tfoot>'+
                    '<tr>'+
                        '<td colspan = "2">Jumlah</td>'+
                        isian+
                    '</tr>'+
                '</tfoot>'
                );

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

    $(document).off('click', '.datadetailPS').on('click', '.datadetailPS',function(e) {
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
                                            '<td>Program Study</td>'+
                                        '</tr>'+
                                    '</thead>'+
                                    '<tbody>';
                    for (var i = 0; i < dt.length; i++) {
                        html += '<tr>'+
                                    '<td>'+ (parseInt(i)+1) + '</td>'+
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

    $(document).on('click','.btnSaveDescription',function(e){
        const itsme =  $(this);
        newDescritionInput.saveDescription(itsme);
    })
</script>
