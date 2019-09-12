

<style>
    #dataTable tr th, #dataTable tr td {
        text-align: center;
    }
</style>

<div class="well">
    <div class="row">
        <div class="col-md-12">
            <div style="text-align: right"><button class="btn btn-success" id="btndownloaadExcel" title="Dowload Excel"><i class="fa fa-file-excel-o margin-right"></i> Excel</button></div>
            <table class="table" id="dataTable">
                <thead>
                <tr>
                    <th style="width: 1%;" rowspan="2">No</th>
                    <th rowspan="2">Pendidikan</th>
                    <th colspan="4">Jabatan Akademik</th>
                    <th style="width: 10%;" rowspan="2">Tenaga Pengajar</th>
                    <th style="width: 10%;" rowspan="2">Jumlah</th>
                </tr>
                <tr>
                    <th style="width: 10%;">Asisten Ahli</th>
                    <th style="width: 10%;">Lektor</th>
                    <th style="width: 10%;">Lektor Kepala</th>
                    <th style="width: 10%;">Guru Besar</th>
                </tr>
                </thead>
                <tbody id="listData"></tbody>
            </table>

        </div>
    </div>
</div>

<script>
    var passToExcel = [];
    $(document).ready(function () {
        loadData();
    });

    function loadData() {
        passToExcel = [];
        var url = base_url_js+'api3/__getJabatanAkademikDosenTidakTetap';
        $.getJSON(url,function (jsonResult) {

            $('#listData').empty();
            if(jsonResult.length>0){
                var AA = 0;
                var L = 0;
                var LK = 0;
                var GB = 0;
                var TP = 0;
                var J = 0;
                $.each(jsonResult,function (i,v) {
                   
                    var td = '';
                    var total = 0;
                    $.each(v.details,function (i2, v2) {
                        var det = v2.dataEmployees.length;
                        td = td+'<td>'+v2.dataEmployees.length+'</td>';
                        total = total + parseInt(v2.dataEmployees.length);
                
                        if(i2==0){
                            TP = TP+det; //tenaga pengajar
                        }
                        else if(i2==1){ // Asisten Ahli
                            AA = AA+det;
                        }
                        else if(i2==2){ // lektor
                            L = L + det;
                        }
                        else if(i2==3){ // lektor kepala
                            LK = LK+det;
                        }
                        else if(i2==4){ // Guru besar
                            GB = GB+det;
                        }
                        
                    });

                    $('#listData').append('<tr>' +
                        '<td style="border-right: 1px solid #ccc;">'+(i+1)+'</td>' +
                        '<td style="text-align: left;">'+v.Level+' - '+v.Description+'</td> '+td+
                        '<td style="text-align: center;background: lightyellow;border-left: 1px solid #ccc;">'+total+'</td>' +
                        '</tr>');
                     J = J + total;

                });

                $('#listData').append('<tr>' +
                    '<th colspan="2" class="tdJml">Jumlah</th>' +
                    '<th class="tdJml">'+AA+'</th>' +
                    '<th class="tdJml">'+L+'</th>' +
                    '<th class="tdJml">'+LK+'</th>' +
                    '<th class="tdJml">'+GB+'</th>' +
                    '<th class="tdJml">'+TP+'</th>' +
                    '<th class="tdJml">'+J+'</th>' +
                    '</tr>');

                passToExcel = jsonResult
            }

        });
    }


    $(document).off('click', '#btndownloaadExcel').on('click', '#btndownloaadExcel',function(e) {
        if (passToExcel.length > 0) {
            var url = base_url_js+'agregator/excel-dosen-tidak-tetap';
            data = {
              passToExcel : passToExcel,
            }
            var token = jwt_encode(data,"UAP)(*");
            FormSubmitAuto(url, 'POST', [
                { name: 'token', value: token },
            ]);
        }

    })
</script>