<style type="text/css">
.nav-tabs > li > a {
    background-color: #ececec;
}
</style>
<?php $Segment1 = $this->uri->segment(2); ?>
<?php $Segment2 = $this->uri->segment(3); ?>
<div class="col-xs-12">
    <div class="well" style="padding-bottom: 30px">
        <div class="panel-heading clearfix">
            <h3 >
                Set <?= ucwords(str_replace("-"," ",$Segment1)); ?>
            </h3>
            <p id="idDisplay"></p>
        </div>
        <div class="panel-body" id="tabs">
            <button class="btn btn-success" onclick="add_podivers()"><i class="glyphicon glyphicon-plus"></i> Create</button>
            <button class="btn btn-success" onclick="SetPodivers()"><i class="glyphicon glyphicon-plus"></i> Add Set Group</button>
            <button class="btn btn-default" onclick="reload_table()"><i class="glyphicon glyphicon-refresh"></i> Reload</button>
            <hr>            
                        
            <div class="table-responsive">
              <table class="table table-condensed table-striped" id="table">
                  <thead>
                    <tr>
                      <!-- <th>#</th> -->
                      <th>NIP</th>
                      <th>Name</th>
                      <th>Set Master Group</th>
                      <th>Set Group</th>
                      <th>Date Update</th>
                      <!-- <th>Lang</th> -->
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody id="viewDatalist">
                    
                  </tbody>
                </table>
            </div>
        

        </div>
    </div>
</div>
<style type="text/css">.form-horizontal .form-group {
    padding: 5px 15px;
}</style>



<!-----Modal set group--->
<div class="modal fade " id="modal_SetGroup" tabindex="-1" role="dialog" aria-labelledby="largeModal" aria-hidden="true">
    <div class="modal-dialog modal-md" style="width: 50%">
        <div class="modal-content">
        <div class="modal-header" style="padding: 20px 35px">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h3 class="modal-title" id="myModalLabel"><span id="cng1"></span> Group <?= ucwords(str_replace("-"," ",$Segment1)); ?></h3>
        </div>
        
        <div class="modal-body form">
            <form class="getcategory" id="SetGroup" style="margin: 0 15px;;">                     
                <input type="hidden" value="" name="idSetGroup"/>  
                <div class="form-group" style="margin-bottom: 0px">
                    <label>Group Name</label>                       
                    <input name="groupname" placeholder="Group Name" class="form-control" type="text">
                    <span class="help-block"></span>
                </div> 
                
                <div class="form-group">
                    <div class="btn btn-success" id="btnSetGroup" onclick="add_SetGroup()">Save</div>
                </div>
            </form> 
            <div class="panel-body" style="min-height: 100px;">
                <div class="table-responsive">
                    <table class="table table-condensed table-striped">
                        <thead>
                            <tr>
                                <td style="width: 8%">No</td>
                                <td>Group Name</td>
                                <td>Action</td>
                            </tr>
                        </thead>
                        <tbody id="viewDatalistSetGroup">                            
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- <div class="modal-footer">
                <button type="button" id="btnSave" onclick="save()" class="btn btn-info">Save</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
            </div> -->

        </div>
        
        </div>
    </div>
</div>




<!-- MODAL ADD -->
    <div class="modal fade " id="modal_form" tabindex="-1" role="dialog" aria-labelledby="largeModal" aria-hidden="true">
        <div class="modal-dialog modal-lg" style="width: 50%">
        <div class="modal-content">
        <div class="modal-header" style="padding: 20px">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h3 class="modal-title" id="myModalLabel">User Accesss <?= ucwords(str_replace("-"," ",$Segment1)); ?></h3>
        </div>
        <form action="#" id="form" class="form-horizontal">
        <div class="modal-body form">
           
            <input type="hidden" value="" name="idset"/>
            <input type="hidden" value="" name="idpodivers"> 
            <div class="form-group">
                <label>Search Student</label>
                <input id="filterStudent" class="form-control" placeholder="Search student by NPM or Name">
                <input id="MedicalRecordID" value="'+MedicalRecordID+'" class="hide">
                <input id="NPM" class="hide formMedicalRecord">
                <div style="margin-top: 15px;margin-bottom: 15px;"><table class="table table-striped table-centre"><tbody id="loadStudent"></tbody></table></div><hr/>
                </div>
                
                <div class="form-group">
                <label>Student Selected</label>
                <div id="viewStudent">-</div>
            </div>
            <div class="form-group" style="margin-bottom: 0px">                
                <div class="form-group" style="margin-bottom: 0px">
                    <label>Select Master Group</label>
                    <select  name="ID_master_group" class="form-control getSetcontent" id="showSetMasterGroup">
                      <option value="">--Select--</option>
                        <!-- <?php foreach($setgroup as $row):?>
                        <option value="<?php echo $row->ID_set_group;?>" ><?php echo $row->GraoupName;?></option>
                        <?php endforeach;?>      -->                  
                    </select>
                    <span class="help-block"></span>
                </div> 
                <span class="help-block"></span>
            </div>

            <div class="form-group" style="margin-bottom: 0px">                
                <div class="form-group" style="margin-bottom: 0px">
                    <label>Select Group</label>
                    <select  name="ID_set_group" class="form-control getSetcontent" id="showSetGroup">
                      <option value="">--Select--</option>
                        <!-- <?php foreach($setgroup as $row):?>
                        <option value="<?php echo $row->ID_set_group;?>" ><?php echo $row->GraoupName;?></option>
                        <?php endforeach;?>      -->                  
                    </select>
                    <span class="help-block"></span>
                </div> 
                <span class="help-block"></span>
            </div>

            <div class="form-group" style="margin-bottom: 0px">                
                <div class="form-group" style="margin-bottom: 0px">
                    <label>Set Member</label>
                    <select  name="ID_set_member" class="form-control getSetcontent" id="showSetMember">
                      <option value="">--Select--</option>
                        <!-- <?php foreach($setgroup as $row):?>
                        <option value="<?php echo $row->ID_set_group;?>" ><?php echo $row->GraoupName;?></option>
                        <?php endforeach;?>      -->                  
                    </select>
                    <span class="help-block"></span>
                </div> 
                <span class="help-block"></span>
            </div>

        </div>

        <div class="modal-footer">
            <button type="button" id="btnSave" onclick="save()" class="btn btn-info">Save</button>
            <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
        </div>


        </div>
        
    </div>
<!--END MODAL ADD-->
<script>

    $( function() {
        $('.datetimepicker').datetimepicker();
    } );

</script>

<script>


    $(document).on('keyup','#filterStudent',function () {

        var filterStudent = $('#filterStudent').val();

        if(filterStudent!='' && filterStudent!=null) {
            var url = base_url_js + 'api/__getStudentsServerSide';
            $.post(url,{key : filterStudent},function (jsonResult) {

                $('#loadStudent').empty();
                if(jsonResult.length>0){
                    $.each(jsonResult,function (i,v) {
                        $('#loadStudent').append('<tr>' +
                            '<td style="width: 1%;">'+(i+1)+'</td>' +
                            '<td style="text-align: left;"><b>'+v.Name+'</b><br/>'+v.NPM+'</td>' +
                            '<td style="width: 5%;"><div class="btn btn-sm btn-default btnAddStudent" data-name="'+v.Name+'" data-npm="'+v.NPM+'"><i class="fa fa-plus"></i></div></td>' +
                            '</tr>');
                    });
                } else {
                    $('#loadStudent').html('<tr>' +
                        '<td colspan="3">Student not yet</td>' +
                        '</tr>');
                }

            });
        }

    });
    $(document).on('click','.btnAddStudent',function () {
        var Name = $(this).attr('data-name');
        var NPM = $(this).attr('data-npm');

        $('#viewStudent').html('<input type="text" name="npm" value="'+NPM+'" class="form-control hide"><b style="color: darkgreen;">'+NPM+' - '+Name+'</b>');
        $('#NPM').val(NPM);

    }); 
       

    var save_method; //for save method string
    var table;
     
    $(document).ready(function() {
     
        //datatables
        table = $('#table').DataTable({ 
     
            "processing": true, //Feature control the processing indicator.
            "serverSide": true, //Feature control DataTables' server-side processing mode.
            "ordering": true, // Set true agar bisa di sorting
            "order": [[ 0, 'desc' ]], // Default sortingnya berdasarkan kolom / field ke 0 (paling pertama)
     
            // Load data for the table's content from an Ajax source
            "ajax": {               
                "url": base_url_js+'__ajaxlist_podivers',
                "type": "POST",
                "data": function ( data ) {
                        data.type = '<?= $Segment1 ?>';                     
                    }
            },   
            "deferRender": true,
            "aLengthMenu": [[5, 10, 50,100],[ 5, 10, 50,100]], // Combobox Limit
            
            //Set column definition initialisation properties.
            "columnDefs": [
                    { 
                        "searchable": false,
                        "targets": [ 0 ], //first column / numbering column
                        "orderable": false, //set not orderable
                    },
       
            ],
     
        });

        //set input/textarea/select event when change value, remove class error and remove text help block 
        $("input").change(function(){
            $(this).parent().parent().removeClass('has-error');
            $(this).next().empty();
        });
        $("textarea").change(function(){
            $(this).parent().parent().removeClass('has-error');
            $(this).next().empty();
        });
        $("select").change(function(){
            $(this).parent().parent().removeClass('has-error');
            $(this).next().empty();
        });
     
    });     

// Craete content
    $(document).ready(function(){        
            var id=$(this).val();
            $.ajax({
                url : base_url_js+'_crudSetMasterGroup',
                method : "POST",
                data : {id: id},
                async : true,
                dataType : 'json',
                success: function(data){                     
                    var html = '';
                    var i;
                    for(i=0; i<data.length; i++){
                        if(i==0){
                            html += '<option value="'+'%'+'">'+'--All--'+'</option>';                            
                        }
                        html += '<option value='+data[i].ID_master_group+'>'+data[i].MasterGroupName+'</option>';
                    }
                    $('#showSetMasterGroup').html(html);
                }
            });
            return false;
    });

    // $(document).ready(function(){        
    //         var id=$('.getSetcontent[name="ID_master_group"]').val();
    //         alert(id);
    //         $.ajax({
    //             url : base_url_js+'_crudSetGroup',
    //             method : "POST",
    //             data : {id: id},
    //             async : true,
    //             dataType : 'json',
    //             success: function(data){                     
    //                 var html = '';
    //                 var i;
    //                 for(i=0; i<data.length; i++){
    //                     html += '<option value='+data[i].ID_set_group+'>'+data[i].GroupName+'</option>';
    //                 }
    //                 $('#showSetGroup').html(html);
    //             }
    //         });
    //         return false;
    // });

    $(document).ready(function(){
        loadDataSetGroup();
    })

    function loadDataSetGroup(dataMaster) {
        var id=dataMaster;
        $.ajax({
            url : base_url_js+'_crudSetGroup',
            method : "POST",
            data : {id: id},
            async : true,
            dataType : 'json',
            success: function(data){                     
                var html = '';
                var i;
                for(i=0; i<data.length; i++){
                    html += '<option value='+data[i].ID_set_group+'>'+data[i].GroupName+'</option>';
                }
                $('#showSetGroup').html(html);
            }
        });
        return false;
    }

    $(document).ready(function(){        
            var id=$(this).val();
            $.ajax({
                url : base_url_js+'page/student-life/c_studentlife/crudSetMember',
                method : "POST",
                data : {id: id},
                async : true,
                dataType : 'json',
                success: function(data){                     
                    var html = '';
                    var i;
                    for(i=0; i<data.length; i++){
                        html += '<option value='+data[i].ID_set_member+'>'+data[i].MemberName+'</option>';
                    }
                    $('#showSetMember').html(html);
                }
            });
            return false;
    });


    function add_podivers()
    {
        save_method = 'add';
        $('#form')[0].reset(); // reset form on modals
        $('.form-group').removeClass('has-error'); // clear error class
        $('.help-block').empty(); // clear error string
        $('#modal_form').modal('show'); // show bootstrap modal
        $('.modal-title').text('Build Content Podivers'); // Set Title to Bootstrap modal title
    }

    function edit_podivers(id)
    {
        $('#modal_form').modal('show'); // show bootstrap modal
        $('.modal-title').text('Update Build Content Podivers'); // Set Title to Bootstrap modal title
        save_method = 'update';
        $.ajax({
            url : base_url_js+'__ajaxedit_podivers/'+id,
            type: "GET",
            dataType: "JSON",
            success: function(data)
            {
                $('[name="ID_set_group"]').val(data.ID_set_group);
                $('[name="idset"]').val(data.ID_set_group);
                $('[name="idpodivers"]').val(data.ID_set_list_member).trigger('change');
                $('[name="npm"]').val(data.NIPNPM);
                $('[name="name"]').val(data.Name);
                $('[name="setgroup"]').val(data.ID_set_group).trigger('change');
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                alert('Error get data from ajax');
            }
        });
    } 
     
    function reload_table()
    {
        table.ajax.reload(null,false); //reload datatable ajax 
    }
     
    function save()
    {
        $('#btnSave').text('saving...'); //change button text
        $('#btnSave').attr('disabled',true); //set button disable 
        var url;
     
        if(save_method == 'add') {
            url = base_url_js+'__ajax_addpodivers';
        } else {
            url = base_url_js+'__ajaxupdate_podivers';
        }
        var NPM = $(this).attr('data-npm');
        // ajax adding data to database
        var formData = new FormData($('#form')[0]);

        $.ajax({
            url : url,
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            dataType: "JSON",
            success: function(data)
            {
     
                if(data.status) //if success close modal and reload ajax table
                {
                    toastr.success('Data saved','Success');
                    $('#modal_form').modal('hide');
                    reload_table();
                }
                else
                {
                    for (var i = 0; i < data.inputerror.length; i++) 
                    {
                        $('[name="'+data.inputerror[i]+'"]').parent().parent().addClass('has-error'); //select parent twice to select div form-group class and add has-error class
                        $('[name="'+data.inputerror[i]+'"]').next().text(data.error_string[i]); //select span help-block class set text error string
                    }
                }
     
                $('#btnSave').text('Save'); //change button text
                $('#btnSave').attr('disabled',false); //set button enable 
     
     
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                alert('Error adding / update data');
                $('#btnSave').text('Save'); //change button text
                $('#btnSave').attr('disabled',false); //set button enable 
     
            }
        });
    }
     
    function delete_podivers(id)
    {
        if(confirm('Are you sure delete this data?'))
        {
            // ajax delete data to database
            $.ajax({
                url : base_url_js+'__ajaxdelete_podivers/'+id,
                type: "POST",
                dataType: "JSON",
                success: function(data)
                {
                    //if success reload ajax table
                    toastr.success('Data delete','Success');
                    $('#modal_form').modal('hide');
                    reload_table();
                },
                error: function (jqXHR, textStatus, errorThrown)
                {
                    alert('Error deleting data');
                }
            });
     
        }
    }

    // set group
    function loadDataset() {
        
        $.ajax({
              type  : 'GET',
              url   : base_url_js+'_crudSetGroup',
              async : false,
              dataType : 'json',
              success : function(data){

                  var html = '';
                  var i;
                  for (i = 0; i < data.length; i++) {
                      html += '<tr>'+
                                '<td>'+(i+1)+'</td>'+
                                '<td>'+data[i].GroupName+'</td>'+
                                
                                '<td>'+
                                  '<a id="tab1-20933" class="btn btn-success" onclick="editSetGroup('+data[i].ID_set_group+')" href="javascript:;" data="'+data[i].ID_set_group+'"> Edit</a>'+
                                  '<a id="tab1-20933" class="btn btn-danger" onclick="deleteSetGroup('+data[i].ID_set_group+')" href="javascript:;" data="'+data[i].ID_set_group+'"> Delete</a>'+
                                '</td>'+
                              '</tr>';
                              // console.log(data[i].Name);
                  }
                  
                  $('#viewDatalistSetGroup').html(html);
              }
          })
    }

    function add_SetGroup()
    {
        $('#btnSetGroup').text('Saving...'); //change button text
        $('#btnSetGroup').attr('disabled',true); //set button disable 
        var url;
     
        if(save_method == 'add') {
            url = base_url_js+'__ajaxaddSetGroup';
        } else {
            url = base_url_js+'__ajaxupdateSetGroup';
        }
     
        // ajax adding data to database
        var category = new FormData($('#SetGroup')[0]);
        // console.log(category);
        $.ajax({
            url : url,
            type: "POST",
            data: category,
            contentType: false,
            processData: false,
            dataType: "JSON",
            success: function(data)
            {
     
                if(data.status) //if success close modal and reload ajax table
                {
                    toastr.success('Data saved','Success');
                    // $('#modal_form').modal('hide');
                    // reload_table();
                    loadDataset();
                    $('[name="groupname"]').val('');
                    $('#btnSetGroup').text('Save'); //change button text
                    $('#btnSetGroup').attr('disabled',false); //set button enable 
                    $('#cng1').text('Add')
                }
                
                           
                
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                alert('Error adding / update data');
                $('#btnSetGroup').text('Save'); //change button text
                $('#btnSetGroup').attr('disabled',false); //set button enable     
            }
        });
        return false;
    }

    function SetPodivers()
    {
        loadDataset();  
        save_method = 'add';
        $('#SetGroup')[0].reset(); // reset form on modals
        $('.form-group').removeClass('has-error'); // clear error class
        $('.help-block').empty(); // clear error string
        $('#modal_SetGroup').modal('show'); // show bootstrap modal
        $('.modal-title').text('Build Set Group'); // Set Title to Bootstrap modal title
    }

    function editSetGroup(id)
    {
        save_method = 'update';
        $.ajax({
            url : base_url_js+'__ajaxeditSetGroup/'+id,
            type: "GET",
            dataType: "JSON",
            success: function(data)
            {
                $('[name="ID_set_group"]').val(data.ID_set_group);
                $('[name="idSetGroup"]').val(data.ID_set_group);
                $('[name="groupname"]').val(data.GroupName);
                $('[name="groupname"]').focus();                     
                $('#btnSetGroup').text('Update')
                $('#cng1').text('Update')
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                alert('Error get data from ajax');
            }
        });
    }

    function deleteSetGroup(id)
    {
        if(confirm('Are you sure delete this data?'))
        {
            // ajax delete data to database
            $.ajax({
                url : base_url_js+'__ajaxdeleteSetGroup/'+id,
                type: "POST",
                dataType: "JSON",
                success: function(data)
                {
                    //if success reload ajax table
                    toastr.success('Data delete','Success');
                    $('#modal_form').modal('hide');
                    loadDataset();
                },
                error: function (jqXHR, textStatus, errorThrown)
                {
                    alert('Error deleting data');
                }
            });
     
        }
    }

    $(document).off('change', '#showSetMasterGroup').on('change', '#showSetMasterGroup',function(e) {
        var dataMaster = $('.getSetcontent[name="ID_master_group"]').val();
        var selectorOPsetGroup = $('.getSetcontent[name="ID_set_group"]').val('');
        // alert(dataMaster);
        loadDataSetGroup(dataMaster);
    })

</script>
