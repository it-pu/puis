<link href="<?php echo base_url('assets/custom/jquery-ui.css'); ?>" rel="stylesheet" type="text/css" />
<style>
    .row-sma {
        padding-top: 5px;
        padding-bottom: 5px;
    }

    .form-time {
        padding-left: 0px;
        padding-right: 0px;
    }
    .row-sma .fa-plus-circle {
        color: green;
    }
    .row-sma .fa-minus-circle {
        color: red;
    }
    .btn-action {

        text-align: right;
    }

    #tableDetailTahun thead th {
        text-align: center;
    }

    .form-filter {
        margin-top: 10px;
        padding-top: 10px;
        border-top: 1px solid #ccc;
    }
    .filter-time {
        padding-left: 0px;
    }
</style>

<!-- <div class="row" style="margin-top: 30px;">
    <div class="col-md-12">
        <div class="widget box">
            <div class="widget-header">
                <h4 class="header"><i class="icon-reorder"></i> <?php echo $NameMenu ?></h4>
            </div>
            <div class="widget-content">
                <div class = "row">
                    <div class="col-md-2" style="">
                        Prody
                        <select class="select2-select-00 col-md-4 full-width-fix" id="selectProgramStudy">
                            <option></option>
                        </select>
                    </div>
                    <div class="col-md-2" style="">
                        Nama
                        <input class="form-control" id="Nama" placeholder="All..." "="">
                    </div>
                    <div class="col-md-2" style="">
                        No Formulir
                        <input class="form-control" id="No_Formulir" placeholder="All..." "="">
                    </div>
                    <div class="col-md-2" style="">
                        Sekolah
                        <input class="form-control" id="Sekolah" placeholder="All..." "="">
                    </div>
                    <div  class="col-md-4" align="right" id="pagination_link"></div>
                </div>
                <div class="row" style="margin-top: 10px;margin-left: 0px;margin-right: 0px">
                    <div id="pageData">
                        
                    </div>
                </div>
                
                <br>
                <div class="col-md-12" align = "right">
                   <button class="btn btn-inverse btn-notification btn-submit" id="btn-submit">Submit</button>
                </div>
            </div>
        </div>
    </div>
</div> -->
<h4>Comming Soon</h4>


<script type="text/javascript">
    window.temp = '';
    $(document).ready(function () {
        loadProgramStudy();
        loadData(1);
        loadAutoCompleteNama();
        autoCompleteSchool($("#Sekolah"))
    });

    $(document).on("click", ".pagination li a", function(event){
      event.preventDefault();
      var page = $(this).data("ci-pagination-page");
      loadData(page)
      // loadData_register_document(page);
    });

    function getValueChecbox()
    {
         var allVals = [];
         $('.tableData :checked').each(function() {
            if($(this).val() != 'nothing')
            {
                allVals.push($(this).val());
            }
           
         });
         return allVals;
    }

    function loadData(page)
    {
        loading_page('#pageData');
        var url = base_url_js+'admission/proses-calon-mahasiswa/loadData_calon_mahasiswa/'+page;
        var Nama = $("#Nama").val();
        var selectProgramStudy = $("#selectProgramStudy").val();
        var No_Formulir = $("#No_Formulir").val();
        selectProgramStudy = (selectProgramStudy == '') ? '%' : selectProgramStudy;
        // var Sekolah = $("#Sekolah").val();
        data = {
            Nama : Nama,
            selectProgramStudy : selectProgramStudy,
            Sekolah : temp,
            No_Formulir : No_Formulir,
        }
        var token = jwt_encode(data,"UAP)(*");
        $.post(url,{token:token},function (data_json) {
            // jsonData = data_json;
            var obj = JSON.parse(data_json); 
            // console.log(obj);
            setTimeout(function () {
                $("#pageData").html(obj.loadtable);
                $("#pagination_link").html(obj.pagination_link);
            },500);
        }).done(function() {
          
        }).fail(function() {
          toastr.error('The Database connection error, please try again', 'Failed!!');
        }).always(function() {
          // $('#btn-dwnformulir').prop('disabled',false).html('Formulir');
        });
    }

    $(document).on('click','#btn-submit', function () {
        loading_button('#btn-submit');
        var chkValue = getValueChecbox();
        // console.log(test);
        if (chkValue.length == 0) {
            toastr.error("Silahkan checked dahulu", 'Failed!!');
            $('#btn-submit').prop('disabled',false).html('Submit');
        }
        else
        {
            var url = base_url_js+'admission/proses-calon-mahasiswa/submit_ikut_ujian';
            var data = {
                                chkValue : chkValue,
                            };
            var token = jwt_encode(data,"UAP)(*");
            $.post(url,{token:token},function (data_json) {
                /*var obj = JSON.parse(data_json);
                if(obj != 'Success')
                {
                    toastr.error(obj, 'Failed!!');
                }
                $('#btn-submit').prop('disabled',false).html('Submit');*/
                
            }).done(function() {
                setTimeout(function () {
                    temp = '';
                    loadData(1);
                },500);
            }).fail(function() {
              toastr.error('The Database connection error, please try again', 'Failed!!');;
              $('#btn-submit').prop('disabled',false).html('Submit');
            }).always(function() {
                $('#btn-submit').prop('disabled',false).html('Submit');
            });
        }
    });

    function loadProgramStudy()
    {
        var url = base_url_js+"api/__getBaseProdiSelectOption";
        $('#selectProgramStudy').empty();
        $.post(url,function (data_json) {
              $('#selectProgramStudy').append('<option value="'+'%'+'" selected>'+'All'+'</option>');
              for(var i=0;i<data_json.length;i++){
                  // var selected = (i==0) ? 'selected' : '';
                  //var selected = (data_json[i].RegionName=='Kota Jakarta Pusat') ? 'selected' : '';
                  $('#selectProgramStudy').append('<option value="'+data_json[i].ID+'" '+''+'>'+data_json[i].Name+'</option>');
              }
              $('#selectProgramStudy').select2({
                 //allowClear: true
              });
        }).done(function () {
          
        });
    }

    function loadAutoCompleteNama()
    {
        $("#Nama").autocomplete({
          minLength: 3,
          select: function (event, ui) {
            event.preventDefault();
            var selectedObj = ui.item;
            $("#Nama").val(selectedObj.value);
            var Sekolah = $("#Sekolah").val();  
            temp = (Sekolah == '') ? '' : temp; 
            loadData(1); 
          },
          /*select: function (event,  ui)
          {

          },*/
          source:
          function(req, add)
          {
            var url = base_url_js+'admission/master-calon-mahasiswa/showAutoComplete';
            var Nama = $('#Nama').val();
            var data = {
                        Nama : Nama,
                        };
            var token = jwt_encode(data,"UAP)(*");          
            $.post(url,{token:token},function (data_json) {
                var obj = JSON.parse(data_json);
                add(obj.message) 
            })
          } 
        })

    }

    function autoCompleteSchool(ID)
    {
        ID.autocomplete({
          minLength: 4,
          select: function (event, ui) {
            event.preventDefault();
            var selectedObj = ui.item;
            ID.val(selectedObj.label);
            temp = selectedObj.value;
            loadData(1); 
          },
          source:
          function(req, add)
          {
            loadingStart();
            var url = base_url_js+'api/__getAutoCompleteSchool';
            var School = ID.val();
            var data = {
                        School : School,
                        };
            var token = jwt_encode(data,"UAP)(*");          
            $.post(url,{token:token},function (data_json) {
                // var obj = JSON.parse(data_json);
                add(data_json.message); 
                loadingEnd(1000);
            })
          } 
        })
    }

    $(document).on('click','#dataResultCheckAll', function () {
        $('input.uniform').not(this).prop('checked', this.checked);
    });

    $(document).on("change", "#selectProgramStudy", function(event){
      var Sekolah = $("#Sekolah").val();  
      temp = (Sekolah == '') ? '' : temp; 
      loadData(1)
    });

    $(document).on("keypress", "#No_Formulir", function(event){
        var code = event.keyCode || event.which;
        if(code==13){
            loadData(1);
        }
      
    });

</script>