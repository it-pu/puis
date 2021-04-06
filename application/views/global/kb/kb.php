<style>

    #viewkb .item-head:hover{
        background: #f5f5f5;
    }
    #viewkb .numbering {
        width: 30px;
        height: 30px;
        border: 1px solid #3F51B5;
        border-radius: 15px;
        text-align: center;
        padding-top: 5px;
        display: inline-block;
        margin-right: 10px;
        font-size: 11px;
        font-weight: bold;
    }
    #viewkb .info {
        color: orangered;
        font-size: 15px;
    }
    #viewkb .detailKB {
        margin-top: 15px;
    }

    #viewkb .detailKB ul.list-group .list-group-item {
        border-radius: 15px !important;
    }

    #viewkb a {
        text-decoration: none !important;
        display: block;
    }

</style>
<div class="row" id = "pageContent">
    <div class="col-md-3" style="border-right: 1px solid #CCCCCC;">
     <div class="panel panel-default hidden" id="panel-admin">
         <div class="panel-heading"><h4 class="panel-title">Admin tools</h4></div>
         <div class="panel-body text-center">
          <div class="btn-group">
            <?php if ($this->session->userdata('PositionMain')['IDDivision']=='12'){ ?>
            <button class="btn btn-sm btn-info" type="button" onclick="location.href='<?=base_url('admin-log-config/knowledge_base')?>'"><i class="fa fa-wrench"></i> Access Config</button>          
            <?php } ?>
            <button class="btn btn-sm btn-success btn-log-view hidden" type="button" onclick="location.href='<?=base_url('admin-log-content/knowledge_base')?>'"><i class="fa fa-history"></i> Logs of employee</button>
          </div>
         </div>
      </div>

     <div class="panel panel-default">
        <div class="panel-heading">
          <h4 class="panel-title"><i class="fa fa-edit"></i> Form Guideline Knowledge Base</h4>
        </div>
        <div class="panel-body">
          <div class="form-group hide">
            <label>Division</label>
            <input type="text" id="formKB_IDDivision" value="<?= $this->session->userdata('PositionMain')['IDDivision']; ?>">
          </div>
          <div class="form-group">
              <label>Type</label>
              <select class="form-control" id="formKBID"></select>
              <a style="float: right;" href="javascript:void(0);" class="" id="btnCrud_KB"><i class="fa fa-edit margin-right"></i> Type</a>
          </div>

        <div class="form-group">
            <label>Desc</label>
            <input class="form-control" id="formKB_Desc"/>
        </div>
        <div class="form-group">
          <label>Status</label>
          <select class="form-control" id = "statusKB">
            <option value="Private">Private</option>
            <option value="Public">Public</option>
          </select>
        </div>
        <div class="form-group">
            <label>File</b></label>
          <form id="formupload_files" enctype="multipart/form-data" accept-charset="utf-8" method="post" action="">
          <input type="file" name="userfile" id="upload_files" accept="">
          <div>PDF, Max Size 8MB</div>
            </form>
          </div>
          <div class="form-group" style="text-align: right;">
              <button class="btn btn-primary" id="saveFormKB">Save</button>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-9">
      <div class="row">
        <div class="col-md-4 col-md-offset-4">
            <div class="well">
              <div class="form-group">
                <label>Division</label>
                <select class="select2-select-00 full-width-fix" id="Division">
                  <?php for($i = 0; $i < count($G_division); $i++): ?>
                    <option value="<?php echo $G_division[$i]['Code'] ?>" > <?php echo $G_division[$i]['Name2'] ?> </option>
                  <?php endfor ?>
                 </select>
              </div>
            </div>
        </div>
      </div>
      <div class="row">
        <div id ="viewkb" class="col-md-12">
              <ul class="list-group" id="headerlist">...</ul>
        </div>
      </div>
    </div>
</div>
<?php if ($this->session->userdata('IDdepartementNavigation') == 12): // just IT ?>
<div class="row" style="max-height: 600px;overflow-y: auto;margin-top: 10px;">
  <div class="col-md-12">

    <div class="thumbnail">
      <div class="row" style="padding:10px;">
          <div class="col-md-12">
              <p style="color: red;">Just IT for see that</p>
              <div class="panel panel-primary">
                  <div class="panel-heading clearfix">
                      <h4 class="panel-title pull-left" style="padding-top: 7.5px;">Log KB</h4>
                  </div>
                  <div class="panel-body">
                    <?php echo  $page_log_content ?>
                  </div>
              </div>
          </div>
      </div>
    </div>
  </div>
</div>
<?php endif ?>


<script type="text/javascript">
  $(document).ready(function () {
    $("#Division option").filter(function() {
       //may want to use $.trim in here
       return $(this).val() == "<?php echo $selected ?>";
     }).prop("selected", true);
    $('#Division').select2({

    });
    $('#Division').trigger('change');  
    loadTypeKB() ;
  });

//save_formKB
$('#saveFormKB').click(function () {

    var formKBID = $('#formKBID').val();
    //var formKB_IDDivision = $('#formKB_IDDivision').val();
    var formKB_Type = $('#formKB_Type').val();
    var formKB_Desc = $('#formKB_Desc').val();
    var upload_files = $('#upload_files').val();


    var input = $('#upload_files');
    var files = input[0].files[0];

    var sz = parseFloat(files.size) / 8000000; // ukuran MB
    var ext = files.type.split('/')[1];
    var uploadFile = (Math.floor(sz)<=2) ? true : false ;



    // ($('#bpp_start').datepicker("getDate")!=null) ? moment($('#bpp_start').datepicker("getDate")).format('YYYY-MM-DD') : '',

    if(
    //formKB_IDDivision!='' && formKB_IDDivision!=null &&
    formKBID!='' && formKBID!=null &&
    formKB_Desc!='' && formKB_Desc!=null
    && uploadFile == true

  ){
      loading_button('#saveFormKB');

      var url = base_url_js+'api3/__crudkb';
      var data = {
          action: 'updateNewKB',
          ID : '', // ID knowledge_base
          dataForm : {
              IDType : formKBID, // menampung id kb type
              Desc : formKB_Desc,
              Status : $('#statusKB option:selected').val(),
          }
      };
      var token = jwt_encode(data,'UAP)(*');

      $.post(url,{token:token},function (jsonResult) {

          toastr.success('Data saved','Success');

          if (upload_files!=null && upload_files!=''){
            upload_kb(jsonResult.ID,'');
          }

          setTimeout(function () {
          $('#saveFormKB').html('Save').prop('disabled',false);
          $('#formKBID').val('');
          $('#formKB_Type').val('');
          $('#formKB_Desc').val('');
          $('#formKB_File').val('');
          $('#Division').trigger('change');
          }, 500);

        });

        } else {

          if(uploadFile==false){
            toastr.error('Max Size 8 Mb','error');
          } else {
          toastr.error('Form Required','error');
          }
        }

      });

  $(document).on('change','#Division', function () {
    //console.log('asdsad');
     var url = base_url_js+"kb";
     var data = {
      Division : $(this).find('option:selected').val(),
     };
     $.post(url,data,function (resultJson) {
      $(".list-group").empty();
      $(".list-group").html('<div id = "pageloading"></div>');
      loading_page('#pageloading');
      setTimeout(function () {
        $(".list-group").html(resultJson);
      },1000);
     })

  });



  $('#btnCrud_KB').click(function () {

      var bodyModal = '<div class="well row">' +
          '    <div class="col-md-8">' +
          '        <input class="hide" id="formKB_ID">' +
          '        <input class="form-control" id="formKB_Type">' +
          '    </div>' +
          '    <div class="col-md-4">' +
          '        <button class="btn btn-block btn-success" id="btnKBSave">Save</button>' +
          '    </div>' +
          '</div>' +
          '<div class="row">' +
          '    <div class="col-md-12">' +
          '        <hr/>' +
          '        <table class="table table-striped">' +
          '            <thead>' +
          '            <tr>' +
          '                <th style="width: 1%;">No</th>' +
          '                <th>Type</th>' +
          '                <th style="width: 15%;"><i class="fa fa-cog"></i></th>' +
          '            </tr>' +
          '            </thead>' +
          '            <tbody id="listData"></tbody>' +
          '        </table>' +
          '    </div>' +
          '</div>';

      // $('#GlobalModal .modal-header').html('<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
      //     '<h4 class="modal-title">Sumber Dana</h4>');
      $('#GlobalModal .modal-body').html(bodyModal);
      loadTypeKB();
      $('#GlobalModal .modal-footer').html('<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>');
      $('#GlobalModal').modal({
          'show' : true,
          'backdrop' : 'static'
      });

      $('#btnKBSave').click(function () {

          var formKB_ID = $('#formKB_ID').val();
          var formKB_Type = $('#formKB_Type').val();

          if(formKB_Type!='' && formKB_Type!=null){

              loading_buttonSm('#btnKBSave');

              var data = {
                  action : 'updateListKB_2',
                  ID : formKB_ID,
                  Type : formKB_Type
              };

              var token = jwt_encode(data,'UAP)(*');
              var url = base_url_js+'api3/__crudkb';

              $.post(url,{token:token},function (result) {
                  loadTypeKB();
                  //loadPenggunaanDana();
                  setTimeout(function () {
                      $('#btnKBSave').prop('disabled',false).html('Save');
                      $('#formKB_ID').val('');
                      $('#formKB_Type').val('');
                      
                  },500);
              });

          } else {
              toastr.warning('Hanya dapat digunakan untuk edit','Warning');
          }

      });

  });




      function loadTypeKB() {

          var data = {
              action : 'viewListKB_2'
          };

          var token = jwt_encode(data,'UAP)(*');
          var url = base_url_js+'api3/__crudkb';

          $.post(url,{token:token},function (jsonResult) {
            

              $('#listData,#formKBID').empty();
              if(jsonResult.length>0){

                  $.each(jsonResult,function (i,v) {
                    console.log(v);
                      $('#listData').append('<tr>' +
                          '<td>'+(i+1)+'</td>' +
                          '<td>'+v.Type+'</td>' +
                          '<td><button class="btn btn-sm btn-default btnEditKB" data-id="'+v.ID+'" data-j="'+v.Type+'"><i class="fa fa-edit"></i></button></td>' +
                          '</tr>');

                      $('#formKBID').append('<option value="'+v.ID+'">'+v.Type+'</option>');
                  });

              }

          });

      }

      $(document).on('click','.btnEditKB',function () {
          var j = $(this).attr('data-j');
          var ID = $(this).attr('data-id');

          $('#formKB_ID').val(ID);
          $('#formKB_Type').val(j);
      })


      $(document).on('click','.btnEdit',function () {
          var ID = $(this).attr('data-id');
          var j = $(this).attr('data-j');

          $('#formKB_ID').val(ID);
          $('#formKB_Desc').val(j);
      })



  function upload_kb(ID,FileNameOld) {

      var input = $('#upload_files');
      var files = input[0].files[0];

      var sz = parseFloat(files.size) / 8000000; // ukuran MB
      var ext = files.type.split('/')[1];

      if(Math.floor(sz)<=2){

          var fileName = moment().unix()+'_'+sessionNIP+'.'+ext;
          var formData = new FormData( $("#formupload_files")[0]);


          var url = base_url_js+'kb/upload_kb?fileName='+fileName+'&old='+FileNameOld+'&&id='+ID;

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
                  setTimeout(function () {
                      if (oTable !== undefined) {
                         oTable.ajax.reload((e) => {
                             
                         });
                      }
                  },500);
                  // loadDataEmployees();

              }
          });

      }
  }



  $(document).on('click','.btnActRemove',function () {
     if(confirm('Are you sure?')){
         var ID = $(this).attr('data-id');

         var data = {
             action : 'removeDataKB',
             ID : ID
         };

         var token = jwt_encode(data,'UAP)(*');
         var url = base_url_js+'api3/__crudkb';
         $.post(url,{token:token},function (result) {
             toastr.success('Data removed','Success');
             // loadDataKB();
             $('#Division').trigger('change');

             if (oTable !== undefined) {
                oTable.ajax.reload((e) => {
                    
                });
             }
         });

     }
  });



</script>

<script type="text/javascript">
  function checkHasAccess() {
    var result = [];
    var dataPost = {
      DivisiID : "<?=$this->session->userdata('IDdepartementNavigation')?>",
      TypeContent : 'knowledge_base'
    }
    var token = jwt_encode(dataPost,'UAP)(*');
    $.ajax({
        type : 'POST',
        url : base_url_js+"user-access-content",
        data : {token:token},
        dataType : 'json',
        async: false, 
        beforeSend :function(){},
        error : function(jqXHR){
          $('#GlobalModal .modal-header').html('<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
                    '<h4 class="modal-title">Error !</h4>');
          $("body #GlobalModal .modal-body").html(jqXHR.responseText);
          $('body #GlobalModal .modal-footer').html('<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>');
          $("body #GlobalModal").modal("show");
        },success : function(response){
          result = response;
        }
    });

    return result;
  }
  $(document).ready(function(){
    $("#viewkb").on('click','.detailKB .list-group-item',function(){
      var itsme = $(this);
      var contentid = itsme.data('contentid');
      var type = itsme.data('type');

      var dataPost = {
        ContentID : contentid,
        TypeContent : type
      }
        
      var token = jwt_encode(dataPost,'UAP)(*');

      $.ajax({
        type : 'POST',
        url : base_url_js+"help/hitlog",
        data : {token:token},
        dataType : 'json',
        beforeSend :function(){},
        error : function(jqXHR){
          $("body #GlobalModal .modal-body").html(jqXHR.responseText);
          $('body #GlobalModal .modal-footer').html('<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>');
          $("body #GlobalModal").modal("show");
        },success : function(response){
          console.log(response);
          if(!jQuery.isEmptyObject(response)){
            if(response.finish){
              itsme.find(".viewers").html('<span class="text-success"><i class="fa fa-check-square"></i> has bean read <span class="total-read">'+response.count+'</span> times</span>');             
            }
          }
        }
    });
    });

    var HasAnAccess = checkHasAccess();
    if(!jQuery.isEmptyObject(HasAnAccess)){
      if(HasAnAccess.IsLogEmp == 'Y'){
        $("#panel-admin, .btn-log-view").removeClass('hidden');
      }else{
        $("#panel-admin, .btn-log-view").addClass('hidden');        
      }
      /*if(HasAnAccess.IsCreateGuide == 'Y'){
        $("#panel-form").removeClass('hidden');
        $("#user-panel").removeClass("col-md-12").addClass("col-md-9");
      }else{
        $("#user-panel").removeClass("col-md-9").addClass("col-md-12");
        $("#panel-form").addClass('hidden');
      }*/

    }
  });
</script>
