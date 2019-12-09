
<style>
    .form-judiciums .form-control[readonly] {
        cursor: cell;
        background-color: #fff;
        color: #333;
    }
</style>

<div class="row" style="margin-top: 30px;">
    <div class="col-md-6 col-md-offset-3">
        <div class="well">
            <div class="row">
                <div class="col-md-8">
                    <label>Programme Study</label>
                    <select class="form-control" id="filterBaseProdi">
                        <option value="">-- All Programme Study --</option>
                        <option disabled>------------------------------------------</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label>Judiciums Year</label>
                    <select class="form-control" id="filterJudiciumsYear"></select>
                </div>
            </div>

        </div>
        <hr/>
    </div>
    <div class="col-md-3" style="text-align: right;">
        <button class="btn btn-default" id="btnJudiciumsSetting"><i class="fa fa-cog margin-right"></i> Judiciums Setting</button>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <table class="table table-centre table-bordered">
            <thead>
            <tr>
                <th style="width: 1%;">No</th>
                <th style="width: 15%;">Student</th>
                <th>Title</th>
            </tr>
            </thead>
        </table>
    </div>
</div>

<script>

    $(document).ready(function () {
        loadSelectOptionBaseProdi('#filterBaseProdi','');
        loadSelectOptionJudiciumsYear('filterJudiciumsYear','');
    });

    $('#btnJudiciumsSetting').click(function () {

        $('#GlobalModalLarge .modal-header').html('<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
            '<h4 class="modal-title">Judiciums Setting</h4>');

        var htmlss = '<div class="row">' +
            '    <div class="col-md-3">' +
            '        <div class="well form-judiciums">' +
            '        <div class="form-group">' +
            '            <label>Year</label>' +
            '            <input class="hide" id="formID">' +
            '            <input class="form-control" id="formYear" type="number">' +
            '        </div>' +
            '        <div class="form-group">' +
            '            <label>Judiciums Date</label>' +
            '           <input type="text" id="formJudiciumsDate" name="regular" class="form-control" readonly>' +
            '        </div>' +
            '        <div class="form-group">' +
            '            <label>Graduation Date</label>' +
            '            <input type="text" id="formGraduationDate" name="regular" class="form-control" readonly>' +
            '        </div>' +
            '        <div class="form-group">' +
            '            <label>Publish ?</label>' +
            '            <select class="form-control" id="formPublish">' +
            '                <option value="0">Unpublish</option>' +
            '                <option value="1">Publish</option>' +
            '            </select>' +
            '        </div>' +
            '        <div class="form-group" style="text-align: right;">' +
            '            <button class="btn btn-primary" id="btnSaveJudiciums">Save</button>' +
            '        </div>' +
            '    </div>' +
            '    </div>' +
            '    <div class="col-md-9">' +
            '        <table class="table table-striped table-centre">' +
            '            <thead>' +
            '            <tr>' +
            '                <th style="width: 1%;">No</th>' +
            '                <th style="width: 7%;">Year</th>' +
            '                <th>Judiciums Date</th>' +
            '                <th>Graduation Date</th>' +
            '                <th style="width: 1%;"><i class="fa fa-cog"></i></th>' +
            '                <th style="width: 15%;">Status</th>' +
            '            </tr>' +
            '            </thead>' +
            '           <tbody id="loadDataJudiciums"></tbody>' +
            '        </table>' +
            '    </div>' +
            '</div>';

        $('#GlobalModalLarge .modal-body').html(htmlss);

        loadDataJudiciums();


        $("#formJudiciumsDate, #formGraduationDate")
            .datepicker({
                showOtherMonths:true,
                autoSize: true,
                dateFormat: 'dd MM yy',
                // minDate: new Date(moment().year(),moment().month(),moment().date()),
                onSelect : function () {
                    // var data_date = $(this).val().split(' ');
                    // var nextelement = $(this).attr('nextelement');
                    // nextDatePick(data_date,nextelement);
                }
            });

        $('#GlobalModalLarge .modal-footer').html('<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>');

        $('#GlobalModalLarge').on('shown.bs.modal', function () {
            $('#formSimpleSearch').focus();
        })

        $('#GlobalModalLarge').modal({
            'show' : true,
            'backdrop' : 'static'
        });


        $('#btnSaveJudiciums').click(function () {

            var ID = $('#formID').val();
            var Year = $('#formYear').val();
            var JudiciumsDate = ($('#formJudiciumsDate').datepicker("getDate")!=null) ? moment($('#formJudiciumsDate').datepicker("getDate")).format('YYYY-MM-DD') : '';
            var GraduationDate = ($('#formGraduationDate').datepicker("getDate")!=null) ? moment($('#formGraduationDate').datepicker("getDate")).format('YYYY-MM-DD') : '';
            var Publish = $('#formPublish').val();

            if(Year!='' && Year!=null &&
                JudiciumsDate!='' && JudiciumsDate!=null &&
            GraduationDate!='' && GraduationDate!=null){

                loading_buttonSm('#btnSaveJudiciums');

                var data = {
                    action : 'updateDataJudiciums',
                    ID : ID,
                    dataForm : {
                        Year : Year,
                        JudiciumsDate : JudiciumsDate,
                        GraduationDate : GraduationDate,
                        Publish : Publish
                    }
                };

                var token = jwt_encode(data,'UAP)(*');
                var url = base_url_js+'api3/__crudYudisium';

                $.post(url,{token:token},function (jsonResult) {

                    toastr.success('Data saved','Success');

                    $('#formID').val('');
                    $('#formYear').val('');
                    $('#formJudiciumsDate').val('');
                    $('#formGraduationDate').val('');
                    loadDataJudiciums();
                    setTimeout(function () {
                        $('#btnSaveJudiciums').html('Save').prop('disabled',false);
                    },500);

                });

            } else {
                toastr.warning('Please fill in the required form','Warning');
            }



        });

    });


    function loadDataJudiciums() {

        var data = {
            action : 'readDataJudiciums'
        };

        var token = jwt_encode(data,'UAP)(*');
        var url = base_url_js+'api3/__crudYudisium';

        $.post(url,{token:token},function (jsonResult) {

            $('#loadDataJudiciums').empty();
            if(jsonResult.length>0){
                $.each(jsonResult,function (i,v) {

                    var jd = (v.JudiciumsDate!='' && v.JudiciumsDate!=null) ? moment(v.JudiciumsDate).format('dddd, DD MMM YYYY') : '';
                    var gd = (v.GraduationDate!='' && v.GraduationDate!=null) ? moment(v.GraduationDate).format('dddd, DD MMM YYYY') : '';
                     var sts = (v.Publish=='0') ? 'Unpublish' : 'Publish' ;

                    $('#loadDataJudiciums').append('<tr>' +
                        '<td style="border-right: 1px solid #CCC;">'+(i+1)+'</td>' +
                        '<td>'+v.Year+'</td>' +
                        '<td>'+jd+'</td>' +
                        '<td>'+gd+'</td>' +
                        '<td><button class="btn btn-sm btn-default btnEditJudiciums" data-id="'+v.ID+'"><i class="fa fa-edit"></i></button><textarea id="dataEdit_'+v.ID+'" class="hide">'+JSON.stringify(v)+'</textarea></td>' +
                        '<td>'+sts+'</td>' +
                        '</tr>');
                });
            }


        });

    }

    $(document).on('click','.btnEditJudiciums',function () {
        var ID = $(this).attr('data-id');
        var dataEdit = $('#dataEdit_'+ID).val();
        var d = JSON.parse(dataEdit);


        $('#formID').val(d.ID);
        $('#formYear').val(d.Year);
        (d.JudiciumsDate!=='0000-00-00' && d.JudiciumsDate!==null) ? $('#formJudiciumsDate').datepicker('setDate',new Date(d.JudiciumsDate)) : '';
        (d.GraduationDate!=='0000-00-00' && d.GraduationDate!==null) ? $('#formGraduationDate').datepicker('setDate',new Date(d.GraduationDate)) : '';
        $('#formPublish').val(d.Publish);

    });

</script>