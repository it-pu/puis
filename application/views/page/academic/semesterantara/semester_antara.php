

<div class="row" style="margin-top: 30px;">
    <div class="col-md-12">
        <div class="widget box">
            <div class="widget-header">
                <h4 class=""><i class="icon-reorder"></i> Semester Antara</h4>
                <div class="toolbar no-padding">
                    <div class="btn-group">
                        <span class="btn btn-xs btn-action" data-action="addSemesterAntara" id="btn_addTahunAkademik">
                            <i class="icon-plus"></i> Semester Antara
                        </span>
                    </div>
                </div>

            </div>
            <div class="widget-content">
                <div id="loadPage"></div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        loadDataSemesterAntara();
    });

    $('.btn-action').click(function () {
        var action = $(this).attr('data-action');
        var btnSave = (action=='addSemesterAntara') ? 'add' : 'edit';
        $('#GlobalModal .modal-header').html('<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
            '<h4 class="modal-title">Semester Antara</h4>');
        $('#GlobalModal .modal-body').html('<table class="table">' +
            '<tr>' +
            '<td style="width: 25%;">Semester</td>' +
            '<td><select class="form-control" id="formSemester"></select></td>' +
            '</tr>' +
            '</table>');
        // loadSelectOptionProgramCampus('#formProgram','');
        loSelectOptionSemester('#formSemester','');
        // loadSelectOptionSemester('#formSemester','');

        $('#GlobalModal .modal-footer').html('<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>' +
            '<button type="button" id="btnSave" data-id="0" data-action="'+action+'" class="btn btn-success">'+ucwords(btnSave)+'</button>');

        $('#GlobalModal').modal({
            'show' : true,
            'backdrop' : 'static'
        });

    });

    $(document).on('click','#btnSave',function () {
        var dataID = $(this).attr('data-id');
        var action = $(this).attr('data-action');
        // var SemesterID = $('#formProgram').val();
        var DataSemester = $('#formSemester').val();

        if(DataSemester!=''){
            var sp = DataSemester.split('.');

            var Semester = $('#formSemester option:selected').text().trim();

            var SemesterID = sp[0];

            var s = Semester.split('/');
            var Year = s[0];
            var smt = s[1].split(' ')[1].trim();

            var Code = (smt=='Ganjil') ? 3 : 4;
            var Name = Semester+' - Antara';

            var ID = (action=='edit') ? dataID : '';

            var data = {
                action : action,
                ID : ID,
                dataForm : {
                    SemesterID : SemesterID,
                    Year : Year,
                    Code : Code,
                    Name : Name,
                    Status : '0',
                    UpdateBy : sessionNIP,
                    UpdateAt : dateTimeNow()

                }
            };

            var token = jwt_encode(data,'UAP)(*');
            var url = base_url_js+'api/__crudTahunAkademik';

            loading_buttonSm('#btnSave');
            $.post(url,{token:token},function (jsonResult) {

                if(jsonResult!=0){
                    toastr.success('Saved','Success');
                    loadDataSemesterAntara();
                } else {
                    toastr.warning('Data Already Axist','Data Exist!');
                }

                setTimeout(function () {
                    $('#GlobalModal').modal('hide');
                },500);
            });
        }
    });
    


    function loadDataSemesterAntara() {

        $('#loadPage').html('<table class="table table-bordered table-striped">' +
            '                    <thead class="head-center">' +
            '                    <tr>' +
            '                        <th style="width: 1%;">No</th>' +
            '                        <th style="width: 10%;">Year Code</th>' +
            '                        <th>Semester Name</th>' +
            '                        <th style="width: 10%;">Students</th>' +
            '                        <th style="width: 10%;">Action</th>' +
            '                        <th style="width: 10%;">Status</th>' +
            '                    </tr>' +
            '                    </thead>' +
            '                    <tbody id="trSmtAntara">' +
            '                    </tbody>' +
            '                </table>');

        var url = base_url_js+'api/__crudTahunAkademik';
        var token = jwt_encode({action:'readSemesterAntara'},'UAP)(*');
        $.post(url,{token:token},function (jsonResult) {

            if(jsonResult.length>0){
                var no=1;
                for(var i=0;i<jsonResult.length;i++){
                    var data = jsonResult[i];
                    var status = (data.Status==1) ? '<span class="label label-success">Publish</span>' : '<span class="label label-danger">Unpublish</span>';
                    var btnAct = (data.Status==1) ? '<li><a href="javascript:void(0);" class="btnUnpublish" data-id="'+data.ID+'">Unpublish</a></li>'
                        : '<li><a href="javascript:void(0);" class="btnPublish" data-id="'+data.ID+'">Publish</a></li>';

                    var btnAct = '<div class="btn-group">' +
                        '    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">' +
                        '        <i class="fa fa-edit"></i> <span class="caret"></span>' +
                        '    </button>' +
                        '    <ul class="dropdown-menu">' +
                        '      '+btnAct+
                        '    </ul>' +
                        '</div>';

                    $('#trSmtAntara').append('<tr>' +
                        '<td class="td-center">'+no+'</td>' +
                        '<td class="td-center">'+data.Year+''+data.Code+'</td>' +
                        // '<td><a href="javascipt:void(0);" data-id="'+data.ID+'" class="btnDetails">'+data.Name+'</a></td>' +
                        '<td><a href="'+base_url_js+'academic/semester-antara/timetable/'+data.ID+'">'+data.Name+'</a></td>' +
                        '<td class="td-center">'+data.TotalStudent+'</td>' +
                        '<td class="td-center">'+btnAct+'</td>' +
                        '<td class="td-center">'+status+'</td>' +
                        '</tr>');
                    no += 1;


                }


            }
        });
    }


    // ==============================
    $(document).on('click','.btnDetails',function () {
        var SA_ID = $(this).attr('data-id');
        loadDetails(SA_ID);
    });

    function loadDetails(SA_ID) {
        var url = base_url_js+'academic/semester-antara/details/'+SA_ID;
        $.get(url,function (html) {
            $('#loadPage').html(html);
        })
    }

    // === BTN Publish ===
    $(document).on('click','.btnPublish',function () {

        if(confirm('Are you sure?')){
            var ID = $(this).attr('data-id');

            var url = base_url_js+'api/__crudTahunAkademik';
            var token = jwt_encode({action:'publishSemesterAntara',ID:ID},'UAP)(*');

            $.post(url,{token:token},function () {
                toastr.success('Published','Success');
                setTimeout(function () {
                    loadDataSemesterAntara();
                },500);
            });
        }

    });

    $(document).on('click','.btnUnpublish',function () {

        if(confirm('Are you sure?')){
            var ID = $(this).attr('data-id');

            var url = base_url_js+'api/__crudTahunAkademik';
            var token = jwt_encode({action:'UnpublishSemesterAntara',ID:ID},'UAP)(*');

            $.post(url,{token:token},function () {
                toastr.success('Published','Success');
                setTimeout(function () {
                    loadDataSemesterAntara();
                },500);
            });
        }

    });

</script>