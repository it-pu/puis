<style type="text/css">
  /* FANCY COLLAPSE PANEL STYLES */
  .fancy-collapse-panel .panel-default > .panel-heading {
  padding: 0;

  }
  .fancy-collapse-panel .panel-heading a {
  padding: 12px 35px 12px 15px;
  display: inline-block;
  width: 100%;
  background-color: #EE556C;
  color: #ffffff;
  position: relative;
  text-decoration: none;
  }
  .fancy-collapse-panel .panel-heading a:after {
  font-family: "FontAwesome";
  content: "\f147";
  position: absolute;
  right: 20px;
  font-size: 20px;
  font-weight: 400;
  top: 50%;
  line-height: 1;
  margin-top: -10px;
  }

  .fancy-collapse-panel .panel-heading a.collapsed:after {
  content: "\f196";
  }
</style>
<script type="text/javascript" src="<?php echo base_url();?>assets/custom/jquery.maskMoney.js"></script>
<div style="padding-top: 30px;border-top: 1px solid #cccccc">
    <div class="row">
       <div class="col-xs-12" >
        <div class="panel panel-primary">
               <div class="panel-heading clearfix">
                   <h4 class="panel-title pull-left" style="padding-top: 7.5px;">Supplier</h4>
               </div>
               <div class="panel-body" id = "pageContentCatalog">
                    <div class="tabbable tabbable-custom tabbable-full-width btn-read MenuSupplier">
                        <ul class="nav nav-tabs">
                            <li class="active">
                                <a href="javascript:void(0)" class="pageAnchorSupplier" page = "InputSupplier">Input</a>
                            </li>
                            <!-- <li class="">
                                <a href="javascript:void(0)" class="pageAnchorSupplier" page = "ApprovalSupplier">Approve<b style="color: red;" id= "CountApproval"></b></a>
                            </li> -->
                        </ul>
                        <div style="padding-top: 30px;border-top: 1px solid #cccccc">
                            <div id = "pageSupplier">
                               
                            </div>
                        </div>
                    </div>
               </div>
        </div>
       </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        LoadPage('InputSupplier');

        $(".pageAnchorSupplier").click(function(){
            var Page = $(this).attr('page');
            $(".MenuSupplier li").removeClass('active');
            $(this).parent().addClass('active');
            LoadPage(Page)
        });
    }); // exit document Function

    function LoadPage(page)
    {
      loading_page("#pageSupplier");
      var url = base_url_js+'purchasing/page/supplier/'+page;
      $.post(url,function (resultJson) {
          var response = jQuery.parseJSON(resultJson);
          var html = response.html;
          var jsonPass = response.jsonPass;
          $("#pageSupplier").html(html);
      }); // exit spost
    }

    function file_validation(ID_element)
    {
        var files = $('#'+ID_element)[0].files;
        var error = '';
        var msgStr = '';
       var name = files[0].name;
        console.log(name);
        var extension = name.split('.').pop().toLowerCase();
        if(jQuery.inArray(extension, ['xlsm','xlsx']) == -1)
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

    $(document).on('click','#sbmtimportfile', function () {
      loading_button('#sbmtimportfile');
      var chkfile = file_validation('ImportFile');
      if (chkfile) {
        var form_data = new FormData();
        var url = base_url_js + "purchasing/page/supplier/import_data";
        var files = $('#ImportFile')[0].files;  
        form_data.append("fileData", files[0]);
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
            if(data.status == 1) {
              toastr.options.fadeOut = 100000;
              toastr.success(data.msg, 'Success!');
                // $('.pageAnchor[page="FormInput"]').trigger('click');
                  if (CountColapses2 == 0) {
                    $('.pageAnchor[page="DataIntable"]').trigger('click');
                    // LoadPageSupplier('DataIntable');
                    }
                  else
                  {
                    LoadPageSupplier('DataIntable');
                  }
            }
            else
            {
              toastr.options.fadeOut = 100000;
              toastr.error(data.msg, 'Failed!!');
            }
          setTimeout(function () {
              toastr.clear();
          $('#sbmtimportfile').prop('disabled',false).html('Save');
            },1000);

          },
          error: function (data) {
            toastr.error(data.msg, 'Connection error, please try again!!');
            $('#sbmtimportfile').prop('disabled',false).html('Save');
          }
        })

      }
      else
      {
         $('#sbmtimportfile').prop('disabled',false).html('Save');
      }  
    });
</script>