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
                        <!-- <a href="javascript:void(0);" class="btn btn-default btn-sm" id="btnAddQuestionFromMaster"><i class="fa fa-database margin-right"></i> Add Question</a> -->
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
                            <label>DurationType</label>
                            <div> 
                                    <label class="radio-inline"><input type="radio" name="optradio" class="DurationType" value="Flexi">Flexi</label>
                                    <label class="radio-inline"><input type="radio" name="optradio" class="DurationType" value="Fixed">Fixed</label>
                            </div> 
                        </div>

                        <div class="form-group DurationFlexiDiv hide">
                            <label>Quiz Duration Flexi</label>
                            <div class="input-group" style="max-width: 100px;">
                                <input type="number" class="form-control" min="1" id="formDuration" aria-describedby="basic-formDuration" style="width: 100px;">
                                <span class="input-group-addon" id="basic-addon2"><span id="formDurationView"></span></span>
                            </div>
                            <p class="help-block">Enter in minutes | maximum quiz time is 3 hours</p>
                        </div>
                        <div class="form-group DurationFixedDiv hide">
                            <label>Quiz Duration Fixed</label>
                            <div class="row">
                                <div class="col-md-6">
                                    <div id="div_formSesiAwal" data-no="1" class="input-group">
                                        <input data-format="hh:mm" type="text" id="DurationFixedStart" class="form-control form-attd formtime" value="00:00" readonly />
                                        <span class="add-on input-group-addon">
                                            <i data-time-icon="icon-time" data-date-icon="icon-calendar"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div id="div_formSesiAkhir" data-no="1" class="input-group">
                                        <input data-format="hh:mm" type="text" id="DurationFixedEnd" class="form-control form-attd formtime" value="00:00" readonly />
                                        <span class="add-on input-group-addon">
                                            <i data-time-icon="icon-time" data-date-icon="icon-calendar"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
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
    const module_url_question = "<?php echo $module_url_question ?>";

    $(document).on('click','#btnReloadMyQuestion',function(e){
        loadMyQuestion();
    })
    
    const load_default = async() => {
        loadingStart();
        loadQuestionType();
        loadMyQuestion();
        ([ await LoadFilterTA(),await LoadFilterCategory(), await LoadFilterPublishOn()]);
        
        $('#div_formSesiAwal').datetimepicker({
            pickDate: false,
            pickSeconds : false
        })

        $('#div_formSesiAkhir').datetimepicker({
            pickDate: false,
            pickSeconds : false
        })

        loadingEnd(500);
    }

    const loadMyQuestion = () => {
        $('#divTableMyQuestion').html('<div class="table-responsive">' +
            '            <table class="table table-centre table-striped" id="dataTable">' +
            '                <thead>' +
            '                <tr>' +
            '                    <th style="width: 1%;">No</th>' +
            '                    <th>Question</th>' +
            '                    <th style="width: 5%;"><i class="fa fa-cog"></i></th>' +
            '                </tr>' +
            '                </thead>' +
            '                <tbody></tbody>' +
            '            </table>' +
            '        </div>');

        var url = module_url_question + 'getMyQuestion';

        var dataTable = $('#dataTable').DataTable({
            "processing": true,
            "serverSide": true,
            "iDisplayLength": 10,
            "ordering": false,
            "language": {
                "searchPlaceholder": "Topic . . ."
            },
            "responsive": true,
            "ajax": {
                url: url, // json datasource
                ordering: false,
                type: "post", // method  , by default get
                error: function() { // error handling
                    $(".employee-grid-error").html("");
                    $("#employee-grid").append('<tbody class="employee-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
                    $("#employee-grid_processing").css("display", "none");
                }
            }
        });
    }

    const loadQuestionType = () => {
        var url = base_url_js + 'api4/__crudQuiz';
        var token = jwt_encode({
            action: 'getQuestionType'
        }, 'UAP)(*');

        $.post(url, {
            token: token
        }, function(jsonResult) {

            $.each(jsonResult, function(i, v) {
                $('#btnCreateQuestion')
                    .append('<li><a href="javascript:void(0);" data-typeid="' + v.ID + '" class="add-new-question">' + v.Description + '</a></li>');
            });

            $('.add-new-question').click(function() {

                var TypeID = $(this).attr('data-typeid');
                updateQuestion(TypeID, '');

            });

            // console.log(jsonResult);
        });
    }


    const updateQuestion = async(TQID, QID) => {

        var url = module_url_question + 'countTotalMyQuestion';

        try{
            const jsonResult = await AjaxSubmitFormPromises(url);
            console.log(jsonResult)
            var newQuestion = parseInt(jsonResult.Total) + 1;
            var txt = $('#btnCreateQuestion li a[data-typeid="' + TQID + '"]').text();

            $('#GlobalModalLarge .modal-header').html('<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
                '<h4 class="modal-title">Create / Edit Question - ' + txt + '</h4>');

            var hidePoint = (TQID == 1) ? 'hide' : '';
            var formSummernoteID = sessionNIP + '_opt_admission_1_' + newQuestion;

            var pageMultiple = (TQID == 1 || TQID == 2) ?
                '        <div style="text-align: right;margin-bottom: 10px;">' +
                '            <button class="btn btn-sm btn-default" style="color: red;" id="btnRemoveOption"><i class="fa fa-minus-circle"></i> Option</button>' +
                '            <button class="btn btn-sm btn-default" style="color: green;" id="btnAddOption"><i class="fa fa-plus-circle"></i> Option</button>' +
                '             <input value="1" class="hide" id="totalOption" />' +
                '        </div>' +
                '<div class="alert alert-info ' + hidePoint + '" role="alert">' +
                '<i class="fa fa-info-circle"></i> <b>Important to know</b>' +
                '<ul>' +
                '<li>If the answer is an <b>incorrect answer</b>, the points must be <b>less than 0</b></li>' +
                '<li>If the answer is the <b>correct answer</b>, the points must be <b>more than 0</b></li>' +
                '<li>The number of correct answer points <b>must equal 100</b></li>' +
                '</ul></div>' +
                '        <table class="table table-bordered table-striped table-centre">' +
                '            <thead>' +
                '            <tr style="background: #eceff1;">' +
                '                <th style="width: 5%;">Option</th>' +
                '                <th>Description</th>' +
                '                <th style="width: 15%;">Set The Answer</th>' +
                '                <th style="width: 15%;" class="' + hidePoint + '">Point</th>' +
                '            </tr>' +
                '            </thead>' +
                '            <tbody id="listOption">' +
                '               <tr>' +
                '                   <td>1</td>' +
                '                   <td><textarea id="desc_1" class="form-control form-question" rows="1"></textarea>' +
                '                   <input class="hide" id="formSummernoteID_1" value="' + formSummernoteID + '" /></td>' +
                '                   <td>' +
                '                       <div class="checkbox checkbox-primary">' +
                '                           <input id="opt_1" class="setAnswer" type="checkbox">' +
                '                           <label for="opt_1">The Answer</label>' +
                '                       </div>' +
                '                   </td>' +
                '                   <td class="' + hidePoint + '" style="text-align: left;">' +
                '                       <input id="point_1"  data-opt="1" class="form-control form-point"  max="0" type="number"/>' +
                '                       <p id="opt_1_help" class="help-block">*) Less than 0</p>' +
                '                   </td>' +
                '               </tr>' +
                '            </tbody>' +
                '        </table>' :
                '';

            var htmlss = '<div class="row">' +
                '    <input class="hide" value="' + QID + '" id="formID" />' +
                '    <input class="hide" value="' + TQID + '" id="formTQID" />' +
                '    <input class="hide" value="' + sessionNIP + '_question_admission_' + newQuestion + '" id="formSummernoteID" />' +
                '    <input class="hide" value="' + newQuestion + '" id="newQuestion" />' +
                '    <div class="col-md-12">' +
                '        <div class="form-group">' +
                '            <label>Question</label>' +
                '            <textarea class="form-control form-question" rows="5" id="formQuestion"></textarea>' +
                '        </div>' + pageMultiple +
                '        <div class="form-group">' +
                '            <label>Note to this question (Optional)</label>' +
                '            <textarea class="form-control form-question" id="formNote" rows="2"></textarea>' +
                '        </div>' +
                '    </div>' +
                '</div>';

            $('#GlobalModalLarge .modal-body').html(htmlss);

            $('#GlobalModalLarge .modal-footer').html('' +
                '<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>' +
                '<button type="button" id="btnSaveQuestion" class="btn btn-success">Save</button> | ' +
                //'<button type="button" id="btnSaveQuestion2" class="btn btn-success">Save & Add In Quiz</button>' +
                '');

            $('#GlobalModalLarge').on('shown.bs.modal', function() {
                $('#formQuestion').focus();
            });

            $('#GlobalModalLarge').modal({
                'show': true,
                'backdrop': 'static'
            });
        }
        catch(err){
            console.log(err);
            toastr.info('something wrong');
        }
    }

    $(document).on('click', '#btnAddOption', function() {
        var formTQID = $('#formTQID').val();
        var hidePoint = (parseInt(formTQID) == 1) ? 'hide' : '';
        var totalOption = $('#totalOption').val();
        totalOption = parseInt(totalOption) + 1;

        var newQuestion = $('#newQuestion').val();
        var formSummernoteID = sessionNIP + '_opt_admission_' + totalOption + '_' + newQuestion;

        $('#totalOption').val(totalOption);
        $('#listOption').append('<tr class="tr_mtpc" id="tr_' + totalOption + '">' +
            '<td>' + totalOption + '</td>' +
            '<td><textarea id="desc_' + totalOption + '" class="form-control form-question" rows="1"></textarea>' +
            '<input class="hide" id="formSummernoteID_' + totalOption + '" value="' + formSummernoteID + '" /></td>' +
            '<td>' +
            '<div class="checkbox checkbox-primary">' +
            '   <input id="opt_' + totalOption + '" class="setAnswer" type="checkbox">' +
            '   <label for="opt_' + totalOption + '">The Answer</label>' +
            '</div>' +
            '</td>' +
            '<td class="' + hidePoint + '" style="text-align: left;">' +
            '<input id="point_' + totalOption + '" data-opt="' + totalOption + '" class="form-control form-point" type="number" />' +
            '<p id="opt_' + totalOption + '_help" class="help-block">*) Less than 0</p>' +
            '</td>' +
            '</tr>');
    });

    $(document).on('click', '#btnRemoveOption', function() {
        var totalOption = $('#totalOption').val();
        totalOption = parseInt(totalOption);

        if (totalOption > 1) {
            $('#tr_' + totalOption).remove();
            totalOption = totalOption - 1;
            $('#totalOption').val(totalOption);
        } else {
            alert('Firs option can not remove');
        }

    });

    $(document).on('change', '.setAnswer', function() {

        var formTQID = $('#formTQID').val();
        if (parseInt(formTQID) == 1) {
            $('.setAnswer').prop('checked', false);
            $(this).prop('checked', true);
        } else if (parseInt(formTQID) == 2) {
            var ID = $(this).attr('id');
            var opt = ID.split('opt_');
            var kethelp = ($(this).is(':checked')) ? '*) More than 0' : '*) Less than 0';
            $('#' + ID + '_help').html(kethelp);
            $('#point_' + opt[1]).val('');
        }

        $('.setAnswer').blur();

    });

    $(document).on('keyup', '.form-point', function() {

        var opt = $(this).attr('data-opt');
        var va = $(this).val();
        // cek apakah point untuk jawaban benar / salah
        if ($('#opt_' + opt).is(':checked')) {
            if (parseFloat(va) <= 0) {
                $(this).val(1);
            }

        } else {
            if (parseFloat(va) >= 0) {
                $(this).val(-1);
            }
        }

    });

    const LoadFilterTA = async() => {
        const url = module_url+'filter_ta';
        const response = await AjaxSubmitFormPromises(url);
        $('#filterTA').empty();
        $('#filterTA').append('<option disabled value = "0" selected>'+'Choose TA'+'</option>');
        for (var i = 0; i < response.length; i++) {
            let ii = response[i] + 1;
            $('#filterTA').append('<option value = "'+response[i]+'">'+response[i]+'/'+ii+'</option>');
        }
    }

    const LoadFilterCategory = async() => {
        const url = module_url+'filterCategory';
        const response = await AjaxSubmitFormPromises(url);
        $('#filterQuizCategory').empty();
        $('#filterQuizCategory').append('<option disabled value = "0" selected>'+'Choose Category'+'</option>');
        for (var i = 0; i < response.length; i++) {
            $('#filterQuizCategory').append('<option value = "'+response[i].ID+'">'+response[i].Type+'</option>');
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
        LoadQuiz();
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

        if ($('#filterQuizPublishOn option:selected').val() != null && $('#filterQuizPublishOn option:selected').val() != 0 ) {
            $('#viewPublishOn').html($('#filterQuizPublishOn option:selected').text());
            $('#panelPleaseChoose').addClass('hide');
            $('#panel-quiz').removeClass('hide');
            $('#formNotesForStudents').val('');
            $('#formDuration').val(1);
            $('#formDurationView').html('= ' + timeConvert(1));

            // load data quiz existing
            loadingStart();

            const url = module_url+'load_quiz';
            const data = {
                ID_q_quiz_schedule : $('#filterQuizPublishOn').val(),
                ID_q_quiz_category : $('#filterQuizCategory').val(),
            };

            var token = jwt_encode(data, 'UAP)(*');

            try{
                const response = await AjaxSubmitFormPromises(url,token);
                loadDomQuiz(response);
            }
            catch(err){
                console.log(err);
                toastr.info('something wrong');
            }
            loadingEnd(3500);
        }
        else
        {
            $('#viewPublishOn').html('');
            $('#panelPleaseChoose').removeClass('hide');
            $('#panel-quiz').addClass('hide');
            $('#formNotesForStudents').val('');
            $('#formDuration').val('');
            $('#formDurationView').html('');

            $('#btnStudentAnswer').addClass('hide');
            $('#dataTempQuiz').val('');
            $('#dataLoadQuiz').val('');

            loadDataQuiz();
        }

    }

    const loadDomQuiz = async(jsonResult) => {
        var details = jsonResult.Details;
        if (parseInt(jsonResult.TotalAnswer) > 0) {
            $('#btnStudentAnswer').removeClass('hide');
            $('#btnStudentAnswer .badge').html(jsonResult.TotalAnswer);
        } else {
            $('#btnStudentAnswer').addClass('hide');
        }

        var arrQID = [];
        if (details.length > 0) {
            $.each(details, function(i, v) {
                arrQID.push(v.QID);
            });
        }

        var va = (arrQID.length > 0) ? JSON.stringify(arrQID) : '';
        $('#dataTempQuiz').val(va);

        var vaLoad = (arrQID.length > 0) ? JSON.stringify(jsonResult) : '';
        $('#dataLoadQuiz').val(vaLoad);

        await timeout(2000);

        loadDataQuiz();

    }

    const loadDataQuiz = async() => {
       var dataTempQuiz = $('#dataTempQuiz').val();     
       if (dataTempQuiz != '' && dataTempQuiz != null) {
            var d = (dataTempQuiz != '') ? JSON.parse(dataTempQuiz) : [];
            var dataLoadQuiz = $('#dataLoadQuiz').val();
            var dataQ = (dataLoadQuiz != '' && dataLoadQuiz != null) ? JSON.parse(dataLoadQuiz) : [];
            var TotalAnswer = 0;

            if (dataLoadQuiz != '' && dataLoadQuiz != null && dataQ.Quiz.length > 0) {
                TotalAnswer = parseInt(dataQ.TotalAnswer);
                $('.DurationType[value="'+dataQ.Quiz[0].DurationType+'"]').prop('checked', true);

                const itsDurationType = $('.DurationType[value="'+dataQ.Quiz[0].DurationType+'"]');
                onClickDurationType(itsDurationType);

                $('#DurationFixedStart').val(dataQ.Quiz[0].DurationFixedStart);
                $('#DurationFixedEnd').val(dataQ.Quiz[0].DurationFixedEnd);

                $('#formNotesForStudents').val(dataQ.Quiz[0].NotesForStudents);
                $('#formDuration').val(dataQ.Quiz[0].DurationFlexi);
                $('#formDurationView').html('= ' + timeConvert(dataQ.Quiz[0].DurationFlexi));
                $('#viewPoint').html('100');
                $('#btnSaveQuiz').prop('disabled', false);
            }

            var data = {
                ArrQID: d
            };

            var token = jwt_encode(data, 'UAP)(*');
            var url = module_url_question + 'getArrDataQuestion';

            var disabled = (TotalAnswer > 0) ? 'disabled' : '';

            try{
                const jsonResult = await AjaxSubmitFormPromises(url,token);
                if (jsonResult.length > 0) {
                    var listQuestoin = '';

                    $.each(jsonResult, function(i, v) {

                        var o = v.Option;
                        var listOpt = '';
                        if (o.length > 0) {
                            $.each(o, function(i2, v2) {
                                var isAns = (v2.IsTheAnswer == 1 || v2.IsTheAnswer == '1') ?
                                    '<i class="fa fa-check-circle" style="color: green;margin-right:5px;"></i>' :
                                    '';

                                var pointBBG = (v2.Point != '' && v2.Point != null && parseFloat(v2.Point) > 0) ?
                                    '#7CB342' : '#E57373';


                                var viewPoint = (v2.Point != '' && v2.Point != null) ? '<span class="lbl-point" style="background: ' + pointBBG + '; ">' + v2.Point + '</span>' : '';
                                listOpt = listOpt + '<li style="line-height: 1.428571;margin-bottom: 0px;"><div style = "display: inline-flex!important;">' + viewPoint + isAns + v2.Option + '</div></li>';
                            });
                        }

                        var q = v.Question;
                        var expandDetail = (q.QTID == 1 || q.QTID == 2) ?
                            '                            <div class="collapse" id="collapseExample_' + i + '">' +
                            '                                <hr/>' +
                            '                                        <div class="well">' +
                            '                                            <ul>' + listOpt + '</ul>' +
                            '                                        </div>' +
                            '                            </div>' :
                            '';


                        var alertManualCorrection = (q.QTID == 3) ?
                            '<div class="alert alert-warning alert-essay" role="alert">' +
                            '<i class="fa fa-exclamation-triangle margin-right"></i> Need manual correction</div>' : '';

                        var valuePoint = '';
                        var dataTempQuizPoint = $('#dataTempQuizPoint').val();
                        if (dataTempQuizPoint != '') {
                            valuePoint = searchPointTemporary(q.ID);
                        } else if (dataLoadQuiz != '' && dataLoadQuiz != null && dataQ.Details.length > 0) {
                            valuePoint = searchID(q.ID, dataQ.Details);
                        }

                        var viewQuestion = (v.Status == 1 || v.Status == '1') ?
                            '<span class="lbl-' + q.QTID + '">' + q.Type + '</span> - ' + q.Question :
                            '<div class="alert alert-danger" style="margin-bottom: 0px;">Question is outdated, please <b>delete</b> it immediately</div>';

                        listQuestoin = listQuestoin + '<li class="item-quiz" data-id="' + q.ID + '">' +
                            '                            <a role="button" data-toggle="collapse" href="#collapseExample_' + i + '" aria-expanded="true" aria-controls="collapseExample">' + viewQuestion + '</a>' +
                            '                           <div class="panel-act-question-in-quiz">' +
                            '                                   <input class="form-control form-quiz-point" value="' + valuePoint + '" id="point_quiz_' + q.ID + '" placeholder="Point..." type="number" ' + disabled + ' style="max-width: 100px;display: inline;" >' +
                            '                                   <button class="btn btn-danger btn-sm btnRemoveQuestion" data-id="' + v.QID + '" ' + disabled + '><i class="fa fa-trash"></i></button>' +
                            '                           </div>' + expandDetail + alertManualCorrection +
                            '                        </li>';

                    });
                }

                await timeout(500);

                $('#loadQuestionListOnQuiz').html('<ol id="listQuiz">' + listQuestoin + '</ol>');
                if (TotalAnswer <= 0) {
                    $('#listQuiz').sortable({
                        axis: 'y',
                        update: function(event, ui) {
                            var dataUpdate = [];
                            $('#listQuiz li.item-quiz').each(function() {
                                dataUpdate.push($(this).attr('data-id'));
                            });

                            $('#dataTempQuiz').val(JSON.stringify(dataUpdate));

                        }
                    });
                }

                $('#showNoteQuiz,.panel-footer').removeClass('hide');
                var disabledForm = (TotalAnswer > 0) ? true : false;
                $('#formNotesForStudents,#formDuration,#btnSaveQuiz').prop('disabled', disabledForm);

            }
            catch(err){
                console.log(err);
                toastr.info('something wrong');
            }
       }
       else
       {
        $('#showNoteQuiz,.panel-footer').addClass('hide');
        $('#loadQuestionListOnQuiz').html('<div style="text-align: center;">' +
            '<img src="' + "<?php echo url_sign_in_lecturers ?>" + 'images/icon/empty.jpg" style="width: 100%;max-width: 200px;" />' +
            '<h3 style="color: #9E9E9E;"><b>--- No question ---</b></h3>' +
            '</div>');
       } 
    }

    const saveQuestion = async(action='',itsme) => {

        if (confirm('Are you sure ?')) {
            var formID = $('#formID').val();

            var formTQID = $('#formTQID').val();
            var formQuestion = $('#formQuestion').val();
            var formNote = $('#formNote').val();

            var submitQ = true;

            var dataOption = [];

            var errMsg = 'Please check the required form';

            if(formTQID==3){
                submitQ = (formQuestion!='' && formQuestion!=null) ? true : false;
            }
            else if(formTQID==1){
                var totalOption = $('#totalOption').val();
                for(var i=1;i<=totalOption;i++){
                    var des = $('#desc_'+i).val();
                    submitQ = (des!='' && des!=null) ? true : false;
                    var IsTheAnswer = ($('#opt_'+i).is(':checked')) ? '1' : '0';
                    var formSummernoteID = $('#formSummernoteID_'+i).val();
                    var arrOpt = {
                        SummernoteID : formSummernoteID,
                        Option : des,
                        IsTheAnswer : IsTheAnswer
                    };

                    dataOption.push(arrOpt);
                }
            }
            else if(formTQID==2){
                var totalOption = $('#totalOption').val();
                var totalPointBenar = 0;
                var totalPointSalah = 0;
                for(var i=1;i<=totalOption;i++){
                    var des = $('#desc_'+i).val();
                    var Point = $('#point_'+i).val();
                    submitQ = (des!='' && des!=null) ? true : false;
                    var IsTheAnswer = ($('#opt_'+i).is(':checked')) ? '1' : '0';
                    var formSummernoteID = $('#formSummernoteID_'+i).val();
                    var arrOpt = {
                        SummernoteID : formSummernoteID,
                        Option : des,
                        IsTheAnswer : IsTheAnswer,
                        Point : (Point!='' && Point!=null) ? Point : 0
                    };

                    if($('#opt_'+i).is(':checked')){
                        totalPointBenar = parseFloat(totalPointBenar) + parseFloat(Point);
                    } else {
                        totalPointSalah = parseFloat(totalPointSalah) + parseFloat(Point);
                    }

                    dataOption.push(arrOpt);
                }

                if(totalPointBenar==100 && totalPointSalah==-100){
                    submitQ = true;
                } else {
                    errMsg = 'The number of points for correct answers must be equal to 100 ' +
                        'and the number of points for correct answers must be equal to -100';
                    submitQ = false;
                }

            }

            var data = {
                action : 'saveQuestion',
                ID : (formID!='' && formID!=null) ? formID : '',
                SummernoteID : $('#formSummernoteID').val(),
                NIP : sessionNIP,
                dataQustion : {
                    QTID : formTQID,
                    Question : formQuestion,
                    Note : (formNote!='' && formNote!=null) ? formNote : ''
                },
                dataOption : dataOption
            };

            var token = jwt_encode(data,'UAP)(*');
            var url = '<?php echo $module_url_question.'save' ?>';

            loadingStart();
            try{
                const jsonResult = await AjaxSubmitFormPromises(url,token);
                var dataLoadQuiz = $('#dataLoadQuiz').val();
                var dataQ = (dataLoadQuiz != '' && dataLoadQuiz != null) ? JSON.parse(dataLoadQuiz) : [];
                var TotalAnswer = (dataLoadQuiz != '' && dataLoadQuiz != null &&
                    dataQ.Quiz.length > 0) ? parseInt(dataQ.TotalAnswer) : 0;

                if (TotalAnswer > 0) {
                    toastr.warning('Quiz cannot be edited', 'Warning');
                } else {
                    var dataTempQuiz = $('#dataTempQuiz').val();
                    var d = (dataTempQuiz != '') ? JSON.parse(dataTempQuiz) : [];
                    d.push(jsonResult.QID);
                    $('#dataTempQuiz').val(JSON.stringify(d));

                    loadDataQuiz();
                }

                loadMyQuestion();

                if (formID != '' && formID != null) {

                } else {
                    setTimeout(function() {
                        $('#GlobalModalLarge').modal('hide');
                    }, 500);
                }
                
            }
            catch(err){
                console.log(err)
                toastr.info('error save data'); 
            }

            loadingEnd(500);
        }

    }

    const removeQuestion = async(itsme) => {
        if (confirm('Are you sure?')) {

            loadingStart();

            var QID = itsme.attr('data-id');

            var data = {
                QID: QID
            };

            var token = jwt_encode(data, 'UAP)(*');
            var url = module_url_question + 'removeQuestion';

            try{
                const jsonResult =  await AjaxSubmitFormPromises(url,token);
                if (parseInt(jsonResult.Usage) > 0) {
                    toastr.warning('Questions have been registered in several quizzes', 'Cannot be deleted');
                } else {
                    toastr.success('Removed data', 'Success');
                    loadDataQuiz();
                    loadMyQuestion();
                }
            }
            catch(err){
                toastr.info('something wrong');
                console.log(err);
            }

            loadingEnd(500);

        }
    }

    $(document).on('click', '.removeQuestion', function() {
        const itsme = $(this);
        removeQuestion(itsme);
    });

    $(document).on('click','#btnSaveQuestion',function () {

        const itsme =  $(this);
        saveQuestion('',itsme);
    });

    const addToQuizFromMyQuestion = async(itsme) => {
        var filterQuizCategory = $('#filterQuizCategory').val();
        var filterQuizPublishOn = $('#filterQuizPublishOn').val();

        if (filterQuizCategory != '' && filterQuizCategory != null &&
            filterQuizPublishOn != '' && filterQuizPublishOn != null) {

            var dataLoadQuiz = $('#dataLoadQuiz').val();
            var dataQ = (dataLoadQuiz != '' && dataLoadQuiz != null) ? JSON.parse(dataLoadQuiz) : [];
            var TotalAnswer = (dataLoadQuiz != '' && dataLoadQuiz != null &&
                dataQ.Quiz.length > 0) ? parseInt(dataQ.TotalAnswer) : 0;

            if (TotalAnswer > 0) {
                toastr.warning('Quiz cannot be edited', 'Warning');
            } else {

                var ID = itsme.attr('data-id');

                // cek apakah ID sudah di add atau blm
                var dataTempQuiz = $('#dataTempQuiz').val();
                var d = (dataTempQuiz != '' && dataTempQuiz != null) ? JSON.parse(dataTempQuiz) : [];

                var pushID = true;

                if (d.length > 0) {
                    for (var i = 0; i < d.length; i++) {
                        if (ID == d[i]) {
                            toastr.warning('The question is already in the quiz', 'Warning');
                            pushID = false;
                            break;
                        }
                    }
                } else {
                    pushID = true;

                }

                if (pushID) {
                    d.push(ID);
                    var newVal = JSON.stringify(d);
                    $('#dataTempQuiz').val(newVal);
                    loadDataQuiz();
                    toastr.success('Added to quiz', 'Success');
                }
            }

        } else {
            toastr.warning('Please, choose a TA , a Category and a publish on', 'Warning');
        }
    }

    const onClickDurationType = (itsme) => {
         const v = itsme.val();
         if (v == 'Flexi') {
             $('.DurationFlexiDiv').removeClass('hide');
             $('.DurationFixedDiv').addClass('hide');
         }
         else
         {
             $('.DurationFlexiDiv').addClass('hide');
             $('.DurationFixedDiv').removeClass('hide');
         }
    }

    $(document).on('click','.DurationType',function(e){
        const itsme = $(this);
       
        onClickDurationType(itsme);
    })

    $(document).on('keyup', '.form-quiz-point', function() {

        var va = $(this).val();
        var d = parseFloat(va);

        if (d <= 0) {
            $(this).val(1);
        } else if (d > 100) {
            $(this).val(100);
        } else {
            $(this).val(d);
        }

        // Count point
        countPointQuestion();

    });

    const countPointQuestion = () => {
        var dataTempQuiz = $('#dataTempQuiz').val();
        var totalPoint = 0;
        if (dataTempQuiz != '') {
            var d2 = (dataTempQuiz != '') ? JSON.parse(dataTempQuiz) : [];

            if (d2.length > 0) {
                var dataTempQuizPoint = [];
                for (var i = 0; i < d2.length; i++) {
                    var point_quiz = $('#point_quiz_' + d2[i]).val();
                    var arrPoint = {
                        ID: d2[i],
                        Point: point_quiz
                    };

                    dataTempQuizPoint.push(arrPoint);

                    point_quiz = (point_quiz != '' && point_quiz != null) ? point_quiz : 0;
                    totalPoint = totalPoint + parseFloat(point_quiz);
                }
                $('#dataTempQuizPoint').val(JSON.stringify(dataTempQuizPoint));
            }
        }

        $('#btnSaveQuiz').prop('disabled', (totalPoint != 100) ? true : false);

        $('#viewPoint').html(totalPoint);
    }

    $('#formDuration').keyup(function() {
        var formDuration = $('#formDuration').val();
        if (parseFloat(formDuration) <= 0) {
            $('#formDuration').val(1);
        }

        var formDuration2 = $('#formDuration').val();
        $('#formDurationView').html('= ' + timeConvert(formDuration2));
    });

    $(document).on('click', '.addToQuizFromMyQuestion', function() {

        const itsme = $(this);
        addToQuizFromMyQuestion(itsme);
    });

    $(document).on('click', '.btnRemoveQuestion', function() {

        var dataLoadQuiz = $('#dataLoadQuiz').val();
        var dataQ = (dataLoadQuiz != '' && dataLoadQuiz != null) ? JSON.parse(dataLoadQuiz) : [];
        var TotalAnswer = (dataLoadQuiz != '' && dataLoadQuiz != null &&
            dataQ.Quiz.length > 0) ? parseInt(dataQ.TotalAnswer) : 0;

        if (TotalAnswer > 0) {
            toastr.warning('Quiz cannot be edited', 'Warning');
        } else {
            if (confirm('Are you sure?')) {

                var ID = $(this).attr('data-id');
                var dataTempQuiz = $('#dataTempQuiz').val();
                var d = JSON.parse(dataTempQuiz);

                var newArr = [];
                if (d.length > 0) {
                    for (var i = 0; i < d.length; i++) {
                        if (d[i] != ID) {
                            newArr.push(d[i]);
                        }

                    }
                }

                if (newArr.length > 0) {
                    $('#dataTempQuiz').val(JSON.stringify(newArr));
                } else {
                    $('#dataTempQuiz').val('');
                }

                // Point
                countPointQuestion();

                loadDataQuiz();

            }
        }

    });

    function searchPointTemporary(ID) {
        var dataTempQuizPoint = $('#dataTempQuizPoint').val();
        var point = 0;
        if (dataTempQuizPoint != '' && dataTempQuizPoint != null) {
            var d = JSON.parse(dataTempQuizPoint);
            if (d.length > 0) {
                $.each(d, function(i, v) {
                    if (v.ID == ID) {
                        point = v.Point
                    }
                })
            }
        }
        return point;

    }

    const saveQuiz = async(itsme) => {

        var ID_q_quiz_category = $('#filterQuizCategory option:selected').val();
        var ID_q_quiz_schedule = $('#filterQuizPublishOn option:selected').val();

        var dataTempQuiz = $('#dataTempQuiz').val();
        var formNotesForStudents = $('#formNotesForStudents').val();
        var formDuration = $('#formDuration').val();

        if (dataTempQuiz != '' &&
            ID_q_quiz_category != '' && ID_q_quiz_category != null &&
            ID_q_quiz_schedule != '' && ID_q_quiz_schedule != null) {
            var d = (dataTempQuiz != '') ? JSON.parse(dataTempQuiz) : [];

            var totalPoint = 0;
            var dataForm = [];

            if (d.length > 0) {
                for (var i = 0; i < d.length; i++) {
                    var point_quiz = $('#point_quiz_' + d[i]).val();
                    point_quiz = (point_quiz != '' && point_quiz != null) ? point_quiz : 0;
                    totalPoint = totalPoint + parseFloat(point_quiz);

                    var arr = {
                        QID: d[i],
                        Point: point_quiz
                    };
                    dataForm.push(arr);

                }
            }

            if (totalPoint != 100) {

                toastr.warning('The total points must be equal to 100', 'Warning');

            } else {

                // cek maksimal duration
                // if (parseFloat(formDuration) <= (3 * 60)) {
                if (true) {
                   // loading_page_modal();
                   loadingStart();

                   var DurationType = $('.DurationType:checked').val();
                   var DurationFixedStart = $('#DurationFixedStart').val();
                   var DurationFixedEnd = $('#DurationFixedEnd').val();
                   console.log(DurationType);

                    var data = {
                        NIP: sessionNIP,
                        ID_q_quiz_category : ID_q_quiz_category,
                        ID_q_quiz_schedule : ID_q_quiz_schedule,
                        NotesForStudents: formNotesForStudents,
                        DurationType : DurationType,
                        DurationFixedStart : DurationFixedStart,
                        DurationFixedEnd : DurationFixedEnd,
                        DurationFlexi: formDuration,
                        dataForm: dataForm
                    };

                    var token = jwt_encode(data, 'UAP)(*');
                    var url = module_url + 'saveDataQuiz';

                    try{
                        const jsonResult =  await AjaxSubmitFormPromises(url,token);
                        if (jsonResult.Status == 1 || jsonResult.Status == '1') {
                            toastr.success('Saved quiz', 'Success');
                            // setTimeout(function() {
                            //     window.location.href = '';
                            // }, 500);
                            LoadQuiz();
                        } else if (jsonResult.Status == -1 || jsonResult.Status == '-1') {
                            toastr.warning(jsonResult.Message, 'Warning');
                            alert(jsonResult.Message);
                            // setTimeout(function() {
                            //     window.location.href = '';
                            // }, 500);
                            LoadQuiz();

                        } else if (jsonResult.Status == -2 || jsonResult.Status == '-2') {
                            toastr.warning(jsonResult.Message, 'Warning');
                            loadDataQuiz();
                        } else if (jsonResult.Status == -3 || jsonResult.Status == '-3') {
                            toastr.error(jsonResult.Message, 'error');
                            loadDataQuiz();
                        }
                    }
                    catch(err){
                        console.log(err);
                        toastr.info('something wrong');
                    }
                    loadingEnd(500);
                } else {
                    toastr.warning('Maximum quiz time is 3 hours', 'Warning');
                }

            }

        } else {
            toastr.warning('Please, fill out the required forms', 'Warning');
        }
    }

    function searchID(nameKey, myArray) {
        for (var i = 0; i < myArray.length; i++) {
            if (myArray[i].QID === nameKey) {
                return myArray[i].Point;
            }
        }
    }

    $(document).on('click', '#btnSaveQuiz', function() {

        const itsme =  $(this);
        saveQuiz(itsme);

    });

    $(document).on('change','#filterQuizPublishOn,#filterQuizCategory',function(e){
        LoadQuiz();
    })
    
    $(document).ready(function(e){
        load_default();
    })

    $(document).on('click', '#btnStudentAnswer', function() {

        var filterTA = $('#filterTA option:selected').val();
        var filterTAText = $('#filterTA option:selected').text();
        var filterQuizCategory = $('#filterQuizCategory option:selected').text();
        var filterQuizPublishOn = $('#filterQuizPublishOn option:selected').text();

        var dataLoadQuiz = $('#dataLoadQuiz').val();

        var d = (dataLoadQuiz != '' && dataLoadQuiz != null) ? JSON.parse(dataLoadQuiz) : [];
        console.log(d);

        if (parseInt(d.TotalAnswer) > 0) {

            var Quiz = d.Quiz[0];

            var data = {
                TA : filterTA,
                TAText : filterTAText,
                Course: filterQuizCategory,
                Session: filterQuizPublishOn,
                QuizID: Quiz.ID,
                dataQuiz : d,
            };

            var token = jwt_encode(data,"UAP)(*");
            window.open(module_url + 'student_answers/' + token);
        }

    });
</script>
