
<div class="row" style="margin-top: 30px;">
    <div class="col-md-6">
        <table class="table">
            <tr>
                <td style="width: 15%;">Email</td>
                <td style="width: 1%;">:</td>
                <td>
                    <input type="text" id="Email" class="form-control required  input" placeholder="ex: example@podomorouniversity.ac.id" name = "Email">
                </td>
            </tr>
           <tr>
                <td style="width: 15%;">Telphone</td>
                <td style="width: 1%;">:</td>
                <td>
                    <input type="text" id="Tlp" class="form-control required  input" placeholder="ex: 021XXXXXXXXX" name="Tlp">
                </td>
            </tr>
            <tr>                
                <td>Open & Close</td>
                <td>:</td>
                <td>
                    <textarea id="OpenClose" class="form-control input" name="OpenClose" placeholder="Open and Close University"></textarea>
                </td>
            </tr>
            <tr>                
                <td>Address</td>
                <td>:</td>
                <td>
                    <textarea id="Address" class="form-control input" name="Address" placeholder="Address"></textarea>
                </td>
            </tr>
            <tr>
                <td colspan="3" style="text-align: right;">
                    <button class="btn btn-success" id="btnSave" action= "saveContactDetail" data-id ="">Save</button>
                </td>
            </tr>
        </table>
    </div>

    <div class="col-md-6" style="border-left: 1px solid #CCCCCC;">
        <div id="viewDataDesc"></div>
    </div>
</div>

<script>
    var App_V_address = {
      validation : function(arr){
          var toatString = "";
              var result = "";
              for(var key in arr) {
                 switch(key)
                 {
                    case 'Email' :
                        var string = App_V_address.jssclean(arr[key]).trim();
                        result = Validation_email(string,key);
                        if (result['status'] == 0) {
                          toatString += result['messages'] + "<br>";
                        }
                    break;
                    case 'Tlp' :
                        var string = App_V_address.jssclean(arr[key]).trim();
                        result = Validation_numeric(string,key);
                        if (result['status'] == 0) {
                          toatString += result['messages'] + "<br>";
                        }
                    break; 

                    default:
                      var string = App_V_address.jssclean(arr[key]).trim();
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

      SubmitData : function(action='saveContactDetail',ID='',selector){
          var data = {};
          $('.input').each(function(){
              var field = $(this).attr('name');
               data[field] = $(this).val(); 
          })
         console.log(data);
          var validation =  (action == 'delete') ? true : App_V_address.validation(data);
          if (validation) {
              if (confirm('Are you sure ?')) {
                  var dataform = {
                     
                      data : data,
                      action : action,
                     
                  };
                  var token = jwt_encode(dataform,"UAP)(*");
                  loading_button2(selector);
                  var url = base_url_js + "api-prodi/__crudDataProdi";
                  $.post(url,{ token:token },function (resultJson) {
                          
                  }).done(function(resultJson) {
                      
                      setTimeout(function () {
                         end_loading_button2(selector);
                         toastr.success('Success');
                         location.reload();
                      },1000);
                  }).fail(function() {
                      toastr.error("Connection Error, Please try again", 'Error!!');
                      end_loading_button2(selector); 
                  }).always(function() {
                       end_loading_button2(selector);              
                  }); 
              }
          }

      },

      Loaded : function(){
        var data = {
            action : 'readContactAddress',
        };
        var token = jwt_encode(data,'UAP)(*');
        var url = base_url_js+'api-prodi/__crudDataProdi';

        $.post(url,{token:token},function (jsonResult) {
            $('#viewDataDesc').empty();
            if(jsonResult.length>0){

                $.each(jsonResult,function (i,v) {
                    $('#viewDataDesc').append('<div class="well"><h3 style="margin-top: 5px;"><b>Address</b></h3><div><b>Address :</b>'+v.Address+'</div><div><b>Open & Close :</b>'+v.OpenClose+'</div><div><b>Email :</b>'+v.Email+'</div><div><b>Tlp :</b>'+v.Tlp+'</div></div>');
                    $('#Email').val(v.Email);
                    $('#Tlp').val(v.Tlp);
                    $('#OpenClose').append(v.OpenClose);
                    $('#Address').append(v.Address);
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
    App_V_address.Loaded();
  })


  $('#btnSave').click(function () {
    var ID = $(this).attr('data-id');
    var selector = $(this);
    var action = $(this).attr('action');
    App_V_address.SubmitData(action,ID,selector);
  });

</script>
