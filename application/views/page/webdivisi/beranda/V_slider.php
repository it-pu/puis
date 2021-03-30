
<style>

#imagePreview1{ 
    width: 99%;
    height: 100px;
    margin: 2px;
    background-position: center center;
    background-size: cover;
    -webkit-box-shadow: 0 0 1px 1px rgba(0, 0, 0, .3);
    display: inline-block;
    margin-bottom:30px;
}

#showCheck{
  display: none;
}
.showfrom{
  display: block !important;
}
</style>
<script>
    var base_url = '<?= base_url() ?>'; // Buat variabel base_url agar bisa di akses di semua file js
</script>
        <div class="row"> 
            <div class="col-md-12">

              <div class="widget box ">
                <div class="widget-header">

                <h4><i class="icon-reorder"></i> Show slider</h4>
                <div class="toolbar no-padding">
                    <div class="btn-group">
                      <span data-smt="" class="btn btn-xs btn-add">
                        <a href="" id="btn-tambah" data-toggle="modal" data-target="#form-modal">
                        <i class="icon-plus"></i> Add Slider
                        </a>
                      </span>
                    </div>
                </div>
                </div>
                

                <div id="show_data" class="widget-content" style="display: flow-root;">

                </div>

            </div>
          </div>
        </div>


<!-- ====== modal form slider =======-->
<!-- ======= tambah slide ======== -->
  <div class="modal fade in" id="form-modal" role="dialog" aria-hidden="false">
    <div class="modal-dialog" role="document">
        <div class="modal-content animated jackInTheBox">
            <div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
              <h4 class="modal-title"><span id="modal-title"></span></h4></div>
            <div class="modal-body">
              <div class="row"> 
              <!-- Beri id "pesan-error" untuk menampung pesan error -->
                   <!--  <div id="pesan-error" class="alert alert-danger"></div>    -->    
                <div class="col-md-12">            
                  
                 
                         
                        <div class="caption">
                          <label class="control-label">Title Slider:</label>

                          <input type="text"  id="formTitle1" class="form-control" placeholder="Title Slide Show"><br>
                        <div id="imagePreview1" style="margin-bottom:7.5px;"></div>     
                         <div class="custom-file-input " style="position:relative; left:0px;">
                          <i class="fa fa-file-image-o"></i>&nbsp; &nbsp;Browse<input  id="uploadFile1"  type="file" value="" name="file-input" />
                          <p class="red">*Size weight x height 1920px x 500px</p>
                         </div>   
                         
                      </div>
                   
  
                  <button type="submit" id="btn-simpan" class="btn btn-primary btnsave1" style="margin-top: 15px">Simpan</button>
                  <button type="submit" id="btn-ubah" class="btn btn-primary" style="margin-top: 15px">Ubah</button>
           
                </div>    
              </div>
            </div>
            <div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">Close</button></div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
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
                    <button type="button" class="btn btn-primary" id="btn-hapus">Ya</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Tidak</button>
                </div>
            </div>
        </div>
    </div>


<script type="text/javascript">
   var id = 0 // Untuk menampung ID yang kaan di ubah / hapus
    
// view upload images
$("#uploadFile1").on("change", function()
    {
        var files = !!this.files ? this.files : [];
        if (!files.length || !window.FileReader) return; // no file selected, or no FileReader support
        
        if (/^image/.test( files[0].type)){ // only image file
            var reader = new FileReader(); // instance of the FileReader
            reader.readAsDataURL(files[0]); // read the local file
            
            reader.onloadend = function(){ // set image data as background of div
                $("#imagePreview1").css("background-image", "url("+this.result+")");
            }
      
        }
    });

// check show form
$('#formStatus1').on("change", function(){
   
    if($(this).is(":checked")) {
           $('#showCheck').addClass('showfrom');
      } else {
          
           $('#showCheck').removeClass('showfrom');
      }

  });
  
  // $('.UpdSort').on("change", function(){
  $(document).off('change', '.UpdSort').on('change', '.UpdSort',function(e) {   
    var ID = $(this).attr('data-id');
    var v = $(this).find('option:selected').val();
    var sortex = $(this).attr('sortex');
    var data = {
      ID : ID,
      Sorting : v,
      action : 'change_sorting',
      sortex : sortex,
    }

      var token = jwt_encode(data,'UAP)(*');
      var url = base_url_js+'api-prodi/__crudDataProdi';
      $.post(url,{token:token},function(jsonResult){
        showSlider();
      });
  });

  
  // Sembunyikan loading simpan, loading ubah, loading hapus, pesan error, pesan sukes, dan tombol reset
  $('#loading-simpan, #loading-ubah, #loading-hapus, #pesan-error, #pesan-sukses, #btn-reset').hide()
  // Fungsi ini akan dipanggil ketika tombol edit diklik

  $(document).on('click', '.btn-form-ubah', function(){ // Ketika tombol dengan class btn-form-ubah pada div view di klik
    id = $(this).data('id') // Set variabel id dengan id yang kita set pada atribut data-id pada tag button edit
    $('#btn-ubah').attr('data-id',id);
    var token = $(this).attr('token');
    var data = jwt_decode(token);
    // console.log(data);
    var Images = base_url_js+'images/Slider/'+data['Images'];
     $("#imagePreview1").css("background-image", "url("+Images+")");
     $('#formTitle1').val(data['Title']);
     $('#formButtonName1').val(data['NameButton']);
     $('#formUrl1').val(data['Url']);

    $('#btn-simpan').hide() // Sembunyikan tombol simpan
    $('#btn-ubah').show() // Munculkan tombol ubah dan checkbox foto

    // Set judul modal dialog menjadi Form Ubah Data
    $('#modal-title').html('Form Ubah data');

    if (data['Button']=='1') {
      $('#formStatus1').prop('checked',true);
      $('#formStatus1:checked').trigger('change');
    }

  })


  // Fungsi ini akan dipanggil ketika tombol hapus diklik
  $(document).on('click', '.btn-alert-hapus', function(){ // Ketika tombol dengan class btn-alert-hapus pada div view di klik
    id = $(this).data('id') // Set variabel id dengan id yang kita set pada atribut data-id pada tag button edit
    $('#btn-hapus').attr('data-id',id); // Set variabel id dengan id yang kita set pada atribut data-id pada tag button hapus
  })


  $('#btn-tambah').click(function(){ // Ketika tombol tambah diklik
    $('#btn-ubah').hide() // Sembunyikan tombol ubah
    $('#btn-simpan').show() // Munculkan tombol simpan

    // Set judul modal dialog menjadi Form Simpan Data
    $('#modal-title').html('Form Simpan data')
    $("#imagePreview1").css("background-image", "none");
  })

// ======== View data ==== ////
$(document).ready(function(){
    showSlider();

});
     
//fungsi tampil barang
function showSlider(){
    var data = {action : 'viewDataSlider'};
    var token = jwt_encode(data,'UAP)(*');
    var url = base_url_js+'api-prodi/__crudDataProdi';

    $.post(url,{token:token},function(jsonResult){
        if(jsonResult.length>0){
            var html = '';
            var i;
            var data = jsonResult;
            var totgambar = data.length;
            var OpLoop = function(ss= '')
            {
              // console.log(ss);
              var htmlOP = '<option value ="" selected>--Not Set--</option>';
              for (var z = 1; z <= totgambar; z++) {
                if (ss!= '') {
                  var selected = (z==ss) ? 'selected' : '';
                }
                htmlOP += '<option value ="'+z+'" '+selected+'>'+z+'</option>';
              }

              return htmlOP;
            };
            
            for(i=0; i<data.length; i++){
                var Sorting = data[i].Sorting;
                 var htmlCombo = '<select class= "form-control UpdSort" data-id="'+data[i].ID+'"  sortex = "'+Sorting+'">'+
                      OpLoop(Sorting)+
                   '</select>';   

                html += 
                      '<div class="col-sm-6 col-md-3" style="margin-bottom: 15px;">'+
                        '<div class="thumbnail">'+
                          '<img src="../../images/Slider/'+data[i].Images+'" alt="'+data[i].Title+'">'+
                          '<div class="caption">'+
                            
                            '<p style="text-transform: capitalize;">'+data[i].Title+'</p>'+
                            '<p>'+
                            ' <a href="" data-id="'+data[i].ID+'" data-toggle="modal" data-target="#form-modal" class="btn btn-warning btn-form-ubah" token = "'+data[i].token+'"><span class="glyphicon glyphicon-pencil"></span> Edit</a>'+ 
                            ' <a href="" data-id="'+data[i].ID+'" data-toggle="modal" data-target="#delete-modal" class="btn btn-danger btn-alert-hapus"><span class="glyphicon glyphicon-trash"></span> Hapus</a>'+
                            
                            '</p>'+
                            '<p>'+htmlCombo+'</p>'+
                          '</div>'+
                        '</div>'+
                      '</div>'+
                      '<div>';
            }
            $('#show_data').html(html);
        }
    });
}
// ======= save ======== /////
  $(document).off('click', '.btnsave1').on('click', '.btnsave1',function(e) {
    // console.log('asc');
    var thisbtn = $(this);
    var formTitle1 = $('#formTitle1').val();
    var formButtonName1 = $('#formButtonName1').val();
    var formUrl1 = $('#formUrl1').val();
    var formStatus1 = $('#formStatus1').val();
    var form_data = new FormData();
    var find = true;
     
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
                  if ( $( '#'+'uploadFile1').length ) { // jika upload file
                  var UploadFile = $('#'+'uploadFile1')[0].files;
                    for(var count = 0; count<UploadFile.length; count++)
                    {
                     form_data.append("uploadFile1[]", UploadFile[count]);
                    }
                  }
            // loading_button('#btn-simpan');  
             
            var data = {
                  action : 'insertDataslider',
                  dataform:{
                    Title : formTitle1,
                    Button : formStatus1,
                    Url : formUrl1,
                    NameButton : formButtonName1,
                   
                  }
            };
            // console.log(data);return;
            var token = jwt_encode(data,"UAP)(*");
            form_data.append('token',token);
            var url = base_url_js + "api-prodi/__crudDataProdi";
            loading_button('.btnsave1');
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
                showSlider();
                toastr.success('Data saved','Success');
                thisbtn.prop('disabled',false).html('Save');
                $('#form-modal').modal('hide');
              },
              error: function (data) {
                 toastr.error('Form required','Error');
                 thisbtn.prop('disabled',false).html('Save');
              }
            })
          }

  });

  // edit data

    $(document).off('click', '#btn-ubah').on('click', '#btn-ubah',function(e) {
     // console.log('asc');
    var ID = $(this).attr('data-id');
    var thisbtn = $(this);
    var formTitle1 = $('#formTitle1').val();
    var formButtonName1 = $('#formButtonName1').val();
    var formUrl1 = $('#formUrl1').val();
    var formStatus1 = $('#formStatus1').val();
    var form_data = new FormData();
    var find = true;
    
  
      
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
                  if ( $( '#'+'uploadFile1').length ) { // jika upload file
                  var UploadFile = $('#'+'uploadFile1')[0].files;
                    for(var count = 0; count<UploadFile.length; count++)
                    {
                     form_data.append("uploadFile1[]", UploadFile[count]);
                    }
                  }
            // loading_button('#btn-simpan');  
             
            var data = {
                  action : 'updateDataslider',
                  dataform:{
                    ID : ID,
                    Title : formTitle1,
                    Button : formStatus1,
                    Url : formUrl1,
                    NameButton : formButtonName1,
                  }
            };
            // console.log(data);return;
            var token = jwt_encode(data,"UAP)(*");
            form_data.append('token',token);
            var url = base_url_js + "api-prodi/__crudDataProdi";
            loading_button('#btn-ubah');
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
                showSlider();
                toastr.success('Data Update','Success');
                thisbtn.prop('disabled',false).html('Save');
                $('#form-modal').modal('hide');
              },
              error: function (data) {
                 toastr.error('Form required','Error');
                 thisbtn.prop('disabled',false).html('Save');
              }
            })
          }
  });

// DELETE
$(document).off('click', '#btn-hapus').on('click', '#btn-hapus',function(data) { // Ketika tombol hapus di klik
    var ID = $(this).attr('data-id');

    var thisbtn = $(this);
    loading_button('#btn-hapus'); // Munculkan loading hapus
    var data = {
                action : 'deleteDataslider',
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
            showSlider();

            setTimeout(function(){
              window.location.href="";
            },500);

        },
        error: function (data) {
            toastr.error('Form required','Error');
            thisbtn.prop('disabled',false).html('Ya');
        }
    })
  });

  $('#form-modal').on('hidden.bs.modal', function (data){ // Ketika Modal Dialog di Close / tertutup
    $('#form-modal input, #form-modal select, #form-modal textarea').val('') // Clear inputan menjadi kosong
  })

// validasi images
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
       // console.log(fsize);

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

