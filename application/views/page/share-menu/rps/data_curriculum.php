

<!-- <div class="row" style="margin-top: 30px;">
    <div class="col-md-12" style="text-align: right;">
      <a href="<?= base_url('academic/curriculum_cross/2014/1') ?>" style="float:left;" target="_blank" class="btn btn-default"><b>Check Curriculum Cross</b></a>


        <div class="btn-group">
            <button class="btn btn-success dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                Add Curriculum
                <span class="caret"></span>
            </button>
            <ul class="dropdown-menu" id="yearAddKurikulum">
            </ul>
        </div>

        <div class="btn-group">
            <button class="btn btn-default btn-default-success dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                Add Semester
                <span class="caret"></span>
            </button>
            <ul class="dropdown-menu" id="addSmt">
            </ul>
        </div>

        <div class="btn-group">
            <button class="btn btn-info dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                <i class="fa fa-cog" aria-hidden="true"></i>
                <span class="caret"></span>
            </button>
            <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                <li><a href="javascript:void(0)" data-action="ConfProgram" data-header="Campus Programme" class="btn-conf">Campus Programme</a></li>
                <li><a href="javascript:void(0)" data-action="ConfJenisKurikulum" data-header="Curriculum Type" class="btn-conf">Curriculum Type</a></li>
                <li><a href="javascript:void(0)" data-action="ConfJenisKelompok" data-header="Course Group" class="btn-conf">Course Group</a></li>
            </ul>
        </div>

        <hr/>
    </div>
</div> -->

<div class="row" style="">

    <div class="col-xs-6 col-md-offset-3">
        <div class="well">
            <div class="row">
                <div class="col-md-5">
                    <select class="form-control" id="selectKurikulum"></select>
                </div>
                <div class="col-md-5">
                    <select class="form-control" id="selectProdi"></select>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-success" id="btnSubmitSearch">Submit</button>
                </div>
            </div>
        </div>
    </div>

</div>




<div class="row">
    <div class="col-md-12">
        <div id="pageKurikulum"></div>
    </div>
</div>

<script>
    $(document).ready(function () {
        loadSelectOptionCurriculum('#selectKurikulum','');
        loadSelectOptionBaseProdi('#selectProdi','');
        loaddataAddKurikulum();
        $('.btn-addsmt').prop('disabled',true);

        var loadDataFirst = setInterval(function () {
            var selectKurikulum = $('#selectKurikulum').val();
            var selectProdi = $('#selectProdi').val();
            if(selectKurikulum!='' && selectKurikulum!=null && selectProdi!='' && selectProdi!=null){
                pageKurikulum();
                clearInterval(loadDataFirst);
            }
        },1000);
    });

    $(document).on('click','#btnSubmitSearch',function () {
        pageKurikulum();
    });

    $(document).on('click','.btn-add-mksmt', function () {
       var semester = $(this).attr('data-smt');
       modal_add_mk(semester,'add');
    });

    $(document).on('click','.btn-conf',function () {
        var action = $(this).attr('data-action');
        var header = $(this).attr('data-header');
        if(action == 'ConfJenisKurikulum' || action == 'ConfJenisKelompok' || action=='ConfProgram'){
            modal_dataConf(action,header);
        }
        // else if(action=='ClassGroup'){
        //     modal_dataClassGroup(action,header);
        // }
    });

    $(document).on('click','.btn-control',function () {

        var action = $(this).attr('data-action');
        if(action=='add-kurikulum') {
            var year = $(this).attr('data-year');
            modal_add_kurikulum(year);
        } else if(action=='add-semester'){
            var semester = $(this).attr('data-smt');
            modal_add_mk(semester,'add');
        }


    });

    $(document).on('click','.btnAddRPS',function () {
        var semester = $(this).attr('data-smt');
        var CDID = $(this).attr('data-id');
        modal_add(semester,'addRPS',CDID);
    });

    // function modal_add_RPS(semester,action,ID='') {
    //     var url = base_url_js+"rps/loadPageModal";
    //     var curriculum = $('#selectKurikulum').find(':selected').val().split('.');
    //     var curriculumYear = curriculum[1];
    //     var data = {
    //         Action : action,
    //         CDID : ID,
    //         Semester : semester,
    //         curriculumYear : curriculumYear
    //     };
    //     var token = jwt_encode(data,"UAP)(*");
    //     $.post(url,{ token:token }, function (html) {
    //         $('#GlobalModal .modal-header').html('<h4 class="modal-title">Add Rencana Pembelajaran Semester (RPS)</h4>');
    //         $('#GlobalModal .modal-body').html(html);
    //         $('#GlobalModal .modal-footer').html(' ');
    //         $('#GlobalModal').modal({
    //             'show' : true,
    //             'backdrop' : 'static'
    //         });
    //     })
    // }

    $(document).on('click','.btnManageCPL',function () {
        var semester = $(this).attr('data-smt');
        var CDID = $(this).attr('data-id');
        var MKCode = $(this).attr('data-mkcode');
        var prodi = $(this).attr('data-prodi');
        var course = $(this).attr('data-course');
        var url = base_url_js+"rps/loadPageModal";
        var curriculum = $('#selectKurikulum').find(':selected').val().split('.');
        var curriculumYear = curriculum[1];
        var data = {
            CDID : CDID,
            MKCode : MKCode,
            Prodi : prodi,
            Course : course,
            Semester : semester,
            curriculumYear : curriculumYear
        };
        var token = jwt_encode(data,"UAP)(*");
        window.location.assign("<?php echo base_url('rps/manage-CPL/'); ?>"+ token);
    });

    $(document).on('click','.btnViewCPMK',function () {
        var CDID = $(this).attr('data-id');
        var semester = $(this).attr('data-smt');
        var MKCode = $(this).attr('data-mkcode');
        var prodi = $(this).attr('data-prodi');
        var course = $(this).attr('data-course');
        var url = base_url_js+"rps/loadPageModal";
        var curriculum = $('#selectKurikulum').find(':selected').val().split('.');
        var curriculumYear = curriculum[1];
        var data = {
            CDID : CDID,
            MKCode : MKCode,
            Prodi : prodi,
            Course : course,
            Semester : semester,
            curriculumYear : curriculumYear
        };
        var token = jwt_encode(data,"UAP)(*");
        window.location.assign("<?php echo base_url('rps/list-CPMK/'); ?>"+ token);
    });

    $(document).on('click','.btnViewDescMK',function () {
        var CDID = $(this).attr('data-id');
        var semester = $(this).attr('data-smt');
        var MKCode = $(this).attr('data-mkcode');
        var prodi = $(this).attr('data-prodi');
        var course = $(this).attr('data-course');
        var url = base_url_js+"rps/loadPageModal";
        var curriculum = $('#selectKurikulum').find(':selected').val().split('.');
        var curriculumYear = curriculum[1];
        var data = {
            CDID : CDID,
            MKCode : MKCode,
            Prodi : prodi,
            Course : course,
            Semester : semester,
            curriculumYear : curriculumYear
        };
        var token = jwt_encode(data,"UAP)(*");
        window.location.assign("<?php echo base_url('rps/desc-MK/'); ?>"+ token);
    });

    $(document).on('click','.btnViewMaterial',function () {
        var CDID = $(this).attr('data-id');
        var semester = $(this).attr('data-smt');
        var MKCode = $(this).attr('data-mkcode');
        var prodi = $(this).attr('data-prodi');
        var course = $(this).attr('data-course');
        var url = base_url_js+"rps/loadPageModal";
        var curriculum = $('#selectKurikulum').find(':selected').val().split('.');
        var curriculumYear = curriculum[1];
        var data = {
            CDID : CDID,
            MKCode : MKCode,
            Prodi : prodi,
            Course : course,
            Semester : semester,
            curriculumYear : curriculumYear
        };
        var token = jwt_encode(data,"UAP)(*");
        window.location.assign("<?php echo base_url('rps/bahan-kajian/'); ?>"+ token);
    });

    $(document).on('click','.btnViewRPS',function () {
        var CDID = $(this).attr('data-id');
        var semester = $(this).attr('data-smt');
        var MKCode = $(this).attr('data-mkcode');
        var prodi = $(this).attr('data-prodi');
        var course = $(this).attr('data-course');
        var url = base_url_js+"rps/loadPageModal";
        var curriculum = $('#selectKurikulum').find(':selected').val().split('.');
        var curriculumYear = curriculum[1];
        var data = {
            CDID : CDID,
            MKCode : MKCode,
            Prodi : prodi,
            Course : course,
            Semester : semester,
            curriculumYear : curriculumYear
        };
        var token = jwt_encode(data,"UAP)(*");
        window.location.assign("<?php echo base_url('rps/list-rps/'); ?>"+ token);
    });

    $(document).on('click','.btnAddCPMK',function () {
        var semester = $(this).attr('data-smt');
        var CDID = $(this).attr('data-id');
        modal_add(semester,'addCPMK',CDID);
    });

    $(document).on('click','.btnAddDescMK',function () {
        var semester = $(this).attr('data-smt');
        var CDID = $(this).attr('data-id');
        modal_add(semester,'addDescMK',CDID);
    });

    $(document).on('click','.btnAddMaterial',function () {
        var semester = $(this).attr('data-smt');
        var CDID = $(this).attr('data-id');
        modal_add(semester,'addMaterial',CDID);
    });

    $(document).on('click','.btnViewCPL',function () {
        console.log('testview')
    });

    function modal_add(semester,action,ID='') {
        var url = base_url_js+"rps/loadPageModal";
        var curriculum = $('#selectKurikulum').find(':selected').val().split('.');
        var curriculumYear = curriculum[1];
        var data = {
            Action : action,
            CDID : ID,
            Semester : semester,
            curriculumYear : curriculumYear
        };
        var token = jwt_encode(data,"UAP)(*");
        if (action === 'addRPS') {
            $.post(url,{ token:token }, function (html) {
                $('#GlobalModal .modal-header').html('<h4 class="modal-title">Add Rencana Pembelajaran Semester (RPS)</h4>');
                $('#GlobalModal .modal-body').html(html);
                $('#GlobalModal .modal-footer').html(' ');
                $('#GlobalModal').modal({
                    'show' : true,
                    'backdrop' : 'static'
                });
            })
        } else if (action === 'addCPMK') {
            $.post(url,{ token:token }, function (html) {
                $('#GlobalModal .modal-header').html('<h4 class="modal-title">Add Capaian Pembelajaran Mata Kuliah (CPMK)</h4>');
                $('#GlobalModal .modal-body').html(html);
                $('#GlobalModal .modal-footer').html(' ');
                $('#GlobalModal').modal({
                    'show' : true,
                    'backdrop' : 'static'
                });
            })
        } else if (action === 'addDescMK') {
            $.post(url,{ token:token }, function (html) {
                $('#GlobalModal .modal-header').html('<h4 class="modal-title">Add Deskripsi MK</h4>');
                $('#GlobalModal .modal-body').html(html);
                $('#GlobalModal .modal-footer').html(' ');
                $('#GlobalModal').modal({
                    'show' : true,
                    'backdrop' : 'static'
                });
            })
        } else if (action === 'addMaterial') {
            $.post(url,{ token:token }, function (html) {
                $('#GlobalModal .modal-header').html('<h4 class="modal-title">Add Bahan Kajian</h4>');
                $('#GlobalModal .modal-body').html(html);
                $('#GlobalModal .modal-footer').html(' ');
                $('#GlobalModal').modal({
                    'show' : true,
                    'backdrop' : 'static'
                });
            })
        }
        
    }

    $(document).on('click','.detailMataKuliah',function () {
        var semester = $(this).attr('data-smt');
        var CDID = $(this).attr('data-id');
        modal_add_mk(semester,'edit',CDID);
    });

    function pageKurikulum() {


        var selectKurikulum = $('#selectKurikulum').val();
        var selectProdi = $('#selectProdi').val();
        if(selectKurikulum!='' && selectKurikulum!=null && selectProdi!='' && selectProdi!=null){
            $('.btn-addsmt').prop('disabled',true);

            var kurikulum = $('#selectKurikulum').find(':selected').val().split('.');
            var year = kurikulum[1].trim();
            var prodi = $('#selectProdi').find(':selected').val().split('.');
            var prodiID = prodi[0];
            loading_page('#pageKurikulum');
            loading_modal_show();
            var url = base_url_js+'rps/curriculum-detail';
            var data = {
                SemesterSearch : '',
                year : year,
                ProdiID : prodiID
            };

            var token = jwt_encode(data,"UAP)(*");
            $.post(url,{token:token},function (page) {
                setTimeout(function () {
                    $('#pageKurikulum').html(page);
                },500);
            });
        }

    }

    function loaddataAddKurikulum() {
        for(var i=0;i<2;i++){
            $('#yearAddKurikulum').append('<li>' +
                '<a href="javascript:void(0)" data-year="'+moment().add(i,'years').year()+'" data-action="add-kurikulum" class="btn-control">' +
                'Curriculum '+moment().add(i,'years').year()+'' +
                '</a></li>');
        }
    }
    function modal_add_kurikulum(year) {
        var url = base_url_js+"academic/kurikulum/add-kurikulum";
        var data = {
            Year : year,
            Name : 'Kurikulum '+year,
            NameEng : 'Curriculum '+year,
            CreateAt : dateTimeNow(),
            CreateBy : '2017090',
            UpdateAt : dateTimeNow(),
            UpdateBy : '2017090'
        };
        var token = jwt_encode(data,"UAP)(*");
        $.post(url,{token:token},function (html) {
            $('#GlobalModal .modal-header').html('<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
                '<h4 class="modal-title">Add Curriculum</h4>');
            $('#GlobalModal .modal-body').html(html);
            $('#GlobalModal .modal-footer').html(' ');
            $('#GlobalModal').modal({
                'show' : true,
                'backdrop' : 'static'
            });
        })
    }
    function modal_add_mk(semester,action,ID='') {
        var url = base_url_js+"academic/kurikulum/loadPageDetailMataKuliah";
        var curriculum = $('#selectKurikulum').find(':selected').val().split('.');
        var curriculumYear = curriculum[1];
        var data = {
            Action : action,
            CDID : ID,
            Semester : semester,
            curriculumYear : curriculumYear
        };
        var token = jwt_encode(data,"UAP)(*");
        $.post(url,{ token:token }, function (html) {
            $('#GlobalModal .modal-header').html('<h4 class="modal-title">Add MK Semester '+semester+' - Curriculum '+curriculumYear+'</h4>');
            $('#GlobalModal .modal-body').html(html);
            $('#GlobalModal .modal-footer').html(' ');
            $('#GlobalModal').modal({
                'show' : true,
                'backdrop' : 'static'
            });
        })
    }
    function modal_dataConf(action,header) {
        var url = base_url_js+'academic/kurikulum/data-conf';

       var data = {
            action : action
        };

        var token = jwt_encode(data,'UAP)(*');
        $.post(url,{token:token}, function (html) {
            $('#GlobalModal .modal-header').html('<button type="button" class="close" data-dismiss="modal" aria-label="Close">' +
                '<span aria-hidden="true">&times;</span></button>' +
                '<h4 class="modal-title">'+header+'</h4>');
            $('#GlobalModal .modal-body').html(html);
            $('#GlobalModal .modal-footer').html(' ');
            $('#GlobalModal').modal({
                'show' : true,
                'backdrop' : 'static'
            });
        });
    }

</script>
