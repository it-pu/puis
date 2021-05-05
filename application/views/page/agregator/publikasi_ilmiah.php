
<style>
    .dataTable2Excel tr th, .dataTable2Excel tr td {
        text-align: center;
    }
    .dataTable2Excel tr td:nth-child(2) {
        text-align: left;
    }
</style>

<div class="well">

    <div class="row">
        
        <div class="col-md-12">
            <div style="text-align: right;">
                <button onclick="saveTable2Excel('dataTable2Excel')" class="btn btn-success"><i class="fa fa-file-excel-o margin-right"></i> Excel</button> <p></p>
                <!-- <button id="saveToExcel" class="btn btn-success"><i class="fa fa-file-excel-o margin-right"></i> Excel</button> -->
            </div>
            <div id = "content_dt">
                
            </div>
        </div>
    </div>

<script>
    $(document).ready(function () {
        var firstLoad = setInterval(function () {

            loadDataTable();
            clearInterval(firstLoad);

        },1000);

        setTimeout(function () {
            clearInterval(firstLoad);
        },5000);

    });

    $("#btndownloaadExcel").click(function(){
        var akred = "0";

        var url = base_url_js+'agregator/excel-mahasiswa-asing';
        data = {
          akred : akred
        }
        var token = jwt_encode(data,"UAP)(*");
        FormSubmitAuto(url, 'POST', [
            { name: 'token', value: token },
        ]);
    })

    $('#filterYear').change(function () {
        loadDataTable();

    });

    function selectyearstudy() {

        var url = base_url_js+'api3/__crudAgregatorTB5';
        var token = jwt_encode({action : 'yearstudy'},'UAP)(*');

        $.post(url,{token:token},function (jsonResult) {
            $('#formYear').append('<option disabled selected></option>');
                for(var i=0;i<jsonResult.length;i++){
                   $('#formYear').append('<option id="'+jsonResult[i].Year+'"> '+jsonResult[i].Year+' </option>');
                }
            });
      }


    function loadDataTable() {
        var data = {
            action : 'readPublikasiIlmiah',
        };
        var token = jwt_encode(data,'UAP)(*');
        var url = base_url_js+'api3/__crudAgregatorTB5';

        $.post(url,{token:token},function (jsonResult) {
            var arr_header_table = jsonResult.header;
            var arr_total = [];
            // make table
            var htmlTable = '<table class = "table table-striped table-bordered dataTable2Excel" data-name="TabelProduktivitasPKMdosen">'+
                                '<thead>'+
                                 '<tr style="background: #20485A;color: #FFFFFF;">'+
                                   '<td style="vertical-align : middle;text-align:center;width: 1%;" rowspan="2">No</td>'+
                                   '<td rowspan="2" style="vertical-align : middle;text-align:center;width: 15%;">Jenis Publikasi</td>'+
                                   '<td colspan="'+arr_header_table.length+'" style="vertical-align : middle;text-align:center;">Jumlah Judul</td>'+
                                   '<td rowspan="2" style="vertical-align : middle;text-align:center;width: 5%;">Jumlah</td> </tr><tr style="background: #20485A;color: #FFFFFF;">';

           
            for (var i = 0; i < arr_header_table.length; i++) {
                htmlTable += '<td style="vertical-align : middle;text-align:center;width: 5%;">'+arr_header_table[i]+'</td>';
                arr_total.push(0);
            }
            
            htmlTable += '</tr>'+
                        '</thead>'+
                        '<tbody id="listStd"></tbody>'+
                        '</table>';
            $('#content_dt').html(htmlTable);            
            $('#listStd').empty();

            var arr_body_table = jsonResult.body;
            // console.log(arr_body_table);
            if(arr_body_table.length>0){
                for (var i = 0; i < arr_body_table.length; i++) {
                    var jumlahKanan = 0;
                    var No = parseInt(i) + 1;
                    var htmlTableBody = '<tr>'+
                                            '<td>'+No+'</td>';
                    var arr = arr_body_table[i];
                    for(var key in arr) {
                        var viewDataTb = (key==0) ? arr[key] : arr[key].length;
                        var tokenPen = jwt_encode(arr[key],'UAP)(*');

                        var isi = (viewDataTb!=0 && key!=0) ? '<a href="javascript:void(0);" class="showDetailLect"  data-pkm="'+tokenPen+'">'+viewDataTb+'</a>' : viewDataTb;
                        htmlTableBody += '<td style="vertical-align : middle;">'+isi+'</td>';
                    }

                    for (var m = 1; m < arr.length; m++) {

                       arr_total[(m-1)] = parseInt(arr_total[(m-1)]) + parseInt(arr[m].length);
                       jumlahKanan += parseInt(arr[m].length);
                    }

                    htmlTableBody += '<td style="vertical-align : middle;text-align:center;">'+jumlahKanan+'</td>';
                    htmlTableBody += '</tr>';

                    $('#listStd').append(htmlTableBody);
                }
                // console.log(arr_total);
                var tbl = $('#listStd').closest('table');
                var isian = '';
                var jumlahbawahkanan = 0;
                for (var i = 0; i < arr_total.length; i++) {
                    isian += '<td style="vertical-align : middle;text-align:center;">'+arr_total[i]+'</td>';
                    jumlahbawahkanan += parseInt(arr_total[i]);
                }
                isian += '<td style="vertical-align : middle;text-align:center;">'+jumlahbawahkanan+'</td>';
                tbl.append(
                    '<tfoot>'+
                        '<tr>'+
                            '<td colspan = "2" style="vertical-align : middle;text-align:center;"><b>Jumlah</b></td>'+
                            isian+
                        '</tr>'+
                    '</tfoot>'        
                    );


            } else {
                $('#listStd').append('<tr><td colspan="5">Data not yet</td></tr>');
            }

        });

        newDescritionInput.getDescription();

    }


    $(document).on('click','.showDetailLect',function () {
       var  tokenLect = $(this).attr('data-pkm');
       var d = jwt_decode(tokenLect,'UAP)(*');
       //console.log(d);
       //return false;

        var tr = '';
        if(d.length>0){
            $.each(d,function (i,v) {

               tr = tr+'<tr>' +
                   '<td style="border-right: 1px solid #ccc;text-align:center;">'+(i+1)+'</td>' +
                   '<td>'+v.Judul+'</td>' +
                   '<td>'+v.Name+'</td>' +
                   '</tr>';
            });
        }


        $('#GlobalModal .modal-header').html('<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
            '<h4 class="modal-title">Judul Publikasi Ilmiah</h4>');
        $('#GlobalModal .modal-body').html('<div class="row">' +
            '    <div class="col-md-12">' +
            '        <table class="table table-striped table-bordered" id="tableLect" style="margin-bottom: 0px;">' +
            '            <thead>' +
            '            <tr style="background: #20485A;color: #FFFFFF;">' +
            '                <th style="width: 5%; text-align:center;">No</th>' +
            '                <th style="width: 25%; text-align:center;">Judul Publikasi Ilmiah</th>' +
             '                <th style="width: 20%; text-align:center;">Nama Dosen</th>' +
            '            </tr>' +
            '            </thead>' +
            '            <tbody>'+tr+'</tbody>' +
            '        </table>' +
            '    </div>' +
            '</div>');
        $('#GlobalModal .modal-footer').html('<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>');
        $('#GlobalModal').modal({
            'show' : true,
            'backdrop' : 'static'
        });

    });

    $(document).on('click','.btnSaveDescription',function(e){
        const itsme =  $(this);
        newDescritionInput.saveDescription(itsme);
    })
</script>