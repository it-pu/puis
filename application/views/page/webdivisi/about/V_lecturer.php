
<div class="row" style="margin-top: 30px;">
    <div class="col-md-6">
        <table class="table">
            
            <tr>
                <td style="width: 15%;">Name Lecture</td>
                <td style="width: 1%;">:</td>
                
                <td>
                      <select style="width: 100%;" id="NIP" ></select>
                </td>
            </tr>
            <tr>
                <td style="width: 15%;">Upload Images</td>
                <td style="width: 1%;">:</td>
                <td>
                  <input type="file" id="uploadFile" name="uploadFile" accept="jpg/png">
                  <span class="red">* Size weight x height 200px x 200px</span>
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
      <div class="well" style="position: absolute;">
        <div id="viewDataDesc"></div>
      </div>
    </div>
</div>



<!-- ======= Hapus slide ======== --> 
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
                <!-- Beri id "loading-hapus" untuk loading ketika klik tombol hapus -->
                <!-- <div id="loading-hapus" class="pull-left">
                    <b>Sedang meghapus...</b>
                </div> -->
                <!-- Beri id "btn-hapus" untuk tombol hapus nya -->
                <button type="button" class="btn btn-primary" id="btn-hapus">Ya</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Tidak</button>
            </div>
        </div>
    </div>
</div>


<script>
$(document).ready(function () {
    
    loadDataLecture();
    LoadNama();
});
function loadDataLecture() {
    var data = {
        action : 'loadDataLecturer',
        
    };
    var token = jwt_encode(data,'UAP)(*');
    var url = base_url_js+'api-prodi/__crudDataProdi';
    var locimg = base_url_js+'images/Lecturer/';
    $.post(url,{token:token},function (jsonResult) {
        $('#viewDataDesc').empty();
        if(jsonResult.length>0){

            $.each(jsonResult,function (i,v) {
                $('#viewDataDesc').append('<div class="col-lg-4 col-md-6"><div class="thumbnail" style="text-align: center; padding: 15px;"> <img src="'+locimg+''+v.Photo+'"  >'+
                      '<p><b>'+v.NIP+'||'+v.Name+'</b></p>'+
                       '<p>'+
                        // ' <a href="#" data-toggle="modal" data-target="#form-modal" data-id="'+v.ID+'" token = "'+v.token+'" class="btn-form-ubah"><span class="glyphicon glyphicon-pencil"></span> Edit</a>'+ 
                        ' <a href="" data-id="'+v.ID+'" data-toggle="modal" data-target="#delete-modal" class="btn-alert-hapus"><span class="glyphicon glyphicon-trash"></span> Hapus</a>'+
                        
                        '</p>'+
                      '</div></div>');
            });

        } else {
            $('#viewDataDesc').html('Data not yet');
        }

    });
}
function LoadNama() { // load data student
        var selector =$('#NIP');
        var url = base_url_js+'api/__getDosenSelectOption';
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
                var NIP = resultJson[i].NIP
                var Name = resultJson[i].Name
                selector.append(
                    ' <option value="'+NIP+'">'+NIP+'|'+Name+'</option>'
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
   
    var NIP = $('#NIP').val(); 
    var thisbtn = $(this);
    var form_data = new FormData();
    var find = true;

    
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
                action : 'saveDataLecturer',
                dataForm:{
                  NIP : NIP,
                }
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
                loadDataLecture();
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
    
});
// Fungsi ini akan dipanggil ketika tombol hapus diklik
$(document).on('click', '.btn-alert-hapus', function(){ // Ketika tombol dengan class btn-alert-hapus pada div view di klik
  id = $(this).data('id') // Set variabel id dengan id yang kita set pada atribut data-id pada tag button edit

  $('#btn-hapus').attr('data-id',id); // Set variabel id dengan id yang kita set pada atribut data-id pada tag button hapus
})

// DELETE
$(document).off('click', '#btn-hapus').on('click', '#btn-hapus',function(e) { // Ketika tombol hapus di klik
    var ID = $(this).attr('data-id');
   
    var thisbtn = $(this);
    loading_button('#btn-hapus'); // Munculkan loading hapus
    var data = {
                action : 'deleteDataLecturer',
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
              loadDataLecture();
              toastr.success('Data Update','Success');
              thisbtn.prop('disabled',false).html('Ya');
              $('#delete-modal').modal('hide');
            },
            error: function (data) {
              toastr.error('Form required','Error');
              thisbtn.prop('disabled',false).html('Ya');
            }
    })
  });

  $('#form-modal').on('hidden.bs.modal', function (e){ // Ketika Modal Dialog di Close / tertutup
    $('#form-modal input, #form-modal select, #form-modal textarea').val('') // Clear inputan menjadi kosong
  }) 

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
