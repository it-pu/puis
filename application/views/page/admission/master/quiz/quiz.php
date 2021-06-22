<style>
    #panel-filter .well {
        padding-bottom: 5px;
    }

    #listQuiz li.item-quiz {
        background: #fafafa;
        border: 1px solid #ccc;
        border-radius: 5px;
        padding: 5px 10px 5px 10px;
        margin-bottom: 15px;
        line-height: 1.428571;
        position: relative;
        margin-right: 136px;
    }

    #listQuiz {
        padding-inline-start: 15px;
    }

    #listQuiz .label {
        position: relative;
        left: 0px;
    }

    #listQuiz .well {
        padding: 5px;
        margin-bottom: 5px;
    }

    #listQuiz li.item-quiz a {
        color: #333333;
        text-decoration: none;
    }

    #listQuiz li.item-quiz a:hover {
        color: blue;
        background: lightyellow;
    }

    #listQuiz .btn-remove-quiz {
        font-size: 11px;
        padding: 1px 5px 1px 5px;
    }

    .form-question {
        resize: none;
    }

    input[type=number]::-webkit-outer-spin-button,
    input[type=number]::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    /* Firefox */
    input[type=number] {
        -moz-appearance: textfield;
    }

    .lbl-1 {
        background: #2196F3;
        color: #fff;
        padding: 3px 5px 3px 5px;
        border-radius: .25em;
        font-size: 10px;
    }

    .lbl-2 {
        background: #ce56e2;
        color: #fff;
        padding: 3px 5px 3px 5px;
        border-radius: .25em;
        font-size: 10px;
    }

    .lbl-3 {
        background: #FF9800;
        color: #fff;
        padding: 3px 5px 3px 5px;
        border-radius: .25em;
        font-size: 10px;
    }

    .lbl-point {
        margin-right: 5px;
        font-size: 11px;
        color: #fff;
        padding: 1px 5px 1px 5px;
        border-radius: .25em;
    }

    #showNoteQuiz {
        margin-top: 20px;
        background: #f5f5f5;
        padding: 15px;
        border-radius: 5px;
    }

    #dataTable td:first-child {
        border-right: 1px solid #CCCCCC;
    }

    .panel-act-question-in-quiz {
        position: absolute;
        top: 0px;
        right: -142px;
    }

    .panel-default>.panel-heading .btn-success .badge {
        color: #5cb85c;
        background-color: #fff;
    }

    .alert-essay {
        color: #8a6d3b;
        background-color: #fff6c5;
        border-color: #bd994e;
    }
</style>
<div class="row" style="margin-left: 0px;margin-right:0px;">
    <div class="col-md-12">
        <div id="panel-filter">
            <div class="well">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <select class="form-control" id="filterTA"></select>
                        </div>
                    </div>
                    <div class="col-md-5" id="loadQuizCategory">
                        <div class="form-group">
                            <select class="form-control" id="filterQuizCategory"></select>
                        </div>
                    </div>
                    <div class="col-md-4" id="loadPublishOn">
                        <div class="form-group">
                            <div class="input-group">
                                <select class="form-control" id="filterQuizPublishOn"></select>
                                <span class="input-group-addon btn btn-add-schedule"><i class="icon-plus"></i> Add</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <hr />
    </div>
</div>

<div class="row" style="margin-left: 0px;margin-right:0px;">
    <div class="col-md-6">
        <div style="text-align: center;" id="panelPleaseChoose">
            <img src="<?= url_sign_in_lecturers.'images/icon/select2.jpg'; ?>" style="width: 100%;max-width: 250px;" />
            <h4 style="color: #c24651d9;margin-top: 0px;"><b>Please, choose a TA and a Category and a Publish On</b></h4>
        </div>

        <div id="panel-quiz" class="hide" style="margin-bottom: 100px;">
            <div class="alert alert-info" role="alert">
                <i class="fa fa-info-circle margin-right"></i> Published on <b id="viewPublishOn">-</b>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading clearfix">
                    <h4 class="panel-title pull-left" style="padding-top: 7.5px;">Create / Edit Quiz</h4>
                    <div class="pull-right">
                        <a href="javascript:void(0);" class="btn btn-default btn-sm" id="btnAddQuestionFromMaster"><i class="fa fa-database margin-right"></i> Add Question</a>
                        <a href="javascript:void(0);" class="btn btn-success btn-sm hide" id="btnStudentAnswer">Student Answers <span class="badge">0</span></a>
                    </div>
                </div>
                <div class="panel-body">
                    <textarea id="dataTempQuiz" class="hide"></textarea>
                    <textarea id="dataTempQuizPoint" class="hide"></textarea>
                    <textarea id="dataLoadQuiz" class="hide"></textarea>
                    <div id="loadQuestionListOnQuiz"></div>

                    <div id="showNoteQuiz" class="hide">
                        <div class="form-group">
                            <label>Notes For Students</label>
                            <textarea class="form-control" id="formNotesForStudents" rows="3"></textarea>
                        </div>

                        <div class="form-group">
                            <label>Quiz Duration</label>
                            <div class="input-group" style="max-width: 100px;">
                                <input type="number" class="form-control" min="1" id="formDuration" aria-describedby="basic-formDuration" style="width: 100px;">
                                <span class="input-group-addon" id="basic-addon2"><span id="formDurationView"></span></span>
                            </div>
                            <p class="help-block">Enter in minutes | maximum quiz time is 3 hours</p>
                        </div>
                    </div>


                </div>
                <div class="panel-footer hide" style="text-align: right;">
                    Total Point : <span id="viewPoint" style="margin-right: 7px;">0</span> <button class="btn btn-success" disabled id="btnSaveQuiz">Save</button>
                </div>
            </div>
        </div>

    </div>
    <div class="col-md-6">
        <div class="panel panel-default">
            <div class="panel-heading clearfix">
                <h4 class="panel-title pull-left" style="padding-top: 7.5px;">My Question</h4>
                <div class="pull-right">
                    <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Make a Question <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu" style="left: -57px;" id="btnCreateQuestion"></ul>
                    </div>
                    <button class="btn btn-sm btn-default" id="btnReloadMyQuestion">Reload My Question</button>
                </div>
            </div>
            <div class="panel-body" style="min-height: 200px;">
                <div id="divTableMyQuestion"></div>
            </div>
        </div>
    </div>
</div>


<script type="text/javascript">

    const module_url = "<?php echo $module_url ?>";
    
    const load_default = async() => {
        loadingStart();
        ([ await LoadFilterTA(),await LoadFilterCategory(), await LoadFilterPublishOn()]);
        loadingEnd(500);
    }

    const LoadFilterTA = async() => {
        const url = module_url+'filter_ta';
        const response = await AjaxSubmitFormPromises(url);
        $('#filterTA').empty();
        $('#filterTA').append('<option disabled value = "0" selected>'+'Choose TA'+'</option>');
        for (var i = 0; i < response.length; i++) {
            $('#filterTA').append('<option value = "'+response[i]+'">'+response[i]+'</option>');
        }
    }

    const LoadFilterCategory = async() => {
        const url = module_url+'filterCategory';
        const response = await AjaxSubmitFormPromises(url);
        $('#filterQuizCategory').empty();
        $('#filterQuizCategory').append('<option disabled value = "0" selected>'+'Choose Category'+'</option>');
        for (var i = 0; i < response.length; i++) {
            $('#filterQuizCategory').append('<option value = "'+response[i].Type+'">'+response[i].Type+'</option>');
        }
    }

    const LoadFilterPublishOn = async() => {
        let TA = $('#filterTA').val();
        const url = module_url+'filterQuizSchedule/'+TA;
        const response = await AjaxSubmitFormPromises(url);
        $('#filterQuizPublishOn').empty();
        $('#filterQuizPublishOn').append('<option disabled value = "0" selected>'+'Choose Publish On'+'</option>');
        for (var i = 0; i < response.length; i++) {
            $('#filterQuizPublishOn').append('<option value = "'+response[i].ID+'">'+moment(response[i].DateStart).format('DD MMM YYYY')+' - '+moment(response[i].DateEnd).format('DD MMM YYYY')+'</option>');
        }
    }

    $(document).on('click','.btn-add-schedule',function(e){
        let TA = $('#filterTA').val();

        if (TA !== undefined && TA != '' && TA != 0 && TA != null) {
            let htmlss = '<div class = "row contenModal" data-ta = "'+TA+'">'+
                            '<div class = "col-md-6">'+
                                '<label>Start</label>'+
                                '<div id="datetimepickerStart" class="input-group input-append date datetimepicker">'+
                                    '<input data-format="yyyy-MM-dd" class="form-control" id="tglStart" type=" text" readonly="" value = "<?php echo date('Y-m-d') ?>">'+
                                    '<span class="input-group-addon add-on"><i data-time-icon="icon-time" data-date-icon="icon-calendar" class="icon-calendar"></i></span>'+
                                '</div>'+
                            '</div>'+
                            '<div class = "col-md-6">'+
                                '<label>End</label>'+
                                '<div id="datetimepickerEND" class="input-group input-append date datetimepicker">'+
                                    '<input data-format="yyyy-MM-dd" class="form-control" id="tglEND" type=" text" readonly="" value = "<?php echo date('Y-m-d') ?>">'+
                                    '<span class="input-group-addon add-on"><i data-time-icon="icon-time" data-date-icon="icon-calendar" class="icon-calendar"></i></span>'+
                                '</div>'+
                            '</div>'+
                         '</div>';


            $('#GlobalModal .modal-header').html('<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
                '<h4 class="modal-title">'+'Add schedule quiz'+'</h4>');
            $('#GlobalModal .modal-body').html(htmlss);

            $('#GlobalModal .modal-footer').html('' +
                '<button type="button" class="btn btn-success" id="btnsaveSchedule">Submit</button> ' +
                '<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>' +
                '');

            $('#GlobalModal').modal({
                'show' : true,
                'backdrop' : 'static'
            });

            $('#datetimepickerStart').datetimepicker({
              format: 'yyyy-MM-dd',autoclose: true, minView: 2,pickTime: false,
            });

            $('#datetimepickerEND').datetimepicker({
              format: 'yyyy-MM-dd',autoclose: true, minView: 2,pickTime: false,
            });
        }
        else
        {
            toastr.info('please choose TA');
        }
    })

    const saveSchedule = async(itsme) => {
        if (confirm('Are you sure ?')) {
            let data = {
                TA : $('#filterTA').val(),
                DateStart : $('#tglStart').val(),
                DateEnd : $('#tglEND').val()
            };

            var token = jwt_encode(data,'UAP)(*');
            const url = module_url + 'save_schedule';
            loading_button2(itsme);
            try{
                const response = await AjaxSubmitFormPromises(url,token);
                if (response.status == 1) {
                    await LoadFilterPublishOn();
                }
                else
                {
                    toastr.info(response.msg);
                }
            }
            catch(err){
                console.log(err);
                toastr.info('something wrong');
            }

             end_loading_button2(itsme);

             $('#GlobalModal').modal('hide');
        }

    }

    $(document).on('click','#btnsaveSchedule',function(e){
        const itsme = $(this);
        saveSchedule(itsme);
    })

    $(document).on('change','#filterTA',function(e){
        LoadFilterPublishOn();
    })

    const timeConvert = (n) => {
        var num = n;
        var hours = (num / 60);
        var rhours = Math.floor(hours);
        var minutes = (hours - rhours) * 60;
        var rminutes = Math.round(minutes);

        var result =  '-';
        if(parseFloat(rhours)<=0 && parseFloat(rminutes)>0){
            result =  rminutes + " minute(s)"
        } else if(parseFloat(rhours)>0 && parseFloat(rminutes)<=0){
            result =  rhours + " hour(s)";
        }  else if(parseFloat(rhours)>0 && parseFloat(rminutes)>0) {
            result =  rhours + " hour(s) and " + rminutes + " minute(s)";
        }

        return result;
    }

    const LoadQuiz = async() => {
        $('#viewPublishOn').html($('#filterQuizPublishOn option:selected').text());
        $('#panelPleaseChoose').addClass('hide');
        $('#panel-quiz').removeClass('hide');
        $('#formNotesForStudents').val('');
        $('#formDuration').val(1);
        $('#formDurationView').html('= ' + timeConvert(1));
    }

    $(document).on('change','#filterQuizPublishOn',function(e){
        LoadQuiz();
    })
    
    $(document).ready(function(e){
        load_default();
    })
</script>
