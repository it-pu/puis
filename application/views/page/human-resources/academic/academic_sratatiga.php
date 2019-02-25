
<div class="row">
            <div class="col-md-6" style="border-right: 1px solid #afafafb5;">

                    <div class="col-xs-12 id="subsesi">
                        <div class="form-group">
                            <div class="thumbnail" style="padding: 10px;text-align: left;">
                                <h4>Data Academic Transcript S3 </h4>
                               
                                <div class="row"> 
                                    
                                    <div class="col-xs-12">
                                        <div class="form-group">
                                            <label>Name Univesity</label>
                                            <input class="form-control" id="formNameUnivS3">
                                        </div>
                                    </div>

                                    <div class="col-xs-6">
                                        <div class="form-group">
                                            <label>No. Ijazah S3</label>
                                            <input class="form-control" id="formNoIjazahS3">
                                        </div>
                                    </div>

                                    <div class="col-xs-6">
                                        <div class="form-group">
                                            <label>Date & Year Ijazah</label>
                                            <input class="form-control" id="formIjazahDate" autocomplete="off">
                                        </div>
                                    </div>

                                    <div class="col-xs-6">
                                        <div class="form-group">
                                            <label>Major</label>
                                            <input class="form-control" id="formMajorS3">
                                        </div>
                                    </div>
                                    <div class="col-xs-6">
                                        <div class="form-group">
                                            <label>Program Study </label>
                                            <input class="form-control" id="formStudyS3">
                                        </div>
                                    </div>
                                    <div class="col-xs-6">
                                        <div class="form-group">
                                            <label>Grade/ IPK</label>
                                            <input class="form-control" id="gradeS3" maxlength="4">
                                        </div>
                                    </div>
                                    <div class="col-xs-3">
                                        <div class="form-group">
                                            <label>Total Credit</label>
                                            <input class="form-control" id="totalCreditS3" maxlength="3">
                                        </div>
                                    </div>

                                    <div class="col-xs-3">
                                        <div class="form-group">
                                            <label>Total Semester</label>
                                                <div class="input-group number-spinner">
                                                <span class="input-group-btn">
                                                    <button class="btn btn-default" data-dir="dwn"><span class="glyphicon glyphicon-minus"></span></button>
                                                </span>
                                                <input type="text" class="form-control text-center" id="TotSemesterS3" value="0" disabled>
                                                    <span class="input-group-btn">
                                                        <button class="btn btn-default" data-dir="up"><span class="glyphicon glyphicon-plus"></span></button>
                                                    </span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-xs-6">
                                    <div class="form-group">
                                        <label>Ijazah</label>
                                            <form id="tagFM_IjazahS3" enctype="multipart/form-data" accept-charset="utf-8" method="post" action="">
                                                    
                                                        <div class="form-group">
                                                            <label class="btn btn-sm btn-default btn-default-warning btn-upload">
                                                                <i class="fa fa-upload margin-right"></i> Upload File
                                                                <input type="file" id="fileIjazah" name="userfile" class="upload_files" style="display: none;" data-fm="tagFM_IjazahS3" accept="application/pdf">
                                                            </label>
                                                    <p style="font-size: 12px;color: #FF0000;">*) Only PDF Files Max Size 5 MB</p>
                                            </div></form>
                                            <div id="element1">Review Ijazah : </div>
                                             
                                        </div>
                                    </div>

                                    <div class="col-xs-6">
                                    <div class="form-group">
                                        <label>Transcript</label>
                                            <form id="tagFM_TranscriptS3" enctype="multipart/form-data" accept-charset="utf-8" method="post" action="">
                                                    <input id="formPhoto" class="hide" value="" hidden />
                                                        <div class="form-group">
                                                            <label class="btn btn-sm btn-default btn-default-warning btn-upload">
                                                                <i class="fa fa-upload margin-right"></i> Upload File
                                                                <input type="file" id="fileTranscript" name="userfile" class="uploadPhotoEmp" style="display: none;" accept="application/pdf">
                                                        </label>
                                                 <p style="font-size: 12px;color: #FF0000;">*) Only PDF Files Max Size 5 MB</p>
                                            </div></form>
                                            <div id="element2">Review Transcript : </div>
                                        </div>
                                    </div>

                                  
                                </div>
                                <div class="row">
                                   <div class="col-md-12" style="text-align: right;">
                                            <hr/>
                                            <button class="btn btn-success" id="btnSave">Save</button>
                                        </div> 
                                </div>
                            </div>

                        </div>
                    </div>

                    <!-- <span id="bodyAddSesi"></span> -->
            </div>
 </div>

<script>
    $(document).ready(function () {
    $('#formIjazahDate').datepicker({
      dateFormat : 'yy-mm-dd',
      changeMonth : true,
      changeYear : true,
      autoclose: true,
      todayHighlight: true,
      uiLibrary: 'bootstrap'
    });
});

    $(document).on('click', '.number-spinner button', function () {    
        var btn = $(this),
        oldValue = btn.closest('.number-spinner').find('input').val().trim(),
        newVal = 0;
    
    if (btn.attr('data-dir') == 'up') {
        newVal = parseInt(oldValue) + 1;
    } else {
        if (oldValue > 1) {
            newVal = parseInt(oldValue) - 1;
        } else {
            newVal = 1;
        }
    }
    btn.closest('.number-spinner').find('input').val(newVal);
});
</script>

 <script type="text/javascript">
    $('#fileIjazah').change(function (event) {
        $('#element1').empty();
        var file = URL.createObjectURL(event.target.files[0]);
        $('#element1').append('<br/><iframe src="' + file + '" style="width:200px; height:100;" frameborder="0"></iframe>' );
    });

    $('#fileTranscript').change(function (event) {
        $('#element2').empty();
        var file = URL.createObjectURL(event.target.files[0]);
        $('#element2').append('<br/><iframe src="' + file + '" style="width:200px; height:100;" frameborder="0"></iframe>');
    });

 </script>
 <script>
    $('#btnSave').click(function () {
        var NIP = $('#formNIP').val();
        $('#NotificationModal .modal-body').html('<div style="text-align: center;">' +
            'Pastikan Data & File Academic S3 tidak salah! <br/>' +
            'Periksa kembali data yang di input sebelum di Save. ' +
            '<hr/>' +
            '<button type="button" class="btn btn-default" id="btnCloseEmployees" data-dismiss="modal">Close</button> | ' +
            '<button type="button" class="btn btn-success" id="btnSubmitEmployees">Submit</button>' +
            '</div> ');

        $('#NotificationModal').modal({
            'backdrop' : 'static',
            'show' : true
        });
    });

    $(document).on('click','#btnSubmitEmployees',function () {
        saveEmployeesS3();
    });


    function file_validation(ID_element)
    {
        var files = $('#'+ID_element)[0].files;
        var error = '';
        var msgStr = '';
       var name = files[0].name;
        console.log(name);
        var extension = name.split('.').pop().toLowerCase();
        if(jQuery.inArray(extension, ['xlsx']) == -1)
        {
         msgStr += 'Invalid Type File<br>';
        }

        var oFReader = new FileReader();
        oFReader.readAsDataURL(files[0]);
        var f = files[0];
        var fsize = f.size||f.fileSize;
        console.log(fsize);

        if(fsize > 2000000) // 2mb
        {
         msgStr += 'File Size is very big<br>';
        }

        if (msgStr != '') {
          toastr.error(msgStr, 'Failed!!');
          return false;
        }
        else
        {
          return true;
        }
    }


    function saveEmployeesS3() {

        var formNIP = '<?php echo $NIP; ?>';
        var formNoIjazahS3 = $('#formNoIjazahS3').val();
        var formNameUnivS3 = $('#formNameUnivS3').val();
        var formIjazahDate = $('#formIjazahDate').val();
        var formMajorS3 = $('#formMajorS3').val();
        var formStudyS3 = $('#formStudyS3').val();
        var gradeS3 = $('#gradeS3').val();
        var totalCreditS3 = $('#totalCreditS3').val();
        var TotSemesterS3 = $('#TotSemesterS3').val();

        var min=100; 
        var max=999;  
        var random =Math.floor(Math.random() * (+max - +min)) + +min; 

        var type = 'IjazahS3';
        var ext = 'PDF';
        var fileName = type+'_'+NIP+'_'+random+'.'+ext;

        var TypeTrans = 'TranscriptS3';
        var fileName_Transcript = TypeTrans+'_'+NIP+'_'+random+'.'+ext;

        var oFile = document.getElementById("fileIjazah").files[0]; 
        var xFile = document.getElementById("fileTranscript").files[0]; 

        if (oFile.size > 5242880 || xFile.size > 5242880) {  // 5 mb for bytes.
                toastr.error('File Maksimum size 5 Mb!','Error');
                $('#NotificationModal').modal('hide');
                //return;
        } 
            else {

                if(formNIP!=null && formNIP!=''
                    && formNoIjazahS3!='' && formNoIjazahS3!=null
                    && formNameUnivS3!='' && formNameUnivS3!=null
                    && formIjazahDate!='' && formIjazahDate!=null
                    && formMajorS3!='' && formMajorS3!=null
                    && formStudyS3!='' && formStudyS3!=null
                    && gradeS3!='' && gradeS3!=null
                    && totalCreditS3!='' && totalCreditS3!=null
                    && TotSemesterS3!='' && TotSemesterS3!=null 
                    ){ 
                    loading_button('#btnSubmitEmployees');
                    $('#btnCloseEmployees').prop('disabled',true);

                    var data = {
                        //action : 'addEmployees',
                        action : 'addAcademicS3',
                        formInsert : {
                                NIP : formNIP,
                                NoIjazah : formNoIjazahS3,
                                NameUniversity : formNameUnivS3,
                                IjazahDate : formIjazahDate,
                                Major : formMajorS3,
                                ProgramStudy : formStudyS3,
                                Grade : gradeS3,
                                TotalCredit : totalCreditS3,
                                TotalSemester : TotSemesterS3,
                                file_trans : fileName_Transcript,
                                fileName : fileName }
                            };

                    var token = jwt_encode(data,'UAP)(*');
                    var url = base_url_js+'api/__crudAcademicData';
                    $.post(url,{token:token},function (result) {
                    
                        if(result==0 || result=='0'){
                            toastr.error('NIK / NIP is exist','Error');
                        } else {  //if success save data
                
                                var formData = new FormData( $("#tagFM_IjazahS3")[0]);
                                //alert(fileName);
                                var url = base_url_js+'human-resources/upload_academic?fileName='+fileName+'&c='+type+'&u='+NIP;
                                            
                                    $.ajax({
                                            url : url,  // Controller URL
                                            type : 'POST',
                                            data : formData,
                                            async : false,
                                            cache : false,
                                            contentType : false,
                                            processData : false,
                                            success : function(data) {
                                            }
                                    });   

                                uploadfile_transcripts(fileName_Transcript);
                                toastr.success('Academic Data Saved','Success');

                        }
                            setTimeout(function () {
                                $('#NotificationModal').modal('hide');
                                window.location.href = '';
                            },1000);

                        });
                 }

        else {
            toastr.error('Form Masih ada yang kosong','Error');
            $('#NotificationModal').modal('hide');
            return;
        }
    }
}


function uploadfile_transcripts(fileName_Transcript) {

        var NIP = '<?php echo $NIP; ?>';                
        var type = 'TranscriptS3';
        var ext = 'PDF';
        var fileName = fileName_Transcript;
        var formData = new FormData( $("#tagFM_TranscriptS3")[0]);
        var url = base_url_js+'human-resources/upload_academic?fileName='+fileName+'&c='+type+'&u='+NIP;
                                            
            $.ajax({
                url : url,  // Controller URL
                type : 'POST',
                data : formData,
                async : false,
                cache : false,
                contentType : false,
                processData : false,
                    success : function(data) {
                        
                    }
                });   
}

</script>

<script>
    $(document).ready(function () {
        loadSelectOptionEmployeesSingle('#formEmployees','');
        $('#formPengawaS3,#formPengawas2').select2({allowClear: true});
    });

    $('#formEmployees').change(function () {
        loadDataEmployees();
    });

    function loadDataEmployees() {
        var formEmployees = $('#formEmployees').val();
        if(formEmployees!='' && formEmployees!=null){

            var url = base_url_js+'api/__crudEmployees';
            var data = {
                action : 'getEmployeesFiles',
                NIP : formEmployees.trim()
            };
            var token = jwt_encode(data,'UAP)(*');
            $.post(url,{token:token},function (jsonResult) {

                var d = jsonResult[0];

                var Photo = base_url_img_employee+''+d.Photo;

                $('#viewPhoto').html('<img src="'+Photo+'" style="width: 100%;max-width: 40px;">');
                $('#viewName').html(d.Name);
                $('#viewNIP').html(d.NIPLec);


                // Load files
                var KTP = (d.KTP!='' && d.KTP!=null) ? '<a target="_blank" href="'+base_url_js+'uploads/files/'+d.KTP+'">'+d.KTP+'</a>' : '-';
                var CV = (d.CV!='' && d.CV!=null) ? '<a target="_blank" href="'+base_url_js+'uploads/files/'+d.CV+'">'+d.CV+'</a>' : '-';
                var IjazahS3 = (d.IjazahS3!='' && d.IjazahS3!=null) ? '<a target="_blank" href="'+base_url_js+'uploads/files/'+d.IjazahS3+'">'+d.IjazahS3+'</a>' : '-';
                var TranscriptS3 = (d.TranscriptS3!='' && d.TranscriptS3!=null) ? '<a target="_blank" href="'+base_url_js+'uploads/files/'+d.TranscriptS3+'">'+d.TranscriptS3+'</a>' : '-';
                var IjazahS2 = (d.IjazahS2!='' && d.IjazahS2!=null) ? '<a target="_blank" href="'+base_url_js+'uploads/files/'+d.IjazahS2+'">'+d.IjazahS2+'</a>' : '-';
                var TranscriptS2 = (d.TranscriptS2!='' && d.TranscriptS2!=null) ? '<a target="_blank" href="'+base_url_js+'uploads/files/'+d.TranscriptS2+'">'+d.TranscriptS2+'</a>' : '-';
                var IjazahS3 = (d.IjazahS3!='' && d.IjazahS3!=null) ? '<a target="_blank" href="'+base_url_js+'uploads/files/'+d.IjazahS3+'">'+d.IjazahS3+'</a>' : '-';
                var TranscriptS3 = (d.TranscriptS3!='' && d.TranscriptS3!=null) ? '<a target="_blank" href="'+base_url_js+'uploads/files/'+d.TranscriptS3+'">'+d.TranscriptS3+'</a>' : '-';
                var SP_Dosen = (d.SP_Dosen!='' && d.SP_Dosen!=null) ? '<a target="_blank" href="'+base_url_js+'uploads/files/'+d.SP_Dosen+'">'+d.SP_Dosen+'</a>' : '-';
                var SK_Dosen = (d.SK_Dosen!='' && d.SK_Dosen!=null) ? '<a target="_blank" href="'+base_url_js+'uploads/files/'+d.SK_Dosen+'">'+d.SK_Dosen+'</a>' : '-';
                var SK_Pangkat = (d.SK_Pangkat!='' && d.SK_Pangkat!=null) ? '<a target="_blank" href="'+base_url_js+'uploads/files/'+d.SK_Pangkat+'">'+d.SK_Pangkat+'</a>' : '-';
                var SK_JJA = (d.SK_JJA!='' && d.SK_JJA!=null) ? '<a target="_blank" href="'+base_url_js+'uploads/files/'+d.SK_JJA+'">'+d.SK_JJA+'</a>' : '-';


                $('#viewKTP').html(KTP);
                $('#viewCV').html(CV);
                $('#viewIjazahS3').html(IjazahS3);
                $('#viewTranscriptS3').html(TranscriptS3);
                $('#viewIjazahS2').html(IjazahS2);
                $('#viewTranscriptS2').html(TranscriptS2);
                $('#viewIjazahS3').html(IjazahS3);
                $('#viewTranscriptS3').html(TranscriptS3);
                $('#viewSP_Dosen').html(SP_Dosen);
                $('#viewSK_Dosen').html(SK_Dosen);
                $('#viewSK_Pangkat').html(SK_Pangkat);
                $('#viewSK_JJA').html(SK_JJA);



                var btnKTP = (d.KTP!='' && d.KTP!=null) ? false : true;
                var btnCV = (d.CV!='' && d.CV!=null) ? false : true;
                var btnIjazahS3 = (d.IjazahS3!='' && d.IjazahS3!=null) ? false : true;
                var btnTranscriptS3 = (d.TranscriptS3!='' && d.TranscriptS3!=null) ? false : true;
                var btnIjazahS2 = (d.IjazahS2!='' && d.IjazahS2!=null) ? false : true;
                var btnTranscriptS2 = (d.TranscriptS2!='' && d.TranscriptS2!=null) ? false : true;
                var btnIjazahS3 = (d.IjazahS3!='' && d.IjazahS3!=null) ? false : true;
                var btnTranscriptS3 = (d.TranscriptS3!='' && d.TranscriptS3!=null) ? false : true;
                var btnSP_Dosen = (d.SP_Dosen!='' && d.SP_Dosen!=null) ? false : true;
                var btnSK_Dosen = (d.SK_Dosen!='' && d.SK_Dosen!=null) ? false : true;
                var btnSK_Pangkat = (d.SK_Pangkat!='' && d.SK_Pangkat!=null) ? false : true;
                var btnSK_JJA = (d.SK_JJA!='' && d.SK_JJA!=null) ? false : true;


                $('#btnDelete_KTP').prop('disabled',btnKTP).attr('data-file',d.KTP);
                $('#btnDelete_CV').prop('disabled',btnCV).attr('data-file',d.CV);
                //$('#btnDelete_IjazahS3').prop('disabled',btnIjazahS3).attr('data-file',d.IjazahS3);
                $('#btnDelete_TranscriptS3').prop('disabled',btnTranscriptS3).attr('data-file',d.TranscriptS3);
                $('#btnDelete_IjazahS2').prop('disabled',btnIjazahS2).attr('data-file',d.IjazahS2);
                $('#btnDelete_TranscriptS2').prop('disabled',btnTranscriptS2).attr('data-file',d.TranscriptS2);
                $('#btnDelete_IjazahS3').prop('disabled',btnIjazahS3).attr('data-file',d.IjazahS3);
                $('#btnDelete_TranscriptS3').prop('disabled',btnTranscriptS3).attr('data-file',d.TranscriptS3);
                $('#btnDelete_SP_Dosen').prop('disabled',btnSP_Dosen).attr('data-file',d.SP_Dosen);
                $('#btnDelete_SK_Dosen').prop('disabled',btnSK_Dosen).attr('data-file',d.SK_Dosen);
                $('#btnDelete_SK_Pangkat').prop('disabled',btnSK_Pangkat).attr('data-file',d.SK_Pangkat);
                $('#btnDelete_SK_JJA').prop('disabled',btnSK_JJA).attr('data-file',d.SK_JJA);


            });
        }
    }

    //$('.upload_files').change(function () {
    function uploadXDSD_files(fileName) {

        var NIP = '<?php echo $NIP; ?>';
        var input = this;

        if(NIP!='' && NIP!=null && input.files && input.files[0]){
            //var NIP = formEmployees;
            var fm = $(this).attr('data-fm');
            var type = fm.split('tagFM_')[1];
            //var type = $("#typefiles option:selected").attr("id")
            //alert(type);

            var sz = parseFloat(input.files[0].size) / 1000000; // ukuran MB
            var ext = input.files[0].type.split('/')[1];

            var ds = true;
            if(Math.floor(sz)<=8){
                ds = false;

                var fileName = type+'_'+NIP+'.'+ext;
                var formData = new FormData( $("#tagFM_IjazahS3")[0]);
                //var formData = new FormData( $("#"+fm)[0]);
                var url = base_url_js+'human-resources/upload_academic?fileName='+fileName+'&c='+type+'&u='+NIP;
                //alert(url);

                $.ajax({
                    url : url,  // Controller URL
                    type : 'POST',
                    data : formData,
                    async : false,
                    cache : false,
                    contentType : false,
                    processData : false,
                    success : function(data) {
                        toastr.success('Upload Success','Saved');
                        loadDataEmployees();

                        // var jsonData = JSON.parse(data);

                        // if(typeof jsonData.success=='undefined'){
                        //     toastr.error(jsonData.error,'Error');
                        //     // alert(jsonData.error);
                        // }
                        // else {
                        //     toastr.success('File Saved','Success!!');
                        // }

                    }
                });


            } else {
                alert('Maksimum size 8 Mb');
            }

        } else {
            toastr.error('Please, Select user before Upload File !','Eror!');
        }
    };
   // });


    $('.btnDelete').click(function () {

        var formEmployees = $('#formEmployees').val();
        var ID = $(this).attr('id');
        var colom = ID.split('btnDelete_')[1];
        var files = $(this).attr('data-file');

        if(formEmployees!='' && formEmployees!=null && files!='' && files!=null){
            if(confirm('Remove data?')){
                var url = base_url_js+'human-resources/employees/remove_files?fileName='+files+
                    '&user='+formEmployees.trim()+'&colom='+colom;
                $.get(url,function (result) {
                    toastr.success('Data Removed','Success');
                    loadDataEmployees();
                });
            }
        }



    });

</script>