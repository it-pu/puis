<?php $Previleges = ''; ?>
<?php $DepartmentID = $this->session->userdata('DepartmentIDDocument'); ?>
<div class="row">
  <div class="col-md-4 col-md-offset-4">
    <div class="well">
      <div class="form-group">
        <label>Choose Department</label>
        <select class="form-control" id = "DepartmentIDChoose">
          <?php for ($i=0;$i < count($auth);$i++): ?>
            <?php $selected = ($auth[$i]['ID'] == $DepartmentID) ? 'selected' : '' ?>
            <?php 
              if ($auth[$i]['ID'] == $DepartmentID) {
                $Previleges = $auth[$i]['Level'];
              }
             ?>
            <option value="<?php echo $auth[$i]['ID'] ?>" <?php echo $selected ?> ><?php echo $auth[$i]['NameDepartment'] ?></option>
          <?php endfor ?>
        </select>
      </div>
    </div>
  </div>
</div>
<div class="tabbable tabbable-custom tabbable-full-width">
    <ul class="nav nav-tabs">
        <li class="<?php if($this->uri->segment(1)=='request-document-generator' &&  ( $this->uri->segment(2) == '' ||  $this->uri->segment(2) == null ) ) { echo 'active'; } ?>">
            <a href="<?php echo base_url('request-document-generator'); ?>">Document</a>
        </li>
        <?php if ($Previleges == 'Admin'): ?>
          <li class="<?php if($this->uri->segment(1)=='request-document-generator' &&  $this->uri->segment(2) == 'Template' ) { echo 'active'; } ?>">
              <a href="<?php echo base_url('request-document-generator/Template'); ?>">Template</a>
          </li>
        <?php endif ?>
          <li class="<?php if($this->uri->segment(1)=='request-document-generator' &&  $this->uri->segment(2) == 'NeedApproval' ) { echo 'active'; } ?>">
              <a href="<?php echo base_url('request-document-generator/NeedApproval'); ?>">Need Approval <span class="badge" id = "CountApproval"></span></a>
          </li>
    </ul>
    <div style="border-top: 1px solid #cccccc">
        <div class="row" style="margin-top: 15px;">
            <div class="col-md-12">
             <?php echo $page; ?>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
  var msgMasterDocument = 'No Selected Document';
  var DepartmentID = '<?php echo $DepartmentID ?>';

  $(document).ready(function(e){
    $('#DepartmentIDChoose').removeClass('form-control');
    $('#DepartmentIDChoose').addClass('select2-select-00 full-width-fix');
    $('#DepartmentIDChoose').select2({});

    getNeedApproval();

  })

  function getNeedApproval(){
    var url = base_url_js+"__request-document-generator/__NeedApproval";
     $.get( url, function(response) {
       $('#CountApproval').html(response.recordsTotal);
       // console.log(response);
     })
     .done(function() {
       // alert( "second success" );
     })
     .fail(function() {
       // alert( "error" );
     })
  }

  function file_validation_generator(ev,TheName = '')
  {
      var files = ev[0].files;
      var error = '';
      var msgStr = '';
      var max_upload_per_file = 1;
      if (files.length > 0) {
        if (files.length > max_upload_per_file) {
          msgStr += 'Upload File '+TheName + ' 1 Document should not be more than 1 Files<br>';

        }
        else
        {
          for(var count = 0; count<files.length; count++)
          {
           var no = parseInt(count) + 1;
           var name = files[count].name;
           var extension = name.split('.').pop().toLowerCase();
           if(jQuery.inArray(extension, ['docx']) == -1)
           {
            msgStr += 'Upload File '+TheName + ' Invalid Type File<br>';
           }

           var oFReader = new FileReader();
           oFReader.readAsDataURL(files[count]);
           var f = files[count];
           var fsize = f.size||f.fileSize;

           if(fsize > 3000000) // 3mb
           {
            msgStr += 'Upload File '+TheName +  ' Image File Size is very big<br>';
           }
           
          }
        }
      }
      else
      {
        msgStr += 'Upload File '+TheName + ' Required';
      }
      return msgStr;
  }


  function AjaxSubmitTemplate(url='',token='',ArrUploadFilesSelector=[]){
       var def = jQuery.Deferred();
       var form_data = new FormData();
       form_data.append('token',token);
       if (ArrUploadFilesSelector.length>0) {
          for (var i = 0; i < ArrUploadFilesSelector.length; i++) {
              var NameField = ArrUploadFilesSelector[i].NameField+'[]';
              var Selector = ArrUploadFilesSelector[i].Selector;
              var UploadFile = Selector[0].files;
              for(var count = 0; count<UploadFile.length; count++)
              {
               form_data.append(NameField, UploadFile[count]);
              }
          }
       }

       $.ajax({
         type:"POST",
         url:url,
         data: form_data,
         contentType: false,       // The content type used when sending data to the server.
         cache: false,             // To unable request pages to be cached
         processData:false,
         dataType: "json",
         success:function(data)
         {
          def.resolve(data);
         },  
         error: function (data) {
           // toastr.info('No Result Data'); 
           def.reject();
         }
       })
       return def.promise();
  }

  function LoadMasterSuratOP(selector){
      var url = base_url_js+"__request-document-generator/__LoadMasterSuratAccess";
      var data = {
        Active : 1,
      }
      var token =  jwt_encode(data,'UAP)(*');
      AjaxSubmitTemplate(url,token).then(function(response){
          var dt = response.data;
          selector.empty();
          selector.append('<option value = "-" selected>'+msgMasterDocument+'</option>');
          selector.removeClass('form-control');
          selector.addClass('select2-select-00 full-width-fix');
          for (var i = 0; i < dt.length; i++) {
            selector.append('<option value = "'+dt[i][5]+'" datatoken = "'+dt[i][6]+'" >'+dt[i][2]+'</option>');
          }

          selector.select2({
                     });

      }).fail(function(response){
         toastr.error('Connection error,please try again');
      })
  }

  $(document).off('change', '#DepartmentIDChoose').on('change', '#DepartmentIDChoose',function(e) {
    var DepartmentIDChoose = $(this).find('option:selected').val();
    var url = base_url_js+"__request-document-generator/__LoadSessionDepartment";
    var data = {
      DepartmentIDChoose : DepartmentIDChoose,
    }
    var token =  jwt_encode(data,'UAP)(*');
    loadingStart();
    AjaxSubmitTemplate(url,token).then(function(response){
       setTimeout(function () {
          location.reload();
       },1500);
       loadingEnd(2000);
    })
  })

</script>
