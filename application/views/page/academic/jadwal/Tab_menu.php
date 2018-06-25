
<style>
    .tab-right {
        float: right !important;
    }

    .toggle-group .btn-default {
        z-index: 1000 !important;
    }
    .toggle.ios, .toggle-on.ios, .toggle-off.ios { border-radius: 20px; }
    .toggle.ios .toggle-handle { border-radius: 20px; }
</style>


<div class="row" style="margin-top: 30px;">

    <div class="col-md-4">
        <div class="">
            <label>Semester Antara</label>
            <input type="checkbox" id="formSemesterAntara" data-toggle="toggle" data-style="ios"/>
        </div>
    </div>
    <div class="col-md-8" style="text-align: right;">
        <div class="btn-group" role="group" aria-label="...">

            <button data-page="jadwal" type="button" class="btn btn-primary btn-action
                        control-jadwal"><i class="fa fa-calendar right-margin" aria-hidden="true"></i> Schedule</button>

            <button data-page="ruangan" type="button" class="btn btn-default btn-default-primary btn-action
                        control-jadwal"><i class="fa fa-window-restore right-margin" aria-hidden="true"></i> Room Schedule</button>
        </div>
        |
        <button data-page="penawaran_mk" type="button" class="btn btn-default btn-default-primary btn-action control-jadwal">
            <i class="fa fa-exchange right-margin" aria-hidden="true"></i> Course Offerings
        </button>
        <button data-page="inputjadwal" type="button" class="btn btn-default btn-default-primary btn-action control-jadwal">
            <i class="fa fa-pencil right-margin" aria-hidden="true"></i> Set Schedule
        </button>
    </div>

</div>


<div class="row">
    <div class="col-md-12">
        <hr/>
        <div id="dataPage"></div>
    </div>
</div>


<script>
    $(document).ready(function () {
        loadPage('jadwal','');
        $('input[type=checkbox][data-toggle=toggle]').bootstrapToggle();

        checkSemesterAntara();
        window.SemesterAntara = 0;
        window.PageNow = 'jadwal';
        window.PageScdNow = '';
    });

    $('#formSemesterAntara').change(function () {

        if($('#formSemesterAntara').is(':checked')){
            SemesterAntara = 1;
        } else {
            SemesterAntara = 0;
        }

        if(PageNow=='inputjadwal'){
            if(SemesterAntara==0){
                loadAcademicYearOnPublish('');
            } else {
                loadAcademicYearOnPublish('SemesterAntara');
            }
            resetFormSetSchedule();
        } else if(PageNow=='penawaran_mk'){
            resetPenawaranMK();
        } else if(PageNow=='jadwal'){
            loadPage('jadwal','');
        }


        
        // Reset Penawaran MK
    });
    
    function resetPenawaranMK() {
        $('#formSemester').val('');
        $('#box1View,#box1Storage,#box2View,#box2Storage').empty();
        $('#OfferingDiv').addClass('hide');
        $('#btnAnother').html('');
        $('#dataOfferings').empty();
    }

    $(document).on('click','.btn-action',function () {
        var page = $(this).attr('data-page');
        var ScheduleID = (page=='editjadwal') ? $(this).attr('data-id') : '';
        PageNow = page;
        PageScdNow = ScheduleID;

        if(page!='editjadwal'){
            $('.btn-action').removeClass('btn-primary');
            $('.btn-action').addClass('btn-default btn-default-primary');

            $('button[data-page='+page+']').removeClass('btn-default btn-default-primary');
            $('button[data-page='+page+']').addClass('btn-primary');
        }



        loadPage(page,ScheduleID);
    });

    function checkSemesterAntara() {
        var url = base_url_js+'api/__crudTahunAkademik';
        var token = jwt_encode({action:'checkSemesterAntara'},'UAP)(*');
        $.post(url,{token:token},function (jsonResult) {
            if(jsonResult.length>0){
                $('#formSemesterAntara').prop('disabled',false);
            } else {
                $('#formSemesterAntara').prop('disabled',true);
            }
        });
    }

    function loadPage(page,ScheduleID) {
        loading_page('#dataPage');
        var data = {
            page : page,
            ScheduleID : ScheduleID
        };

        var token = jwt_encode(data,"UAP)(*");
        var url = base_url_js+'academic/__setPageJadwal';
        $.post(url,{token:token},function (page) {
            setTimeout(function () {
                $('#dataPage').html(page);
            },500);
        });
    }
</script>

<script>
    // Untuk Page Jadwal
    $(document).on('change','#filterSemester',function () {
        var Semester = $('#filterSemester').val();
        var SemesterID = (Semester!='' && Semester!= null) ? Semester.split('.')[0] : '';

        $('#selectSemesterSc').html('<select class="form-control" id="filterSemesterSchedule"></select>');
        // $('#filterSemesterSchedule').empty();
        $('#filterSemesterSchedule').append('<option value="" disabled selected>-- Semester --</option>' +
            '                <option disabled>------------</option>');
        loadSelectOPtionAllSemester('#filterSemesterSchedule','',SemesterID,SemesterAntara);
        // filterSchedule();

    });

    $(document).on('change','#filterProgramCampus,#filterBaseProdi,#filterCombine,#filterSemesterSchedule',function () {
        filterSchedule();
    });

    $('input[type=checkbox][class=filterDay]').change(function () {
        var v = $(this).val();

        // console.log($('input[type=checkbox][class=filterDay]:checked').val());

        if(v==0){
            $('input[type=checkbox][class=filterDay]').prop('checked',false);
            $(this).prop('checked',true);
            checkedDay = [];
        } else {

            if($('input[type=checkbox][value='+v+']').is(':checked')){
                checkedDay.push($(this).val());
            } else {
                checkedDay = $.grep(checkedDay, function(value) {
                    return value != v;
                });
            }


            $('input[type=checkbox][value=0]').prop('checked',false);
            // $(this).prop('checked',true);
        }

        if(checkedDay.length==0){
            $('input[type=checkbox][value=0]').prop('checked',true);
            $('.widget-schedule').removeClass('hide');
        } else {
            $('.widget-schedule').addClass('hide');
            if(checkedDay.length>0){
                for(var i=0;i<checkedDay.length;i++){
                    $('#dayWidget'+checkedDay[i]).removeClass('hide');
                }
            }
        }



    });
</script>

<!--Penawaran MK-->
<script>
    $(document).on('click','.btnSmtAnother-cl',function () {

        var tg = $(this).attr('data-tg');

        var id = $(this).attr('data-id');
        var dataCourse = $('#dataMK'+id).val();

        var dataJSON = JSON.parse(dataCourse);

        if(tg==1){
            $(this).addClass('btn-default btn-default-warning');
            $(this).removeClass('btn-warning');
            $(this).attr('data-tg',0);

            for(var i=0;i<dataJSON.DetailSemester.length;i++){
                var Courses = dataJSON.DetailSemester[i];


                $('#box1View option[value='+Courses.CDID+']').remove();
                $('#box2View option[value='+Courses.CDID+']').remove();
            }

        } else {
            $(this).removeClass('btn-default btn-default-warning');
            $(this).addClass('btn-warning');
            $(this).attr('data-tg',1);

            for(var i=0;i<dataJSON.DetailSemester.length;i++){
                var Courses = dataJSON.DetailSemester[i];

                if(Courses.Offering==false){
                    var color = (Courses.StatusMK==1) ? '#ff9800' : 'red';
                    var status = (Courses.StatusMK==1) ? '' : 'disabled';

                    $('#box1View').append('<option value="'+Courses.CDID+'" style="color: '+color+';" '+status+'>Smt '+Courses.Semester+' - '+Courses.MKCode+' | '+Courses.NameMKEng+' (Credit : '+Courses.TotalSKS+')</option>');
                }


            }

        }


    });
</script>

<!-- Input Jadwal -->
<script>

    $(document).on('change','.fm-prodi',function () {
        var divNum = $(this).attr('data-id');
        var Prodi = $(this).val();
        if(Prodi!=''){
            var ProdiID = Prodi.split('.')[0];
            getCourseOfferings(ProdiID,divNum);
            if(dataProdi==1){
                setGroupClass();
            }
        }

    });
    $(document).on('change','#formMataKuliah1',function () {
        var data = $(this).val();
        if(data!=null && data!=''){
            // data.split('|');
            // console.log(data);
            // console.log(data.split('|')[2]);
            $('#textTotalSKSMK').val(data.split('|')[2]);

            // var cr = $('#formCredit1').val();
            if(dataSesi==1){
                $('#formCredit1').val(data.split('|')[2]);
            }
        }
    });

    $(document).on('change','#formParalel',function () {
        if(!$(this).is(':checked')){
            $('#ClassgroupParalel').empty();
        }
        setGroupClass();
    });
    $(document).on('change','#ClassgroupParalel',function () {
        setGroupClass();
    });

    // Onchange Cek kelas Bentrok
    $(document).on('change','.form-classroom,.form-day',function () {
        var ID = $(this).attr('data-id');
        checkSchedule(ID);
    });

    $(document).on('keyup','.form-sesiawal,.form-credit',function () {
        var ID = $(this).attr('data-id');
        setSesiAkhir(ID);
        checkSchedule(ID);
    });

    $(document).on('change','.form-sesiawal,.form-timepercredit,.form-credit',function () {
        var ID = $(this).attr('data-id');
        setSesiAkhir(ID);
        checkSchedule(ID);

    });

    $(document).on('change','input[type=radio][fm=dtt-form]',function () {
        loadformTeamTeaching($(this).val(),'#formTeamTeaching');
    });

    // Untuk Add Class Room
    $(document).on('click','#btnSaveClassroom',function () {

        var process = true;

        var Room = $('#formRoom').val(); process = (Room=='') ? errorInput('#formRoom') : true ;
        var Seat = $('#formSeat').val(); var processSeat = (Seat!='' && $.isNumeric(Seat) && Math.floor(Seat)==Seat) ? true : errorInput('#formSeat') ;
        var SeatForExam = $('#formSeatForExam').val(); var processSeatForExam = (SeatForExam!='' && $.isNumeric(SeatForExam) && Math.floor(SeatForExam)==SeatForExam) ? true : errorInput('#formSeatForExam') ;


        if(Room!='' && processSeat && processSeatForExam){
            $('#formRoom,#formSeat,#formSeatForExam,#btnCloseClassroom').prop('disabled',true);
            loading_button('#btnSaveClassroom');
            loading_page('#viewClassroom');

            var data = {
                action : 'add',
                ID : '',
                formData : {
                    Room : Room,
                    Seat : Seat,
                    SeatForExam : SeatForExam,
                    Status : 0,
                    UpdateBy : sessionNIP,
                    UpdateAt : dateTimeNow()
                }
            };

            var token = jwt_encode(data,'UAP)(*');
            var url = base_url_js+"api/__crudClassroom";

            $.post(url,{token:token},function (data_result) {

                for(var i=1;i<=parseInt(dataSesi);i++){
                    var selected = $('#formClassroom'+i).val();
                    loadSelectOptionClassroom('#formClassroom'+i,selected);
                }

                setTimeout(function () {

                    if(data_result.inserID!=0) {
                        toastr.success('Data tersimpan','Success!');
                        $('#GlobalModal').modal('hide');

                    } else {
                        $('#formRoom,#formSeat,#formSeatForExam,#btnCloseClassroom').prop('disabled',false);
                        $('#btnSaveClassroom').prop('disabled',false).html('Save');
                        toastr.warning('Room is exist','Warning');
                    }
                },1000);

            });
        } else {
            toastr.error('Form Required','Error!');
        }
    });
</script>

<!-- Input Jadwal // Crud Time Per Credit -->
<script>
    $(document).on('click','#btnAddTimePerCredit',function () {
        var Time = $('#formTime').val();

        if(Time!=''){
            $('#formTime').prop('disabled',true);
            loading_buttonSm('#btnAddTimePerCredit');
            var url = base_url_js+'api/__crudTimePerCredit';
            var data = {
                action : 'add',
                formData : {
                    Time : Time,
                    UpdateBy : sessionNIP,
                    UpdateAt : dateTimeNow()
                }
            };
            var token = jwt_encode(data,'UAP)(*');
            $.post(url,{token:token},function (json_result) {
                $('#formTime,#btnAddTimePerCredit').prop('disabled',false);
                $('#btnAddTimePerCredit').html('<i class="fa fa-plus-circle" aria-hidden="true"></i> Add');

                setTimeout(function () {
                    if(json_result.inserID==0){
                        toastr.warning('Data Exist','Warning!');
                    } else {

                        for(var d=1;d<=parseInt(dataSesi);d++){
                            var selected = $('#formTimePerCredit'+d).val();
                            loadSelectOptionTimePerCredit('#formTimePerCredit'+d,selected);
                        }


                        $('#formTime').val('');
                        $('#rowTime').append('<tr id="tr'+json_result.inserID+'">' +
                            '<td class="td-center">'+Time+' Minute</td>' +
                            '<td class="td-center">' +
                            '<button class="btn btn-default btn-default-danger" data-id="'+json_result.inserID+'">Delete</button>' +
                            '</td>' +
                            '</tr>');
                        toastr.success('Data Saved','Success!');
                    }
                },1000);
            });

        } else {
            $('#formTime').css('border','1px solid red');
            setTimeout(function () {
                $('#formTime').css('border','1px solid #ccc');
            },5000);
        }
    });
    $(document).on('click','.btn-delete-timepercredit',function () {
        var ID = $(this).attr('data-id');
        var token = jwt_encode({action:'delete',ID:ID},'UAP)(*');
        var url = base_url_js+'api/__crudTimePerCredit';

        $.post(url,{token:token},function (json_result) {
            if(json_result.inserID==0){
                toastr.warning('Data tidak dapat di hapus','Warning!');
            } else {
                for(var d=1;d<=parseInt(dataSesi);d++){
                    var selected = $('#formTimePerCredit'+d).val();
                    loadSelectOptionTimePerCredit('#formTimePerCredit'+d,selected);
                }
                $('#tr'+ID).remove();
                toastr.success('Data deleted','Success!');
            }
        });

    })
</script>