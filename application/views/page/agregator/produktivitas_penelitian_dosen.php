
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
            <div id= "content_dt">

            </div>
        </div>
    </div>

<script>
    $(document).ready(function () {
        var firstLoad = setInterval(function () {

            loadDataTable();
            newDescritionInput.getDescription();
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
            action : 'readProduktivitasPenelitian',
        };
        var token = jwt_encode(data,'UAP)(*');
        var url = base_url_js+'api3/__crudAgregatorTB3';

        $.post(url,{token:token},function (jsonResult) {
            // arr_header_table
            var arr_header_table = jsonResult.header;
            var arr_total = [];
            // make table
            var htmlTable = '<table class = "table table-striped table-bordered dataTable2Excel" data-name="TabelProduktivitasPenelitian">'+
                                '<thead>'+
                                    '<tr style="background: #20485A;color: #FFFFFF;">'+
                                         '<td style="vertical-align : middle;text-align:center;width: 1%;" rowspan="2">No</td>'+
                                         '<td rowspan="2" style="vertical-align : middle;text-align:center;width: 15%;">Sumber Pembiayaan</td>'+
                                         '<td colspan="'+arr_header_table.length+'" style="vertical-align : middle;text-align:center;">Jumlah Judul Penelitian</td>'+
                                         '<td rowspan="2" style="vertical-align : middle;text-align:center;width: 10%;">Jumlah</td> </tr><tr style="background: #20485A;color: #FFFFFF;">';

            for (var i = 0; i < arr_header_table.length; i++) {
                htmlTable += '<td style="vertical-align : middle;text-align:center;width: 5%;">'+arr_header_table[i]+'</td>';
                arr_total.push(0);
            }

            //htmlTable += '<td>'+'Jumlah'+'</td>';
            htmlTable += '</tr></thead>'+
                        '<tbody id="listStd"></tbody>'+
                        '</table>';

            $('#content_dt').html(htmlTable);
            $('#listStd').empty();

            var arr_body_table = jsonResult.body;
            console.log(arr_body_table);
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

                        var isi = (viewDataTb!=0 && key!=0) ? '<a href="javascript:void(0);" class="showDetailLect"  data-pen="'+tokenPen+'">'+viewDataTb+'</a>' : viewDataTb;
                        htmlTableBody += '<td style="vertical-align : middle;">'+isi+'</td>';
                        //htmlTableBody += '<td>'+arr[key]+'</td>';
                    }

                    for (var m = 1; m < arr.length; m++) {
                        // console.log(m + ' ,,' +arr[m]);
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
                            '<td colspan = "2" style="vertical-align : middle;text-align:center;">Jumlah</td>'+
                            isian+
                        '</tr>'+
                    '</tfoot>'
                    );


            } else {
                $('#listStd').append('<tr><td colspan="5">Data not yet</td></tr>');
            }

        });

    }

    $(document).on('click','.showDetailLect',function () {
       var  tokenLect = $(this).attr('data-pen');
       var d = jwt_decode(tokenLect,'UAP)(*');
       //console.log(d);
       //return false;

        var tr = '';
        if(d.length>0){
            $.each(d,function (i,v) {

                //var NID = (v.NIDN!='' && v.NIDN!=null && v.NIDN!=0 && v.NIDN!='0') ? v.NIDN : '-';
                //NID = (v.NIDK!='' && v.NIDK!=null && v.NIDK!=0 && v.NIDK!='0') ? v.NIDK : NID;

               tr = tr+'<tr>' +
                   '<td style="border-right: 1px solid #ccc;">'+(i+1)+'</td>' +
                   '<td>'+v.Judul_litabmas+'</td>' +
                   '<td>'+v.Name+'</td>' +
                   '</tr>';
            });
        }


        $('#GlobalModal .modal-header').html('<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
            '<h4 class="modal-title">Judul Penelitian Dosen</h4>');
        $('#GlobalModal .modal-body').html('<div class="row">' +
            '    <div class="col-md-12">' +
            '        <table class="table table-striped" id="tableLect" style="margin-bottom: 0px;">' +
            '            <thead>' +
            '            <tr>' +
            '                <th style="width: 5%;">No</th>' +
            '                <th style="width: 25%;">Nama Judul</th>' +
            '                <th style="width: 25%;">Nama Dosen</th>' +
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
