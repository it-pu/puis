<div class="" style="margin-top: 30px;">
    <div class="row">
        <div class="col-md-12">
            <div class="widget box">
                <div class="widget-header">
                    <h4><i class="icon-reorder"></i> Category Room</h4>
                    <div class="toolbar no-padding">
                        <div class="btn-group">
                          <span data-smt="" class="btn btn-xs btn-add btn-Categoryclassroom" data-action="add">
                            <i class="icon-plus"></i> Add
                           </span>
                        </div>
                    </div>
                </div>
                <div class="widget-content no-padding" id="viewCategoryClassroom"></div>
            </div>
        </div>
    </div>
</div>
<div class="" style="margin-top: 30px;">
    <div class="row">
        <div class="col-md-12">
            <div class="widget box">
                <div class="widget-header">
                    <h4><i class="icon-reorder"></i> Room</h4>
                    <div class="toolbar no-padding">
                        <div class="btn-group">
                            <span class="btn btn-xs" style="background: #083f88;color: #fff;">
                                <strong>
                                    <span id="totalRoom"></span> Room
                                </strong>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="widget-content no-padding" id="viewClassroom"></div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    var CategoryRoom = <?php echo json_encode($CategoryRoom)  ?>;
    var employees = <?php echo json_encode($employees)  ?>;
    var division = <?php echo json_encode($division)  ?>;
    var GroupUser = <?php echo json_encode($GroupUser)  ?>;
    var PositionUser = <?php echo json_encode($PositionUser)  ?>;
    $(document).ready(function () {
        // console.log(employees);
        loadDataCategoryClassroom();
        loadDataClassroom();
    });

    // ----- Classroom --------
    $(document).on('click','.btn-classroom',function () {
        var action = $(this).attr('data-action');
        var classroom = (action=='edit' || action=='delete') ? $(this).attr('data-form').split('|') : '';
        var ID = (action=='edit' || action=='delete') ? classroom[0] : '';
        var Room = (action=='edit' || action=='delete') ? classroom[1] : '';
        var Seat = (action=='edit') ? parseInt(classroom[2]) : '';
        var SeatForExam = (action=='edit') ? parseInt(classroom[3]) : '';
        var DeretForExam = (action=='edit') ? parseInt(classroom[4]) : '';
        var LectureDesk = (action=='edit') ? classroom[5] : '';
        var ID_CategoryRoom = (action=='edit') ? classroom[6] : '';
        
        if(action=='add' || action=='edit'){
            <?php $positionMain = $this->session->userdata('PositionMain'); 
                $positionMain = $positionMain['IDDivision'];
            ?>
            <?php if ($positionMain == 12): ?>
                var readonly = '';
            <?php else: ?>
                var readonly = (action=='edit')? 'readonly' : '';
            <?php endif ?>

            // get CategoryRoom
                var OptionCategoryRoom = '';
                for (var i = 0; i < CategoryRoom.length; i++) {
                    var selected =  (action=='edit' && ID_CategoryRoom == CategoryRoom[i]['ID']) ? 'selected' : '';
                    OptionCategoryRoom += '<option value = "'+CategoryRoom[i]['ID']+'" '+selected+'>'+CategoryRoom[i]['NameEng']+'</option>';
                }
            $('#GlobalModal .modal-header').html('<h4 class="modal-title">Classroom</h4>');
            $('#GlobalModal .modal-body').html('<div class="row">' +
                '                            <div class="col-xs-4">' +
                '                                <div class="form-group"><label>CategoryRoom</label>' +
                '                                   <select id = "formCategoryRoom" class="form-control">'+
                                                        OptionCategoryRoom+
                '                                   </select>'+
                '                            </div></div>' +
                '                            <div class="col-xs-4">' +
                '                                <div class="form-group"><label>Room</label>' +
                '                                <input type="text" class="form-control" value="'+Room+'" '+readonly+' style="color:#333;" id="formRoom">' +
                '                            </div></div>' +
                '                            <div class="col-xs-4">' +
                '                                <div class="form-group"><label>Seat</label>' +
                '                                <input type="number" class="form-control" value="'+Seat+'" id="formSeat">' +
                '                            </div></div>' +
                '                            <div class="col-xs-4">' +
                '                                <div class="form-group"><label>Seat For Exam</label>' +
                '                                <input type="number" class="form-control" value="'+SeatForExam+'" id="formSeatForExam">' +
                '                            </div></div>' +
                '                            <div class="col-xs-4">' +
                '                                <div class="form-group"><label>Deret For Exam</label>' +
                '                                <input type="number" class="form-control" value="'+DeretForExam+'" id="formDeretForExam" min = "2" max = "10">' +
                '                            </div></div>' +
                '                            <div class="col-xs-4">' +
                '                                <div class="form-group"><label>Lecture Desk</label>' +
                '                                   <select id = "formLectureDesk" class="form-control">'+
                '                                          <option value = "left">Left</option>'+
                '                                          <option value = "right">Right</option>'+
                '                                   </select>'+
                '                            </div></div>' +
                                             '<div class="col-xs-4">'+
                                                '<div class="form-group"><label class="control-label">Layout:</label>'+
                                                 '<input type="file" data-style="fileinput" id="ExFile">'+
                                                 '</div>'+
                                             '</div>'+
                '                        </div>');
            $('#GlobalModal .modal-footer').html('<button type="button" id="btnCloseClassroom" class="btn btn-default" data-dismiss="modal">Close</button>' +
                '<button type="button" class="btn btn-success" data-id="'+ID+'" data-action="'+action+'" id="btnSaveClassroom">Save</button>');
            $('#GlobalModal').modal({
                'show' : true,
                'backdrop' : 'static'
            });

            // console.log(action);

            if(action == 'edit')
            {
                console.log(LectureDesk);
                $("#formLectureDesk option").filter(function() {
                   //may want to use $.trim in here
                   return $(this).val() == LectureDesk; 
                 }).prop("selected", true);
            }
        }
        else {
            $('#NotificationModal .modal-body').html('<div style="text-align: center;">Hapus <b style="color: red;">'+Room+'</b>  ?? | ' +
                '<button type="button" id="btnDeleteClassroom" data-id="'+ID+'" class="btn btn-primary" style="margin-right: 5px;">Yes</button>' +
                '<button type="button" id="btnTidak" class="btn btn-default" data-dismiss="modal">No</button>' +
                '</div>');
            $('#NotificationModal').modal('show');
        }

    });
    $(document).on('click','#btnSaveClassroom',function () {

        var action = $(this).attr('data-action');
        var ID = $(this).attr('data-id');

        var process = true;

        var Room = $('#formRoom').val(); process = (Room=='') ? errorInput('#formRoom') : true ;
        var Seat = $('#formSeat').val(); var processSeat = (Seat!='' && $.isNumeric(Seat) && Math.floor(Seat)==Seat) ? true : errorInput('#formSeat') ;
        var SeatForExam = $('#formSeatForExam').val(); var processSeatForExam = (SeatForExam!='' && $.isNumeric(SeatForExam) && Math.floor(SeatForExam)==SeatForExam) ? true : errorInput('#formSeatForExam') ;
        var DeretForExam = $('#formDeretForExam').val(); var processDeretForExam = (DeretForExam!='' && $.isNumeric(DeretForExam) && Math.floor(DeretForExam)==DeretForExam) ? true : errorInput('#formDeretForExam') ;
        var LectureDesk = $('#formLectureDesk').val(); process = (LectureDesk=='') ? errorInput('#formLectureDesk') : true ;
        var formCategoryRoom = $("#formCategoryRoom").val();
        if(Room!='' && processSeat && processSeatForExam){
            $('#formRoom,#formSeat,#formSeatForExam,#btnCloseClassroom').prop('disabled',true);
            loading_button('#btnSaveClassroom');
            loading_page('#viewClassroom');

            var data = {
                action : action,
                ID : ID,
                formData : {
                    Room : Room,
                    Seat : Seat,
                    SeatForExam : SeatForExam,
                    DeretForExam : DeretForExam,
                    Status : 0,
                    UpdateBy : sessionNIP,
                    UpdateAt : dateTimeNow(),
                    LectureDesk : LectureDesk,
                    ID_CategoryRoom : formCategoryRoom,
                }
            };

            var form_data = new FormData();
            var fileData = document.getElementById("ExFile").files[0];
            var url = base_url_js + "api/__crudClassroomVreservation"
            var token = jwt_encode(data,"UAP)(*");
            form_data.append('token',token);
            form_data.append('fileData',fileData);
            $.ajax({
              type:"POST",
              url:url,
              data: form_data, // Data sent to server, a set of key/value pairs (i.e. form fields and values)
              contentType: false,       // The content type used when sending data to the server.
              cache: false,             // To unable request pages to be cached
              processData:false,
              dataType: "json",
              success:function(data_result)
              {
                    loadDataClassroom();

                   setTimeout(function () {

                       if(data_result.inserID!=0) {
                           toastr.success('Data tersimpan','Success!');
                           $('#GlobalModal').modal('hide');
                           // if(action=='add'){$('#formRoom,#formSeat,#formSeatForExam').val('');}
                       } else {
                           $('#formRoom,#formSeat,#formSeatForExam,#btnCloseClassroom').prop('disabled',false);
                           $('#btnSaveClassroom').prop('disabled',false).html('Save');
                           toastr.warning('Room is exist','Warning');
                       }
                   },1000);

              },
              error: function (data) {
                toastr.error("Connection Error, Please try again", 'Error!!');
                $('#btnSaveClassroom').prop('disabled',false).html('Save');
              }
            })

        } else {
            toastr.error('Form Required','Error!');
        }
    });

    $(document).on('click','#btnDeleteClassroom',function () {
        var ID = $(this).attr('data-id');
        var token = jwt_encode({action:'delete',ID:ID},'UAP)(*');
        var url = base_url_js+"api/__crudClassroomVreservation";

        $('#btnTidak').prop('disabled',true);
        loading_buttonSm('#btnDeleteClassroom');
        $.post(url,{token:token},function () {
            loadDataClassroom();
            setTimeout(function () {
                toastr.success('Data Terhapus','Success!');
                $('#NotificationModal').modal('hide');
            });
        });
    });

    $(document).on('click','.btn-Categoryclassroom',function () {
        var action = $(this).attr('data-action');
        var classroom = (action=='edit' || action=='delete') ? $(this).attr('data-form').split('|') : '';
        var ID = (action=='edit' || action=='delete') ? classroom[0] : '';
        var Name = (action=='edit' || action=='delete') ? classroom[1] : '';
        var NameEng = (action=='edit' || action=='delete') ? classroom[2] : '';
        var Approver1 = (action=='edit' || action=='delete') ? $(this).attr('approver1_ori') : '';
        var Approver2 = (action=='edit' || action=='delete') ? $(this).attr('approver2_ori') : '';
        if(action=='add' || action=='edit'){
            // option value selected for approver
            var JsonApprover1 = '';
            var UserType1Edit = '';
            var TypeApprover1Edit = '';
            var Approver1Edit = '';
            if (action=='edit') {
                JsonApprover1 = jQuery.parseJSON(findAndReplace(Approver1, "'", '"'));
                if (JsonApprover1.length > 0) {
                    UserType1Edit = JsonApprover1[0].UserType;
                    TypeApprover1Edit = JsonApprover1[0].TypeApprover;
                    Approver1Edit = JsonApprover1[0].Approver;
                }
                
            }

            // option value selected for approver
            var JsonApprover2 = '';
            var UserType2Edit = '';
            var TypeApprover2Edit = '';
            var Approver2Edit = '';
            if (action=='edit') {
                JsonApprover2 = jQuery.parseJSON(findAndReplace(Approver2, "'", '"'));
                if (JsonApprover2.length > 0) {
                    TypeApprover2Edit = JsonApprover2[0].TypeApprover;
                    Approver2Edit = JsonApprover2[0].Approver;
                }
                
            }

            
                var getPositionUser = (function(selected1){
                    var aa = '';
                    for (var i = 0; i < PositionUser.length; i++) {
                        if (selected1 == '') {
                            aa+= '<option value="'+PositionUser[i].ID+'" '+''+'>'+PositionUser[i].Position+'</option>';
                        }
                        else
                        {
                            if (PositionUser[i].ID == selected1) {
                                aa+= '<option value="'+PositionUser[i].ID+'" '+'selected'+'>'+PositionUser[i].Position+'</option>';
                            }
                            else
                            {
                                aa+= '<option value="'+PositionUser[i].ID+'" '+''+'>'+PositionUser[i].Position+'</option>';
                            }
                        }
                    }
                    return aa;
                })

                var getGroupUser = (function(selected1){
                    var aa = '';
                    for (var i = 0; i < GroupUser.length; i++) {
                        // aa+= '<option value="'+GroupUser[i].ID+'" '+''+'>'+GroupUser[i].GroupAuth+'</option>';
                        if (selected1 == '') {
                            aa+= '<option value="'+GroupUser[i].ID+'" '+''+'>'+GroupUser[i].GroupAuth+'</option>';
                        }
                        else
                        {
                            if (GroupUser[i].ID == selected1) {
                                aa+= '<option value="'+GroupUser[i].ID+'" '+'selected'+'>'+GroupUser[i].GroupAuth+'</option>';
                            }
                            else
                            {
                                aa+= '<option value="'+GroupUser[i].ID+'" '+''+'>'+GroupUser[i].GroupAuth+'</option>';
                            }
                        }
                    }
                    return aa;
                })

                var getEmployees = function(selected1){
                    var aa = '';
                    for(var i=0;i< employees.length;i++){
                        if (selected1 == '') {
                            aa+= '<option value="'+employees[i].NIP+'" '+''+'>'+employees[i].NIP+' | '+employees[i].Name+'</option>';
                        }
                        else
                        {
                            if (employees[i].NIP == selected1) {
                                aa+= '<option value="'+employees[i].NIP+'" '+'selected'+'>'+employees[i].NIP+' | '+employees[i].Name+'</option>';
                            }
                            else
                            {
                                aa+= '<option value="'+employees[i].NIP+'" '+''+'>'+employees[i].NIP+' | '+employees[i].Name+'</option>';
                            }
                        }
                        
                    }
                    return aa;
                }

                var getDivision1 = function(selected1){
                    var aa = '';
                    for(var i=0;i< division.length;i++){
                        if (selected1 == '') {
                            aa+= '<option value="'+division[i].ID+'" '+''+'>'+division[i].Division+'</option>';
                        }
                        else
                        {
                            if (division[i].ID == selected1) {
                                aa+= '<option value="'+division[i].ID+'" '+'selected'+'>'+division[i].Division+'</option>';
                            }
                            else
                            {
                                aa+= '<option value="'+division[i].ID+'" '+''+'>'+division[i].Division+'</option>';
                            }
                        }
                        
                    }
                    return aa;
                }

                var getDivision = function(selected2){
                    var aa = '';
                    for(var i=0;i< division.length;i++){
                        if (selected2 == '') {
                            aa+= '<option value="'+division[i].ID+'" '+''+'>'+division[i].Division+'</option>';
                        }
                        else
                        {
                            if (division[i].ID == selected2) {
                                aa+= '<option value="'+division[i].ID+'" '+'selected'+'>'+division[i].Division+'</option>';
                            }
                            else
                            {
                                aa+= '<option value="'+division[i].ID+'" '+''+'>'+division[i].Division+'</option>';
                            }
                        }
                        
                    }
                    return aa;
                }

                var OPselectedTypeApprover1 = function(selected1,Hide = ''){
                    var OP =''; 
                    for (var k = 0; k < 3; k++) {
                        switch(k) {
                                    case 0:
                                        var selectedTypeApprover1 = (selected1 == 'Position') ? 'selected' : '';
                                        if (Hide != 'Position') {
                                           OP +=  '<option value ="Position" '+selectedTypeApprover1+'>Position</option>'; 
                                        }
                                        
                                        break;
                                    case 1:
                                        var selectedTypeApprover1 = (selected1 == 'Employees') ? 'selected' : '';
                                        if (Hide != 'Employees') {
                                           OP +=  '<option value ="Employees" '+selectedTypeApprover1+'>Employees</option>'; 
                                        }
                                        
                                        break;
                                    case 2:
                                       var selectedTypeApprover1 = (selected1 == 'Division') ? 'selected' : '';
                                       if (Hide != 'Division') {
                                        OP +=  '<option value ="Division" '+selectedTypeApprover1+'>Division</option>';
                                       }
                                       
                                        break;
                                }
                    }

                    return OP;
                }
                


            $('#GlobalModalLarge .modal-header').html('<h4 class="modal-title">Category Classroom</h4>');
            $('#GlobalModalLarge .modal-body').html('<div class="row">' +
                '                            <div class="col-xs-6">' +
                '                                <div class="form-group"><label>Name</label>' +
                '                                <input type="text" class="form-control" value="'+Name+'" '+''+' style="color:#333;" id="formName">' +
                '                            </div></div>' +
                '                            <div class="col-xs-6">' +
                '                                <div class="form-group"><label>NameEng</label>' +
                '                                <input type="text" class="form-control" value="'+NameEng+'" '+''+' style="color:#333;" id="formNameEng">' +
                '                            </div></div>' +
                '                        </div>'+
                '                         <div class = "row">'+
                '                           <div class = "col-xs-12">'+
                                                '<div class = "row">'+
                                                    '<div class = "col-xs-12">'+
                                                        '<label>Approver 1</label>'+
                                                    '</div>'+
                                                '</div>'+
                                                '<div class = "row" style = "margin-top : 10px">'+
                                                    '<div class = "col-xs-4">'+
                                                        '<div class = "form-group">'+
                                                            '<label>Group User</label>'+
                                                            '<select class=" form-control UserTypeApprover1">'+
                                                                '<option value = "0" selected>-- Choose Group User --</option>'+
                                                                getGroupUser(UserType1Edit)+    
                                                            '</select>'+
                                                        '</div>'+    
                                                    '</div>'+
                                                    '<div class = "col-xs-4">'+
                                                        '<div class = "form-group">'+
                                                            '<label>Category Approver</label>'+
                                                            '<select class=" form-control TypeApprover1">'+
                                                                '<option value = "0" selected>-- Choose Type Approver--</option>'+
                                                                OPselectedTypeApprover1(TypeApprover1Edit)+
                                                            '</select>'+
                                                        '</div>'+    
                                                    '</div>'+
                                                    '<div id = "AddApprover1" class = "col-xs-4"></div>'+
                                                '</div>'+
                                                '<div class = "row" style = "margin-top : 5px">'+
                                                    
                                                '</div>'+
                                            '</div>'+    
                '                        </div>'+
                '                         <div id = "AddingApprover"></div>'+
                '                           <div class = "row">'+
                        '                           <div class = "col-xs-3">'+
                        '                               <button class="btn btn-default" id = "addApprover1" style = "margin-top : 5px"><i class="icon-plus"></i> Add</button>'+
                        '                           </div>'+
                '                            </div>'+
                                            '<hr>'+
                '                         <div class = "row" style="margin-top: 10px">'+
                '                           <div class = "col-xs-12">'+
                '                               <label>Approver 2</label>' +
                '                           </div>'+
                '                           <div class = "col-xs-4 class_approver2">'+
                '                                <div class="form-group"><label>Category Approver</label>' +
                '                               <select class=" form-control TypeApprover2">'+
                '                                   <option value = "0" selected>-- Choose Type Approver--</option>'+OPselectedTypeApprover1(TypeApprover2Edit,'Position')+
                '                               </select></div>'+
                '                           </div>'+
                '<div id = "AddApprover2TypeApp" class = "col-xs-4"></div>'+
                '                           <div class = "col-xs-4">'+
                '                               <!--<button class="btn btn-default" id = "addApprover2" style = "margin-top : 23px"><i class="icon-plus"></i> Add</button>-->'+
                '                           </div>'+
                '                        </div>'+
                '                         <div id = "AddApprover2"></div>'        

                                    );
            $('#GlobalModalLarge .modal-footer').html('<button type="button" id="btnCloseCategoryClassroom" class="btn btn-default" data-dismiss="modal">Close</button>' +
                '<button type="button" class="btn btn-success" data-id="'+ID+'" data-action="'+action+'" id="btnSaveCategoryClassroom">Save</button>');
            $('#GlobalModalLarge').modal({
                'show' : true,
                'backdrop' : 'static'
            });

            $('.UserTypeApprover1').select2({
               //allowClear: true
            });
            

            $('.TypeApprover1').select2({
               //allowClear: true
            });

            $('.Approver2').select2({
               //allowClear: true
            });

            if (action=='edit') {
                // approver 1
                var aa = TypeApprover1Edit;
                console.log(aa);
                switch(aa) {
                    case 'Position':
                        Op = getPositionUser(Approver1Edit);
                        break;
                    case 'Employees':
                        Op = getEmployees(Approver1Edit);
                        break;
                    case 'Division':
                       Op = getDivision1(Approver1Edit);
                        break;
                    default :
                       Op = getDivision1(Approver1Edit);     
                }
                var Input = '<div class = "row" style="margin-top: 5px">'+
                                '<div class="col-xs-12">'+
                                    '<div class = "form-group">'+
                                        '<label>Choose Approver 1</label>'+
                                        '<select class=" form-control Approver1">'+
                                        '   <option value = "0" selected>-- No Selected --</option>'+Op+
                                        '</select>'+
                                    '</div>'+    
                                '</div>'+       
                            '</div>';

                $("#AddApprover1").html(Input); 

                $('select[tabindex!="-1"]').select2({
                    //allowClear: true
                });
                selected1 = JsonApprover1;
                for (var i = 1; i < selected1.length; i++) {
                    var aa = selected1[i].TypeApprover;
                    switch(aa) {
                        case 'Position':
                            Op = getPositionUser(selected1[i].Approver);
                            break;
                        case 'Employees':
                            Op = getEmployees(selected1[i].Approver);
                            break;
                        case 'Division':
                           Op = getDivision1(selected1[i].Approver);
                            break;
                    }
                    var InputApprover1 = '<div class = "row" style="margin-left : 0px">'+
                                    '<div class="col-xs-12">'+
                                        '<div class = "form-group">'+
                                            '<label>Choose Approver 1</label>'+
                                            '<select class=" form-control Approver1">'+
                                            '   <option value = "0" selected>-- No Selected --</option>'+Op+
                                            '</select>'+
                                        '</div>'+    
                                    '</div>'+       
                                '</div>';


                    var Input = '<div class="thumbnail" style="height: 100px;margin-top : 10px"><div class = "row" style = "margin-top : 10px;margin-left : 0px">'+
                                    '<div class = "col-xs-3">'+
                                        '<div class = "form-group">'+
                                            '<label>Group User</label>'+
                                            '<select class=" form-control UserTypeApprover1">'+
                                                '<option value = "0" selected>-- Choose Group User --</option>'+
                                                getGroupUser(selected1[i].UserType)+    
                                            '</select>'+
                                        '</div>'+    
                                    '</div>'+
                                    '<div class = "col-xs-3">'+
                                        '<div class = "form-group">'+
                                            '<label>Category Approver</label>'+
                                            '<select class=" form-control TypeApprover1">'+
                                                '<option value = "0" selected>-- Choose Type Approver--</option>'+
                                                OPselectedTypeApprover1(selected1[i].TypeApprover)+     
                                            '</select>'+
                                        '</div>'+    
                                    '</div>'+
                                    '<div class = "col-xs-4 AppendApprover1">'+InputApprover1+'</div>'+ // exit row
                                    '<div class="col-xs-2">'+
                                        '<button type="button" class="btn btn-danger btn-deleteAuto" style = "margin-top : 23px"> <i class="fa fa-trash" aria-hidden="true"></i> Delete</button>'+
                                    '</div>'+
                                '</div>'+
                            '</div>';

                    $("#AddingApprover").append(Input); 

                    $('select[tabindex!="-1"]').select2({
                        //allowClear: true
                    });

                    $(".btn-deleteAuto").click(function(){
                        $( this )
                          .closest( 'div[class="thumbnail"]'  )
                          .remove();
                        // $(this)
                        //   .parentsUntil( 'div[class="thumbnail"]' ).remove();
                    })

                }

                // approver 2
                var aa = TypeApprover2Edit;
                switch(aa) {
                    case 'Employees':
                        Op = getEmployees(Approver2Edit);
                        break;
                    case 'Division':
                       Op = getDivision1(Approver2Edit);
                        break;
                    default :
                       Op = getDivision1(Approver2Edit);     
                }
                var Input = '<div class = "row" style="margin-top: 5px">'+
                                '<div class="col-xs-12">'+
                                    '<div class = "form-group">'+
                                        '<label>Choose Approver 2</label>'+
                                        '<select class=" form-control Approver2">'+
                                        '   <option value = "0" selected>-- No Selected --</option>'+Op+
                                        '</select>'+
                                    '</div>'+    
                                '</div>'+       
                            '</div>';

                $("#AddApprover2TypeApp").html(Input); 

                $('select[tabindex!="-1"]').select2({
                    //allowClear: true
                });
                selected1 = JsonApprover2;
                for (var i = 1; i < selected1.length; i++) {
                    var aa = selected1[i].TypeApprover;
                    switch(aa) {
                        case 'Position':
                            Op = getPositionUser(selected1[i].Approver);
                            break;
                        case 'Employees':
                            Op = getEmployees(selected1[i].Approver);
                            break;
                        case 'Division':
                           Op = getDivision1(selected1[i].Approver);
                            break;
                    }
                    var InputApprover1 = '<div class = "row" style="margin-left : 0px">'+
                                    '<div class="col-xs-12">'+
                                        '<div class = "form-group">'+
                                            '<label>Choose Approver 1</label>'+
                                            '<select class=" form-control Approver2">'+
                                            '   <option value = "0" selected>-- No Selected --</option>'+Op+
                                            '</select>'+
                                        '</div>'+    
                                    '</div>'+       
                                '</div>';


                    var Input = '<div class="thumbnail" style="height: 100px;margin-top : 10px"><div class = "row" style = "margin-top : 10px;margin-left : 0px">'+
                                    '<div class = "col-xs-3">'+
                                        '<div class = "form-group">'+
                                            '<label>Category Approver</label>'+
                                            '<select class=" form-control TypeApprover1">'+
                                                '<option value = "0" selected>-- Choose Type Approver--</option>'+
                                                OPselectedTypeApprover1(selected1[i].TypeApprover)+     
                                            '</select>'+
                                        '</div>'+    
                                    '</div>'+
                                    '<div class = "col-xs-4 AppendApprover1">'+InputApprover1+'</div>'+ // exit row
                                    '<div class="col-xs-2">'+
                                        '<button type="button" class="btn btn-danger btn-deleteAuto" style = "margin-top : 23px"> <i class="fa fa-trash" aria-hidden="true"></i> Delete</button>'+
                                    '</div>'+
                                '</div>'+
                            '</div>';

                    $("#AddApprover2").append(Input); 

                    $('select[tabindex!="-1"]').select2({
                        //allowClear: true
                    });

                    $(".btn-deleteAuto").click(function(){
                        $( this )
                          .closest( 'div[class="thumbnail"]'  )
                          .remove();
                        // $(this)
                        //   .parentsUntil( 'div[class="thumbnail"]' ).remove();
                    })

                }
                
            } // edit


            $("#addApprover1").click(function(){
                var Input = '<div class="thumbnail" style="height: 100px;margin-top : 10px"><div class = "row" style = "margin-top : 10px;margin-left : 0px">'+
                                '<div class = "col-xs-3">'+
                                    '<div class = "form-group">'+
                                        '<label>Group User</label>'+
                                        '<select class=" form-control UserTypeApprover1">'+
                                            '<option value = "0" selected>-- Choose Group User --</option>'+
                                            getGroupUser('')+    
                                        '</select>'+
                                    '</div>'+    
                                '</div>'+
                                '<div class = "col-xs-3">'+
                                    '<div class = "form-group">'+
                                        '<label>Category Approver</label>'+
                                        '<select class=" form-control TypeApprover1">'+
                                            '<option value = "0" selected>-- Choose Type Approver--</option>'+
                                            '<option value ="Position">Position</option>'+
                                            '<option value = "Employees">Employees</option>'+
                                            '<option value = "Division">Division</option>'+      
                                        '</select>'+
                                    '</div>'+    
                                '</div>'+
                                '<div class = "col-xs-4 AppendApprover1"></div>'+
                                '<div class="col-xs-2">'+   
                                    '<button type="button" class="btn btn-danger btn-deleteAuto" style = "margin-top : 23px"> <i class="fa fa-trash" aria-hidden="true"></i> Delete</button>'+
                                '</div>'+
                        '</div></div>';

                $("#AddingApprover").append(Input); 

                $('select[tabindex!="-1"]').select2({
                    //allowClear: true
                });

                $(".btn-deleteAuto").click(function(){
                    $( this )
                      .closest( 'div[class="thumbnail"]'  )
                      .remove();
                    // $(this)
                    //   .parentsUntil( 'div[class="thumbnail"]' ).remove();
                })

                $(".TypeApprover1").change(function(){
                    var aa = $(this).val();
                    var Op = getDivision1('');
                    switch(aa) {
                        case 'Position':
                            Op = getPositionUser('');
                            break;
                        case 'Employees':
                            Op = getEmployees('');
                            break;
                        case 'Division':
                           Op = getDivision1('');
                            break;
                    }
                    var Input = '<div class = "row" style="margin-left : 0px">'+
                                    '<div class="col-xs-12">'+
                                        '<div class = "form-group">'+
                                            '<label>Choose Approver 1</label>'+
                                            '<select class=" form-control Approver1">'+
                                            '   <option value = "0" selected>-- No Selected --</option>'+Op+
                                            '</select>'+
                                        '</div>'+    
                                    '</div>'+       
                                '</div>';

                    $( this )
                      .closest( 'div[class="thumbnail"]'  ).find('.AppendApprover1').html(Input);          
                    // $("#AddingApprover").append(Input); 

                    $('select[tabindex!="-1"]').select2({
                        //allowClear: true
                    });
                }) 

            })

            $("#addApprover2").click(function(){
                var Input = '<div class = "row" style="margin-top: 5px">'+
                                '<div class="col-xs-12">'+
                                '<div class = "row" style = "margin-left : 0px;">'+    
                                    '<div class="col-xs-4 class_approver2" style = "margin-left : -15px">'+
                                        '                               <select class=" form-control TypeApprover2">'+
                                        '                                   <option value = "0" selected>-- No Selected --</option>'+OPselectedTypeApprover1('','Position')+
                                        '                               </select>'+
                                    '</div>'+
                                    '<div class = "col-xs-4 AddApprover2TypeApp"></div>'+
                                    '<div class="col-xs-4">'+
                                        '<button type="button" class="btn btn-danger btn-deleteAuto2"> <i class="fa fa-trash" aria-hidden="true"></i> Delete</button>'+
                                    '</div>'+
                                '</div>'+    
                                '</div>'+       
                            '</div>';

                $("#AddApprover2").append(Input); 

                $('select[tabindex!="-1"]').select2({
                    //allowClear: true
                });

                $(".TypeApprover2").change(function(){
                    var aa = $(this).val();
                    var Op = getDivision1('');
                    switch(aa) {
                        case 'Position':
                            Op = getPositionUser('');
                            break;
                        case 'Employees':
                            Op = getEmployees('');
                            break;
                        case 'Division':
                           Op = getDivision1('');
                            break;
                    }
                    var Input = '<div class = "row" style="margin-left : -10px">'+
                                    '<div class="col-xs-12">'+
                                        '<div class = "form-group">'+
                                            // '<label>Choose Approver 2</label>'+
                                            '<select class=" form-control Approver2">'+
                                            '   <option value = "0" selected>-- No Selected --</option>'+Op+
                                            '</select>'+
                                        '</div>'+    
                                    '</div>'+       
                                '</div>';

                    $( this )
                      .closest( '.row'  ).find('.AddApprover2TypeApp').html(Input);          
                    // $("#AddingApprover").append(Input); 

                    $('select[tabindex!="-1"]').select2({
                        //allowClear: true
                    });
                }) 

                $(".btn-deleteAuto2").click(function(){
                    $( this )
                      .closest( 'div[class="row"]'  ).remove();
                    // $(this)
                    //   .parentsUntil( 'div[class="row"]' ).remove();
                })      
            })

            $(".TypeApprover1:first").change(function(){
                var aa = $(this).val();
                console.log(aa);
                var Op = getDivision1('');
                switch(aa) {
                    case 'Position':
                        Op = getPositionUser('');
                        break;
                    case 'Employees':
                        Op = getEmployees('');
                        break;
                    case 'Division':
                       Op = getDivision1('');
                        break;
                }
                var Input = '<div class = "row" style="margin-top: 5px">'+
                                '<div class="col-xs-12">'+
                                    '<div class = "form-group">'+
                                        '<label>Choose Approver 1</label>'+
                                        '<select class=" form-control Approver1">'+
                                        '   <option value = "0" selected>-- No Selected --</option>'+Op+
                                        '</select>'+
                                    '</div>'+    
                                '</div>'+       
                            '</div>';

                $("#AddApprover1").html(Input); 

                $('select[tabindex!="-1"]').select2({
                    //allowClear: true
                });
            })  
             
            $(".TypeApprover2:first").change(function(){
                 var aa = $(this).val();
                 console.log(aa);
                 var Op = getDivision1('');
                 switch(aa) {
                     case 'Position':
                         Op = getPositionUser('');
                         break;
                     case 'Employees':
                         Op = getEmployees('');
                         break;
                     case 'Division':
                        Op = getDivision1('');
                         break;
                 }
                 var Input = '<div class = "row" style="margin-top: 0px">'+
                                 '<div class="col-xs-12">'+
                                     '<div class = "form-group">'+
                                         '<label>Choose Approver 2</label>'+
                                         '<select class=" form-control Approver2">'+
                                         '   <option value = "0" selected>-- No Selected --</option>'+Op+
                                         '</select>'+
                                     '</div>'+    
                                 '</div>'+       
                             '</div>';

                 $("#AddApprover2TypeApp").html(Input); 

                 $('select[tabindex!="-1"]').select2({
                     //allowClear: true
                 });
            })  

        }
        else {
            $('#NotificationModal .modal-body').html('<div style="text-align: center;">Hapus <b style="color: red;">'+Name+' / '+NameEng+'</b>  ?? | ' +
                '<button type="button" id="btnDeleteCategoryClassroom" data-id="'+ID+'" class="btn btn-primary" style="margin-right: 5px;">Yes</button>' +
                '<button type="button" id="btnTidak" class="btn btn-default" data-dismiss="modal">No</button>' +
                '</div>');
            $('#NotificationModal').modal('show');
        }

    });

    $(document).on('click','#btnSaveCategoryClassroom',function () {

        var action = $(this).attr('data-action');
        var ID = $(this).attr('data-id');

        var process = true;

        var Name = $('#formName').val(); process = (Name=='') ? errorInput('#formName') : true ;
        var NameEng = $('#formNameEng').val(); process = (Name=='') ? errorInput('#formNameEng') : true ;

        // get approver 1
           var Approver1 = function(){
                var arr_result = {
                    Status : false,
                    result : []
                };

                var UserTypeApprover1 = [];
                $(".UserTypeApprover1").each(function(){
                    UserTypeApprover1.push($(this).val());
                })

                var TypeApprover1 = [];
                $(".TypeApprover1").each(function(){
                    TypeApprover1.push($(this).val());
                })

                var Approver1 = [];
                $(".Approver1").each(function(){
                    Approver1.push($(this).val());
                })

                // CHECK Group User double
                var find = true;
                // for (var l = 0; l < UserTypeApprover1.length; l++) {
                //     for (var i = l+1; i < UserTypeApprover1.length; i++) {
                //         if (UserTypeApprover1[i] ==  UserTypeApprover1[l]) {
                //             find = false;
                //             break;
                //         }
                //     }
                // }

                if (find) {
                    var CountArr = UserTypeApprover1.length;
                    if (TypeApprover1.length == CountArr && Approver1.length == CountArr) {
                        var temp = [];
                        for (var i = 0; i < CountArr; i++) {
                            if (UserTypeApprover1[i] != 0 && TypeApprover1[i] != 0 && Approver1[i] != 0) {
                                var Obtemp = {
                                    UserType : UserTypeApprover1[i],
                                    TypeApprover : TypeApprover1[i],
                                    Approver : Approver1[i],
                                }
                                temp.push(Obtemp);
                            }
                            else
                            {
                                return arr_result;
                            }
                            
                        }
                        arr_result.Status = true;
                        arr_result.result = temp;
                    }
                    else
                    {
                        return arr_result;
                    }
                }

                return arr_result;

           }

        var getApprover1 = [];
        var getF = Approver1();
            if (getF.Status) {
                getApprover1 = getF.result;
            }
        var Approver1 = getApprover1;     

        var Approver2 = [];
        $(".TypeApprover2").each(function(){
            if (!$(this).closest('.row').find('.Approver2').length) {
                var Approver = '';
            }
            else
            {
                var Approver = $(this).closest('.row').find('.Approver2').val();
            }
            var data = {
                TypeApprover : $(this).val(),
                Approver : Approver,
            }
            Approver2.push(data);
        })
        // $(".Approver2").each(function(){
        //     if ($(this).val() != 0) {
        //         Approver2.push($(this).val());
        //     }
            
        // })
        
        if(Name!='' && NameEng != '' && Approver1.length > 0 && Approver2.length > 0){
            $('#formName,#formNameEng').prop('disabled',true);
            loading_button('#btnSaveCategoryClassroom');
            loading_page('#viewCategoryClassroom');

            var data = {
                action : action,
                ID : ID,
                formData : {
                    Name : Name,
                    NameEng : NameEng,
                },
                Approver1 : Approver1,
                Approver2 : Approver2,
            };

            var token = jwt_encode(data,'UAP)(*');
            var url = base_url_js+"api/__crudCategoryClassroomVreservation";

            $.post(url,{token:token},function (data_result) {
                $('#GlobalModalLarge').modal('hide');
                loadDataCategoryClassroom();

            });

        } else {
            toastr.error('Error input. Please check','Error!');
        }
    });

    $(document).on('click','#btnDeleteCategoryClassroom',function () {
        var ID = $(this).attr('data-id');
        var token = jwt_encode({action:'delete',ID:ID},'UAP)(*');
        var url = base_url_js+"api/__crudCategoryClassroomVreservation";

        $('#btnTidak').prop('disabled',true);
        loading_buttonSm('#btnDeleteCategoryClassroom');
        $.post(url,{token:token},function () {
            loadDataCategoryClassroom();
            setTimeout(function () {
                toastr.success('Data Terhapus','Success!');
                $('#NotificationModal').modal('hide');
            });
        });
    });

    function loadDataClassroom() {
        var token = jwt_encode({action:'read'},"UAP)(*");
        var url = base_url_js+'api/__crudClassroomVreservation';
        $.post(url,{token:token},function (json_result) {
            // console.log(json_result);

            if(json_result.length>0){
                $('#viewClassroom').html('<table class="table table-bordered" id="tbClassroom">' +
                    '                        <thead>' +
                    '                        <tr>' +
                    '                            <th class="th-center" style="width:5px;">No</th>' +
                    '                            <th class="th-center" style="width: ">Category</th>' +
                    '                            <th class="th-center" style="width: ">Desc</th>' +
                    // '                            <th class="th-center">Seat</th>' +
                    '                            <th class="th-center">Seat No</th>' +
                    // '                            <th class="th-center">Seat For Exam</th>' +
                    '                            <th class="th-center">Exam Seat No</th>' +
                    // '                            <th class="th-center">Deret For Exam</th>' +
                    '                            <th class="th-center">Exam Seat Row</th>' +
                    '                            <th class="th-center">Lecture Desk</th>' +
                    '                            <th class="th-center">Layout</th>' +
                    '                            <th class="th-center" style="width: 110px;">Action</th>' +
                    '                        </tr>' +
                    '                        </thead>' +
                    '                        <tbody id="dataClassroom"></tbody>' +
                    '                    </table>');

                var tr = $('#dataClassroom');
                var no=1;
                for(var i=0;i<json_result.length;i++){
                    var data = json_result[i];

                    $('#totalRoom').text(json_result.length);
                    tr.append('<tr>' +
                        '<td class="td-center">'+(no++)+'</td>' +
                        '<td class="td-center">'+data.NameEng+'</td>' +
                        '<td class="td-center">'+data.Room+'</td>' +
                        '<td class="td-center">'+data.Seat+'</td>' +
                        '<td class="td-center">'+data.SeatForExam+'</td>' +
                        '<td class="td-center">'+data.DeretForExam+'</td>' +
                        '<td class="td-center">'+data.LectureDesk+'</td>' +
                        '<td class="td-center">'+'<a href="'+base_url_js+'fileGetAny/vreservation-'+data.Layout+'" target="_blank"></i>Click Default Layout</a>'+'</td>' +
                        '<td class="td-center">' +
                        '<button class="btn btn-default btn-default-success btn-classroom btn-edit" data-action="edit" data-form="'+data.ID+'|'+data.Room+'|'+data.Seat+'|'+data.SeatForExam+'|'+data.DeretForExam+'|'+data.LectureDesk+'|'+data.ID_CategoryRoom+'"><i class="fa fa-pencil" aria-hidden="true"></i></button> ' +
                        ' <button class="btn btn-default btn-default-danger btn-classroom btn-delete" data-action="delete" data-form="'+data.ID+'|'+data.Room+'"><i class="fa fa-trash-o" aria-hidden="true"></i></button>' +
                        '</td>' +
                        '</tr>');
                }

                $('#tbClassroom').DataTable({
                    "sDom": "<'row'<'dataTables_header clearfix'<'col-md-3'><'col-md-9'f>r>>t<'row'<'dataTables_footer clearfix'<'col-md-12'p>>>", // T is new
                    'bLengthChange' : false,
                    'bInfo' : false,
                    'pageLength' : 7
                });

                $('.dataTables_header .col-md-3').html('<button class="btn btn-default btn-default-primary btn-classroom btn-add" data-action="add"><i class="fa fa-plus-circle fa-right" aria-hidden="true"></i> Add Room</button>');
            }


        });
    }


    function loadDataCategoryClassroom() {
        var token = jwt_encode({action:'read'},"UAP)(*");
        var url = base_url_js+'api/__crudCategoryClassroomVreservation';
        $.post(url,{token:token},function (json_result) {
            // console.log(json_result);
            if(json_result.length>0){
                $('#viewCategoryClassroom').html('<table class="table table-bordered" id="tbCategoryClassroom">' +
                    '                        <thead>' +
                    '                        <tr>' +
                    '                            <th class="th-center" style="width:5px;">No</th>' +
                    '                            <th class="th-center" style="width:15px;">Name</th>' +
                    '                            <th class="th-center" style="width:15px;">Name Eng</th>' +
                    '                            <th class="th-center" style="width:15px;">Approver 1</th>' +
                    '                            <th class="th-center" style="width:15px;">Approver 2(Div)</th>' +
                    '                            <th class="th-center" style="width: 110px;">Action</th>' +
                    '                        </tr>' +
                    '                        </thead>' +
                    '                        <tbody id="dataCategoryClassroom"></tbody>' +
                    '                    </table>');

                var tr = $('#dataCategoryClassroom');
                var no=1;
                for(var i=0;i<json_result.length;i++){
                    var data = json_result[i];
                    tr.append('<tr>' +
                        '<td class="td-center">'+(no++)+'</td>' +
                        '<td class="td-left">'+data.Name+'</td>' +
                        '<td class="td-left">'+data.NameEng+'</td>' +
                        '<td class="td-left">'+data.Approver1+'</td>' +
                        '<td class="td-left">'+data.Approver2+'</td>' +
                        '<td class="td-left">' +
                        '<button class="btn btn-default btn-default-success btn-Categoryclassroom btn-edit" data-action="edit" data-form="'+data.ID+'|'+data.Name+'|'+data.NameEng+'|'+''+'|'+''+'" Approver1_ori = "'+data.Approver1_ori+'" Approver2_ori = "'+data.Approver2_ori+'"><i class="fa fa-pencil" aria-hidden="true"></i></button> ' +
                        ' <button class="btn btn-default btn-default-danger btn-Categoryclassroom btn-delete" data-action="delete" data-form="'+data.ID+'|'+data.Name+'|'+data.NameEng+'|'+data.Approver1+'|'+data.Approver2+'"><i class="fa fa-trash-o" aria-hidden="true"></i></button>' +
                        '</td>' +
                        '</tr>');
                }

                $('#tbCategoryClassroom').DataTable({
                    'pageLength' : 5
                });
            }
        });
    }
</script>