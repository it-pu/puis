<div class="row" id="pageMK" style="margin-top: 30px;">
    <div class="col-md-12">
        <div class="widget box">
            <div class="widget-header">
                <h4 class=""><i class="icon-reorder"></i> Courses</h4>
                <div class="toolbar no-padding">
                    <div class="btn-group">
                        <span class="btn btn-xs" id="btn_addmk">
                            <i class="icon-plus"></i> Courses
                        </span>
                    </div>
                </div>
            </div>
            <div class="widget-content no-padding">
                <div class="col-md-12">
                    <div class="col-md-3">
                        <div id="test"></div>
                    </div>
                </div>
                <div id="loadTableMK">
                </div>

            </div>
        </div>
    </div>
</div>

<style>
    .TableTools_collection {
        z-index : 1;
    }
</style>
<script>
    var data_credit_type_courses = <?php echo json_encode($data_credit_type_courses) ?>;
    $(document).ready(function() {
        loadDataTableMK();
    } );

    function OPCourseType(dtselected = null)
    {
        var h = '';
        for (var i = 0; i < data_credit_type_courses.length; i++) {
            var selected = (dtselected == data_credit_type_courses[i].ID) ? 'selected' : '';
            h += '<option value ="'+data_credit_type_courses[i].ID+'"  '+selected+' >'+data_credit_type_courses[i].NamaType+'</option>';
        }
        return h;
    }

    $('#btn_addmk').click(function () {

        $('#GlobalModal .modal-header').html('<h4 class="modal-title">Mata Kuliah</h4>');
        // $('#GlobalModal .modal-body').html('Announcement');
        $('#GlobalModal .modal-body').html('<div class="row">' +
            '<table class="table">' +
            '<tr>' +
            '<td>Base Prodi</td>' +
            '<td>:</td>' +
            '<td>' +
            '<select class="form-control" id="FormBaseProdi"></select>' +
            '</td>' +
            '</tr>' +
            '<tr>' +
            '<td>MKCode</td>' +
            '<td>:</td>' +
            '<td>' +
            '<div class="input-group">' +
            // '  <span class="input-group-addon" id="frontCode">ARC</span>' +
            '  <input type="text" class="form-control" id="formCode" style="max-width: 150px;">' +
            '</div>' +
            '</td>' +
            '</tr>' +
            '<tr>' +
            '<td>Name (Indo)</td>' +
            '<td>:</td>' +
            '<td><input class="form-control" id="formName"></td>' +
            '</tr>' +
            '<tr>' +
            '<td>Name (Eng)</td>' +
            '<td>:</td>' +
            '<td><input class="form-control" id="formNameEng"></td>' +
            '</tr>' +
            '<tr>' +
            '<td>Type</td>' +
            '<td>:</td>' +
            '<td>' +
            '<div class="row">' +
            '<div class="col-md-6"><select id="formTypeMK" class="form-control form-mk"></select></div> ' +
            '<div class="col-md-6"><select id="formCourseType" class="form-control">' +
            // '<option value="1">Teori</option>' +
            // '<option value="2">Praktikum / Praktik</option>' +
            // '<option value="3">Praktik Lapangan</option>' +
            OPCourseType()+
            '</select></div> ' +
            '</div> ' +
            '</td>' +
            '</tr>' +
            '<tr>' +
            '   <td colspan="2"></td>' +
            '   <td>' +
            '        <div class="checkbox">' +
            '    <label>' +
            '      <input type="checkbox" id="formYudisium" value="1"> For Yudisium Filter ' +
            '    </label>' +
            '  </div>' +
            '   </td>' +
            '</tr>' +
            '</table>' +
            '</div>');

        loadSelectOptionBaseProdiAll('#FormBaseProdi','');
        loadSelectOptionTypeMK('#formTypeMK');

        $('#GlobalModal .modal-footer').html('<button type="button" id="btnClose" class="btn btn-default" data-dismiss="modal">Close</button>' +
            '<button type="button" class="btn btn-success" data-act="add" id="btnAddMK">Save</button>');
        $('#GlobalModal').modal({
            'show' : true,
            'backdrop' : 'static'
        });


    });

    $(document).on('click','.btn-mk-action',function () {
        var idMK = $(this).attr('data-id');
        var url = base_url_js+'api/__getMKByID';
        $.post(url,{idMK:idMK},function (data) {
            var valueMK = data[0];

            $('#GlobalModal .modal-header').html('<h4 class="modal-title">Mata Kuliah</h4>');


            var sc_yudisium = (valueMK.Yudisium=='1') ? 'checked' : '';

            $('#GlobalModal .modal-body').html('<div class="row">' +
                '<table class="table">' +
                '<tr>' +
                '<td>Base Prodi</td>' +
                '<td>:</td>' +
                '<td>' +
                '<select class="form-control form-mk" id="FormBaseProdi"></select>' +
                '</td>' +
                '</tr>' +
                '<tr>' +
                '<td>MKCode</td>' +
                '<td>:</td>' +
                '<td><input class="form-control form-mk" id="formCode" value="'+valueMK.MKCode+'"></td>' +
                '</tr>' +
                '<tr>' +
                '<td>Name (Indo)</td>' +
                '<td>:</td>' +
                '<td><input class="form-control form-mk" id="formName" value="'+valueMK.Name+'"></td>' +
                '</tr>' +
                '<tr>' +
                '<td>Name (Eng)</td>' +
                '<td>:</td>' +
                '<td><input class="form-control form-mk" id="formNameEng" value="'+valueMK.NameEng+'"></td>' +
                '</tr>' +
                '<tr>' +
                '<td>Type</td>' +
                '<td>:</td>' +
                '<td>' +
                '<div class="row">' +
                '<div class="col-md-6"><select id="formTypeMK" class="form-control form-mk"></select></div> ' +
                '<div class="col-md-6"><select id="formCourseType" class="form-control form-mk">' +
                // '<option value="1">Teori</option>' +
                // '<option value="2">Praktikum / Praktik</option>' +
                // '<option value="3">Praktik Lapangan</option>' +
                OPCourseType(valueMK.CourseType)+
                '</select></div> ' +
                '</div> ' +
                '</td>' +
                '</tr>' +
                '<tr>' +
                '   <td colspan="2"></td>' +
                '   <td>' +
                '        <div class="checkbox">' +
                '    <label>' +
                '      <input type="checkbox" class="form-mk" id="formYudisium" value="1" '+sc_yudisium+'> For Yudisium Filter ' +
                '    </label>' +
                '  </div>' +
                '   </td>' +
                '</tr>' +
                '</table>' +
                '</div>');

            // $('#formCourseType').val(valueMK.CourseType);

            loadSelectOptionBaseProdi('#FormBaseProdi',valueMK.BaseProdiID);
            $('.form-mk').prop('disabled',true).css('color','#333');
            $('#GlobalModal .modal-footer').html('<button type="button" id="btnClose" class="btn btn-default" data-dismiss="modal">Close</button>' +
                '<button type="button" class="btn btn-success hide" data-act="edit" data-id="'+idMK+'" id="btnAddMK">Save</button>' +
                '<button type="button" class="btn btn-info" id="btnEditMK">Edit Data</button>' +
                // '<button type="button" class="btn btn-danger" data-act="delete" data-id="'+idMK+'" id="btnDeleteMK" style="float: left;">Delete</button>' +
                '');

            loadSelectOptionTypeMK('#formTypeMK',valueMK.TypeMK);

            $('#GlobalModal').modal({
                'show' : true,
                'backdrop' : 'static'
            });
        });
    });

    // $(document).on('change','#FormBaseProdi',function () {
    //     var prodi = $('#FormBaseProdi').find(':selected').val().split('.');
    //     $('#frontCode').text(prodi[1]);
    // });

    $(document).on('click','#btnAddMK',function () {

        var action = $(this).attr('data-act');

        var ID = (action=='edit') ? $(this).attr('data-id') : '';

        var process = true;

        var prodi = $('#FormBaseProdi').find(':selected').val().split('.');
        var formCode = $('#formCode').val();


        var MKCode = formCode ;
        var Name = $('#formName').val();

        var NameEng = $('#formNameEng').val();

        var formTypeMK = $('#formTypeMK').val();
        var formCourseType = $('#formCourseType').val();

        process = formRequired('#formCode,#formName,#formNameEng,#formTypeMK');

        if(process){
            // Cek Kesamaan Kode MK
            var url = base_url_js+"api/__cekMKCode";
            $.post(url,{MKCode:MKCode},function (result) {
                if(result.length>0 && action=='add'){
                    toastr.error('MK Code Exis','Error!!');
                    $('#formCode').css('border','1px solid red');
                } else {

                    var data = {
                        action : action,
                        ID : ID,
                        dataForm : {
                            MKCode : MKCode,
                            Name : Name,
                            NameEng : NameEng,
                            BaseProdiID : prodi[0].trim(),
                            TypeMK : formTypeMK,
                            CourseType : formCourseType,
                            Yudisium : ($('#formYudisium').is(':checked')) ? '1' : '0',
                            UpdateBy : sessionNIP,
                            UpdateAt : dateTimeNow()
                        }

                    };

                    loading_button('#btnAddMK');
                    $('#btnClose, #btnDeleteMK, .form-mk')
                        .prop('disabled',true);

                    var token = jwt_encode(data,'UAP)(*');
                    var url_insert = base_url_js+"api/__crudMataKuliah";
                    $.post(url_insert,{token:token},function () {
                        loadDataTableMK();
                        setTimeout(function () {

                            $('#btnAddMK').html('Save');
                            $('#btnAddMK, #btnDeleteMK, #btnClose, .form-mk')
                                .prop('disabled',false);

                            if(action=='add'){
                                $('#formCode, #formName, #formNameEng').val('');
                            }

                            toastr.success('Data Tersimpan','Success!!')
                        },1000);
                    });
                }

            });
        } else {
            toastr.error('Form Required','Error!!');
        }


    });

    $(document).on('click','#btnDeleteMK',function () {
        if (window.confirm('Hapus Mata Kuliah ?'))
        {
            loading_button('#btnDeleteMK');
            $('#btnAddMK, #btnDeleteMK, #btnClose, #FormBaseProdi, #formCode, #formName, #formNameEng')
                .prop('disabled',true);

            var ID = $(this).attr('data-id');
            var data = {
                action : 'delete',
                ID : ID
            };

            var token = jwt_encode(data,'UAP)(*');
            var url = base_url_js+"api/__crudMataKuliah";
            $.post(url,{token:token},function (result) {
                loadDataTableMK();
                setTimeout(function () {
                    $('#btnDeleteMK').html('Delete');
                    $('#btnAddMK, #btnDeleteMK, #btnClose, #FormBaseProdi, #formCode, #formName, #formNameEng')
                        .prop('disabled',false);

                    $('#GlobalModal').modal('hide');
                },1000);
            });
            // log('ok');
        }
    });

    $(document).on('change','#generateMKCode',function () {
        if($(this).is(':checked')){
            var prodi = $('#FormBaseProdi').find(':selected').val().split('.');
            var data = {
                ID : prodi[0],
                ProdiCOde : prodi[1]
            };
            var url = base_url_js+'api/__genrateMKCode';
            var token = jwt_encode(data,'UAP)(*');
            $.post(url,{token:token},function (result) {


                var MKCode = function gen (result) {
                    var url_ = base_url_js+"api/__cekMKCode";
                    var GenMKCode = prodi[1]+''+pad(result[0].TotalMK,4);
                    $.post(url_,{MKCode:GenMKCode},function (data) {

                        if(data.length>0){
                            return gen (parseInt(result) + 1);
                        } else {
                            return GenMKCode;
                        }

                    });
                }


            });

        } else {
            // alert("12");
        }
    });

    $(document).on('click','#btnEditMK',function () {
        $('#btnAddMK').removeClass('hide');
        $('#btnEditMK').addClass('hide');
        $('.form-mk').prop('disabled',false).css('color','#333');
    });

    function loadDataTableMK() {
        // $('#loadingPage').removeClass('hide');
        // $('#pageMK').addClass('hide');
        loading_page('#loadTableMK');

        setTimeout(function () {
            // $('#loadingPage').addClass('hide');
            // $('#pageMK').removeClass('hide');

            var url = base_url_js+"academic/dataTableMK";
            $.get(url,function (html) {
                $('#loadTableMK').html(html);
            });
        },500);


    }

    function pad(n, width, z) {
        z = z || '0';
        n = n + '';
        return n.length >= width ? n : new Array(width - n.length + 1).join(z) + n;
    }

    function formRequired(element) {

        var elementArr = element.split(',');

        var res;
        for(var i=0;i<elementArr.length;i++){

            var val = $(elementArr[i]).val();
            if(val==''){
                $(''+elementArr[i]).css('border','1px solid red');
                res = false;
                break;
            } else {
                $(''+elementArr[i]).css('border','1px solid #ccc');
                res = true;
            }

        }

        return res;



    }

</script>