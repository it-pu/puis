
<div class="row" style="margin-top: 30px;">
    <div class="col-md-6">
        <table class="table">
            <tr>
                <td style="width: 15%;">Language</td>
                <td style="width: 1%;">:</td>
                <td>
                    <select style="max-width: 150px;" id="LangID" class="form-control"></select>
                </td>
            </tr>
            <tr>
                <td style="width: 15%;">Student</td>
                <td style="width: 1%;">:</td>
                <td>
                      <select style="width: 100%;" id="formName" ></select>
                </td>
            </tr>
            <tr>
                <td style="width: 15%;">Upload Images</td>
                <td style="width: 1%;">:</td>
                <td>
                  <input type="file" id="uploadFile" name="uploadFile" accept="jpg/png">
                  <span class="red">* Images size weight x height : 200 x 250px</span>
                </td>
            </tr>
            <tr>
                <td>Description</td>
                <td>:</td>
                <td>
                    <textarea id="Description" class="form-control"></textarea>
                </td>
            </tr>

            <tr>
                <td colspan="3" style="text-align: right;">
                    <button class="btn btn-success" id="btnSave">Save</button>
                </td>
            </tr>
        </table>
    </div>

    <div class="col-md-6" style="border-left: 1px solid #CCCCCC;">
        <div id="viewDataDesc"></div>
    </div>
</div>
<!-- ======= Hapus  ======== -->
<div id="delete-modal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">
                    Konfirmasi
                </h4>
            </div>
            <div class="modal-body">
                Apakah anda yakin ingin menghapus data ini?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="btn-hapus">Ya</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Tidak</button>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function () {

        window.G_Type = 'testimonials';
        loadDataTestimonials();
        LoadNama();
        loadSelectOptionLanguageProdi('#LangID','');

        $('#Description').summernote({
            placeholder: 'Text your announcement',
            tabsize: 2,
            height: 300,
            toolbar: [
                // [groupName, [list of button]]
                ['style', ['bold', 'italic', 'underline', 'clear']],
                ['font', ['strikethrough', 'superscript', 'subscript']],
                ['fontsize', ['fontsize']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['height', ['height']]
            ],
            callbacks: {
                  onPaste: function (e) {
                    var bufferText = ((e.originalEvent || e).clipboardData || window.clipboardData).getData('text/html');
                    e.preventDefault();
                    var div = $('<div />');
                    div.append(bufferText);
                    div.find('*').removeAttr('style');
                    setTimeout(function () {
                      document.execCommand('insertHtml', false, div.html());
                    }, 10);
                  }
                }
        });

        
        var firsLoad = setInterval(function () {

            var LangID = $('#LangID').val();
            if(LangID!='' && LangID!=null){
                loadDataOption();
                clearInterval(firsLoad);
            }

        },1000);

    });

    $('#LangID').change(function () {
        var LangID = $('#LangID').val();
        if(LangID!='' && LangID!=null){
            loadDataOption();
        }
    });

    function loadDataTestimonials() {
        var data = {
            action : 'readProdiTexting',
            Type : G_Type
        };
        var token = jwt_encode(data,'UAP)(*');
        var url = base_url_js+'api-prodi/__crudDataProdi';
        var imgmhs = base_url_js+'images/Testimonials/';
        $.post(url,{token:token},function (jsonResult) {
            $('#viewDataDesc').empty();
            if(jsonResult.length>0){

                $.each(jsonResult,function (i,v) {
                    $('#viewDataDesc').append('<div class="well"> <img src="'+imgmhs+''+v.Photo+'"  width="15%"><h3><b> '+v.NPM+' || '+v.Name+'</b></h3><hr><h3 style="margin-top: 5px;"><b>'+v.Language+'</b></h3><div>'+v.Description+'</div><hr><a href="" data-id="'+v.ID+'" data-toggle="modal" data-target="#delete-modal" class="btn btn-danger btn-alert-hapus"><span class="glyphicon glyphicon-trash"></span> Delete</a></div>');
                });

            } else {
                $('#viewDataDesc').html('<div class="well">Data not yet</div>');
            }

        });
    }

    function loadDataOption() {
        var LangID = $('#LangID').val();
        if(LangID!='' && LangID!=null){
            var data = {
                action : 'readDataProdiTexting',
                Type : G_Type,
                LangID : LangID
            };
            var token = jwt_encode(data,'UAP)(*');
            var url = base_url_js+'api-prodi/__crudDataProdi';
            $.post(url,{token:token},function (jsonResult) {
                if(jsonResult.length>0){
                    $('#Description').summernote('code', jsonResult[0].Description);
                } else {
                    $('#Description').summernote('code', '');
                }

            });
        }
    }

    
    function LoadNama() { // load data student
        var selector =$('#formName');
        var url = base_url_js+'api-prodi/__getStudentsProdi';
        var data = {
            auth : 's3Cr3T-G4N',
            mode : 'showDataDosen'
        };
        var token = jwt_encode(data,"UAP)(*");
        $.post(url,{ token:token },function (resultJson) {
            
        }).done(function(resultJson) {
            //var response = jQuery.parseJSON(resultJson);
            selector.empty()
            for (var i = 0; i < resultJson.length; i++) {
                var NPM = resultJson[i].NPM
                var Name = resultJson[i].Name
                selector.append(
                    ' <option value="'+NPM+'">'+NPM+'|'+Name+'</option>'
                    )

            }
            selector.select2({
                //allowClear: true

            });
        }).fail(function() {
          toastr.info('No Result Data');
        }).always(function() {
                        
        }); 

    }

    $('#btnSave').click(function () {

        var NPM = $('#formName').val(); 
        // alert(NPM);
        var LangID = $('#LangID').val();
        var Description = $('#Description').val();
        var thisbtn= $(this);
        var form_data = new FormData();
        var find = true;

        if(LangID!='' && LangID!=null &&
            Description!='' && Description!=null){

            var prodi_texting = {
              LangID : LangID,
              Type : 'testimonials',
              Description : Description,
              UpdatedBy : sessionNIP,
            };

            var student_testimonials = {
              NPM : NPM,
            }
           
            // upload file
            $('input[type="file"]').each(function(){
                var IDFile = $(this).attr('id');
                var ev = $(this);
                var NameItem = 'ID '+IDFile;
                if (!file_validation2(ev,NameItem) ) {
                  find = false;
                  return false;
                }
            })
            if (find) { // validasi file berhasil
                // console.log('asd');
                if ( $( '#'+'uploadFile').length ) { // jika upload file
                var UploadFile = $('#'+'uploadFile')[0].files;
                  for(var count = 0; count<UploadFile.length; count++)
                  {
                   form_data.append("uploadFile[]", UploadFile[count]);
                  }
                }
                
                var data = {
                    action : 'saveDataTestimonials',
                    prodi_texting : prodi_texting,
                    student_testimonials : student_testimonials,
                };
              
                loading_button('#btnSave');
                var token = jwt_encode(data,'UAP)(*');
                form_data.append('token',token);
                var url = base_url_js+'api-prodi/__crudDataProdi';
                $.ajax({
                  type:"POST",
                  url:url,
                  data: form_data, // Data sent to server, a set of key/value pairs (i.e. form fields and values)
                  contentType: false,       // The content type used when sending data to the server.
                  cache: false,             // To unable request pages to be cached
                  processData:false,
                  dataType: "json",
                  success:function(data)
                  {
                    loadDataTestimonials();
                    toastr.success('Data saved','Success');
                    $('#btnSave').html('save');
                    $('#btnSave').prop('disabled',false);

                  },
                  error: function (data) {
                     toastr.error('Form required','Error');
                     thisbtn.prop('disabled',false).html('Save');
                  }
                })
              }
        }
    });
    // Fungsi ini akan dipanggil ketika tombol hapus diklik
    $(document).on('click', '.btn-alert-hapus', function(){ // Ketika tombol dengan class btn-alert-hapus pada div view di klik
      id = $(this).data('id') // Set variabel id dengan id yang kita set pada atribut data-id pada tag button edit
      $('#btn-hapus').attr('data-id',id); // Set variabel id dengan id yang kita set pada atribut data-id pada tag button hapus
    })
    $(document).off('click', '#btn-hapus').on('click', '#btn-hapus',function(data) { // Ketika tombol hapus di klik
    var ID = $(this).attr('data-id');
    var thisbtn = $(this);
    loading_button('#btn-hapus'); // Munculkan loading hapus
    var data = {
                action : 'deleteTestimonials',
                ID : ID,
                };
    var form_data = new FormData();
    var token = jwt_encode(data,"UAP)(*");
    form_data.append('token',token);
    var url = base_url_js + "api-prodi/__crudDataProdi";
    $.ajax({
        type :"POST",
        url : url,
        data : form_data, // Data sent to server, a set of key/value pairs (i.e. form fields and values)
        contentType: false,       // The content type used when sending data to the server.
        cache: false,             // To unable request pages to be cached
        processData:false,
        dataType: "json",
        beforeSend: function(e) {
          if(e && e.overrideMimeType) {
            e.overrideMimeType('application/jsoncharset=UTF-8');
          }
        },
        success: function(data){ // Ketika proses pengiriman berhasil
           
            toastr.success('Data saved','Success');
            thisbtn.prop('disabled',false).html('Ya');
            $('#delete-modal').modal('hide');
            loadDataTestimonials();

            setTimeout(function(){
              window.location.href="";
            },500);

        },
        error: function (data) {
            toastr.error('Form required','Error');
            thisbtn.prop('disabled',false).html('Ya');
        }
    });
  });

  function file_validation2(ev,TheName = '')
  {
      var files = ev[0].files;
      var error = '';
      var msgStr = '';
      var max_upload_per_file = 4;
      

      if (files.length > max_upload_per_file) {
        msgStr += TheName +' should not be more than 4 Files<br>';

      }
      else
      {
        for(var count = 0; count<files.length; count++)
        {
         var no = parseInt(count) + 1;
         var name = files[count].name;
         var extension = name.split('.').pop().toLowerCase();
         if(jQuery.inArray(extension, ['jpg' ,'png','jpeg','pdf','doc','docx']) == -1)
         {
          // msgStr += TheName +' which file Number '+ no + ' Invalid Type File<br>';
          msgStr += TheName +' Invalid Type File<br>';
          //toastr.error("Invalid Image File", 'Failed!!');
          // return false;
         }

         var oFReader = new FileReader();
         oFReader.readAsDataURL(files[count]);
         var f = files[count];
         var fsize = f.size||f.fileSize;

         
         console.log(fsize);

         if(fsize > 2000000) // 2mb
         {
          // msgStr += TheName + ' which file Number '+ no + ' Image File Size is very big<br>';
          msgStr += TheName + ' Image File Size is very big<br>';
          //toastr.error("Image File Size is very big", 'Failed!!');
          //return false;
         }

       
         
        }
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

</script>
