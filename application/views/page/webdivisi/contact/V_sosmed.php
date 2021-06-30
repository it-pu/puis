
<div class="row" style="margin-top: 30px;">
    <div class="col-md-6">
        <table class="table">
            <tr>
                <td style="width: 15%;">Sosial Media</td>
                <td style="width: 1%;">:</td>
                <td>
                    <select style="max-width: 150px;" id="sosmed" class="form-control input" name="Icon">
                      <option value="fa-facebook">Facebook</option>
                      <option value="fa-instagram">Instagram</option>
                      <option value="fa-twitter">Twitter</option>
                      <option value="fa-youtube">Youtube</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td style="width: 15%;">http://</td>
                <td style="width: 1%;">:</td>
                <td>
                    <input type="text" id="Link" class="form-control required input" placeholder="ex: http://example.com" name="Link">
                </td>
            </tr>
           <!-- <tr>
                <td style="width: 15%;">Icon Sosmed</td>
                <td style="width: 1%;">:</td>
                <td>
                  <input type="file" id="uploadFile" name="uploadFile" accept="jpg/png">
                  <span class="red">* Size weight x height 36px x 36px</span>
                
                </td>
            </tr> -->
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

<script>
    var App_V_sosmed = {
      validation : function(arr){
          var toatString = "";
              var result = "";
              for(var key in arr) {
                 switch(key)
                 {
                    case 'link' :
                        var string = App_V_sosmed.jssclean(arr[key]).trim();
                        result = Validation_required(string,key);
                        if (result['status'] == 0) {
                          toatString += result['messages'] + "<br>";
                        }
                    break;

                    default:
                      var string = App_V_sosmed.jssclean(arr[key]).trim();
                      result = Validation_required(string,key);
                      if (result['status'] == 0) {
                        toatString += result['messages'] + "<br>";
                      }
                 }
              }
              if (toatString != "") {
                toastr.error(toatString, 'Failed!!');
                return false;
              }
              return true
      },

      SubmitData : function(action='saveContactSosmed',ID='',selector,nmtbn='Save'){
       var data = {};
          $('.input').each(function(){
              var field = $(this).attr('name');
               data[field] = $(this).val(); 
          })
        // var ID = $(this).attr('data-id');  
        // alert(ID);
        var form_data = new FormData();
        var find = true;
        var thisbtn = $(this);
            
            $('input[type="file"]').each(function(){
                var IDFile = $(this).attr('id');
                var ev = $(this);
                var NameItem = 'ID '+IDFile;
                if (!file_validation2(ev,NameItem)) {
                  find = false;
                  return false;
                }
            })
            if (find) {

                if ( $( '#'+'uploadFile').length ) { // jika upload file
                      var UploadFile = $('#'+'uploadFile')[0].files;
                        for(var count = 0; count<UploadFile.length; count++)
                        {
                         form_data.append("uploadFile[]", UploadFile[count]);
                        }
                      }
                     
                var validation =  (action == 'deleteDatasosmed') ? true : App_V_sosmed.validation(dataform);
                if (validation) {
                    if (confirm('Are you sure ?')) {
                        var dataform = {
                            ID : ID,
                            data : data,
                            action : action
                           
                        };
                        // console.log(dataform);return;
                        var token = jwt_encode(dataform,"UAP)(*");
                        form_data.append('token',token);
                        loading_button2(selector);
                        var url = base_url_js + "api-webdivisi/__crudDataDivisi";
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
                            setTimeout(function () {
                               end_loading_button2(selector,nmtbn);
                               toastr.success('Success');
                               location.reload();
                            },1000);

                          },
                          error: function (data) {
                              toastr.error("Connection Error, Please try again", 'Error!!');
                              end_loading_button2(selector,nmtbn); 
                          }
                        })
                        // $.post(url,{ token:token },function (resultJson) {
                                
                        // }).done(function(resultJson) {
                            
                        //     setTimeout(function () {
                        //        end_loading_button2(selector);
                        //        toastr.success('Success');
                        //        location.reload();
                        //     },1000);
                        // }).fail(function() {
                        //     toastr.error("Connection Error, Please try again", 'Error!!');
                        //     end_loading_button2(selector); 
                        // }).always(function() {
                        //      end_loading_button2(selector);              
                        // }); 
                    }
                }
              }

      },

      Loaded : function(){
        var data = {
            action : 'readContactSosmed',
        };
        var token = jwt_encode(data,'UAP)(*');
        var url = base_url_js+'api-webdivisi/__crudDataDivisi';
        var locimg = base_url_js+'images/icon/';
        $.post(url,{token:token},function (jsonResult) {
            $('#viewDataDesc').empty();
            if(jsonResult.length>0){

                $.each(jsonResult,function (i,v) {
                    $('#viewDataDesc').append('<div class="col-lg-4 col-md-6">'+
                      '<div class="card" style="text-align: center; padding: 15px;">'+
                      '<div class="card-body">'+                       
                        
                        '<p class="card-text"><a target="_blank" href="'+v.Link+'">'+
                        '<h5 class="card-title"><i class="fa '+v.Icon+'" style="line-height: 1;  width: 50px; height: 50px; background: #083f88; padding: 10px; border-radius: 50%; font-size: 2em; color: #fff;"> </i></h5>'+
                        '</a></p>'+
                        '<p class="card-text"><a href="javascript:void(0);" data-id="'+v.ID+'" class="btn btn-red btn-alert-hapus" id="btn-hapus">(Delete)</a></p>'+
                        
                      '</div>'+
                    '</div>');
                });

            } else {
                $('#viewDataDesc').html('<div class="well">Data not yet</div>');
            }

        });

      },

      jssclean : function(string){
        var div = document.createElement('div');
        div.innerHTML = string;
        var scripts = div.getElementsByTagName('script');
        var i = scripts.length;
        while (i--) {
          scripts[i].parentNode.removeChild(scripts[i]);
        }
        return div.innerHTML;
      },
  };

  $(document).ready(function(){
    App_V_sosmed.Loaded();
  })


  $('#btnSave').click(function () {
    var ID = $(this).attr('data-id');
    var selector = $(this);
    var action = $(this).attr('action');
    App_V_sosmed.SubmitData(action,ID,selector);
  });
   $(document).off('click', '#btn-hapus').on('click', '#btn-hapus',function(e) {
   
    var ID = $(this).attr('data-id');
    // alert(ID);
    // console.log(ID);return;
    var selector = $(this);
    var action = 'deleteDatasosmed'
    App_V_sosmed.SubmitData(action,ID,selector,'Hapus');
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
