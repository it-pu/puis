<style type="text/css">
    button, input, select, textarea {
        /*margin: 7px;*/
        font-family: inherit;
        font-size: 100%;
    }
    .table-responsive {
        height: auto !important;  
        overflow-y: auto;
    }
</style>
    <form class="form-horizontal" id="formModal">
        <div class="form-group">
            <div class="row">
               <div class="col-sm-2">
                  <button class="btn btn-default btn-default-success" type="button" data-toggle="collapse" data-target="#listEquipment" aria-expanded="false" aria-controls="listEquipment">
                      <i class="fa fa-plus-circle" aria-hidden="true"></i> Show Equipment
                  </button>
              </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="collapse" id="listEquipment" style="margin-top: 10px;">
                        <div class="well">
                            <div class="row">
                                <div class="col-xs-12" id = "page_equipment_room">
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>   
            <div class="row">
                <div class="col-sm-3">
                    <label class="control-label">Room</label>
                </div>    
                <div class="col-sm-6">
                   <input type="text" name="Start" id= "Room" placeholder="Input Room" class="form-control" value="<?php echo $room ?>" readonly>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="row">
                <div class="col-sm-12 hide" align = 'center' id='msgMENU' style="color: red;">MSG</div>
            </div>   
            <div class="row">
                <div class="col-sm-3">
                    <label class="control-label">Start</label>
                </div>    
                <div class="col-sm-6">
                   <input type="text" name="Start" id= "Start" placeholder="Input Start" class="form-control" value="<?php echo $Start ?>" readonly>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="row">
                <div class="col-sm-12 hide" align = 'center' id='msgMENU' style="color: red;">MSG</div>
            </div>   
            <div class="row">
                <div class="col-sm-3">
                    <label class="control-label">End</label>
                </div>    
                <div class="col-sm-6">
                   <input type="text" name="Start" id= "Room" placeholder="Input Room" class="form-control" value="<?php echo $End ?>" readonly>
                </div>
            </div>
        </div>
        <div class="form-group"> 
            <div class="row">
                <div class="col-sm-3">
                    <label class="control-label">Agenda :</label>
                </div>    
                <div class="col-sm-6">
                   <input type="text" name="Agenda" id= "Agenda" placeholder="Input Agenda" class="form-control" value="<?php echo $Agenda ?>" readonly>
                </div>
            </div>
        </div>
        <div class="form-group"> 
            <div class="row">
                <div class="col-sm-3">
                    <label class="control-label">User :</label>
                </div>    
                <div class="col-sm-6">
                   <input type="text" name="Agenda" id= "Agenda" placeholder="Input Agenda" class="form-control" value="<?php echo $User ?>" readonly>
                </div>
            </div>
        </div>
        <div class="form-group"> 
            <div class="row">
                <div class="col-sm-3">
                    <label class="control-label">Equipment Additional :</label>
                </div>    
                <div class="col-sm-6">
                    <label class="control-label" style="text-align: left;"><?php echo $Name_equipment_add ?></label>
                </div>
            </div>
        </div>
        <div class="form-group"> 
            <div class="row">
                <div class="col-sm-3">
                    <label class="control-label"  style="text-align: left;">Person Support :</label>
                </div>    
                <div class="col-sm-6">
                    <label class="control-label"><?php echo $Name_add_personel ?></label> 
                </div>
            </div>
        </div>
        <div class="form-group"> 
            <div class="row">
                <div class="col-sm-3">
                    <label class="control-label"  style="text-align: left;">Markom Support :</label>
                </div>    
                <div class="col-sm-6">
                    <?php echo $MarkomSupport ?>
                </div>
            </div>
        </div>
        <div class="form-group"> 
            <div class="row">
                <div class="col-sm-3">
                    <label class="control-label"  style="text-align: left;">Participant Qty :</label>
                </div>    
                <div class="col-sm-6">
                    <label class="control-label"><?php echo $ParticipantQty ?></label> 
                </div>
            </div>
        </div>
        <div class="form-group"> 
            <div class="row">
                <div class="col-sm-3">
                    <label class="control-label"  style="text-align: left;">Seat Qty :</label>
                </div>    
                <div class="col-sm-6">
                    <label class="control-label"><?php echo $RoomDB[0]['Seat'] ?></label> 
                </div>
            </div>
        </div>
        <div class="form-group"> 
            <div class="row">
                <div class="col-sm-3">
                    <label class="control-label">Request Layout:</label>
                </div>    
                <div class="col-sm-6">
                    <?php echo $Req_layout ?>
                </div>
            </div>
        </div>
        <?php if ( is_array($KetAdditional) && count($KetAdditional) > 0): ?>
            <div class="form-group"> 
                <div class="row">
                    <div class="col-sm-3">
                        <label class="control-label">Desc Additional:</label>
                    </div>    
                    <div class="col-sm-8">
                        <div class="form-group">
                            <?php foreach ($KetAdditional as $key => $value): ?>
                                <div class="row">
                                    <div class="col-xs-12">
                                        <label class="control-label"><?php echo str_replace("_", " ", $key) ?></label>
                                        <input type="text" class="form-control" readonly="" value="<?php echo $value ?>">
                                    </div>
                                </div>
                            <?php endforeach ?>
                        </div>    
                    </div>
                </div>
            </div>
        <?php endif ?>
        
        <?php if ($Email_invitation != ''): ?>
            <div class="form-group"> 
                <div class="row">
                    <div class="col-sm-3">
                        <label class="control-label">Attachment:</label>
                    </div>    
                    <div class="col-sm-6">
                        <?php echo $Email_invitation ?>
                    </div>
                </div>
            </div>
        <?php endif ?>
        <div style="text-align: center;">       
    		<div class="col-sm-12" id="BtnFooter">
                <button type="button" id="ModalbtnCancleForm" data-dismiss="modal" class="btn btn-default">Close</button>
                <?php if ($this->session->userdata('ID_group_user') < 3): ?>
                    <!-- <button type="button" class="btn btn-success btn-edit btn-apppove" id_table = "<?php echo $ID ?>" ApproveAccess = <?php echo $ApproveAccess ?> >Approve</button> -->
                <?php else: ?>
                    <?php switch($ApproveAccess): 
                    case 0: ?>
                    <?php case 1: ?>
                    <?php break; ?>
                    <?php case 2: ?>
                        <!-- <button type="button" class="btn btn-success btn-edit btn-apppove" id_table = "<?php echo $ID ?>" ApproveAccess = <?php echo $ApproveAccess ?> >Approve</button> -->
                    <?php case 3: ?>
                        
                    <?php break; ?>
                    <?php case 4: ?>
                        <!-- <button type="button" class="btn btn-success btn-edit btn-apppove" id_table = "<?php echo $ID ?>" ApproveAccess = <?php echo $ApproveAccess ?> >Approve</button> -->
                    <?php break; ?>
                    <?php endswitch; ?>    
                <?php endif ?>
    		</div>
        </div>    
    </form>
<script type="text/javascript">
    //window.equipment_additional = [];
    $(document).ready(function () {
        load_e_additional();
        LoadEnd();
        load_person_support();
        load_multiple();
        loadEquipmentRoom();
    });

    function loadEquipmentRoom()
    {
        var room = "<?php echo $room ?>";
        var html_table =''+
                         '<div class="table-responsive">'+
                            '<table class="table table-striped table-bordered table-hover table-checkable datatable3">'+
                                '<thead>'+
                                    '<tr>'+
                                   ' <th>Nama</th>'+
                                   ' <th>Qty</th>'+
                                   ' <th>Note</th>'+
                                    '</tr>'+
                               ' </thead>'+
                                '<tbody>'+
                                '</tbody>'+
                            '</table>'+
                         '</div>'+   
                        '';
        var url = base_url_js+'api/__room_equipment';
        var data = {
            room : room
        };
        var token = jwt_encode(data,"UAP)(*");
        $.post(url,{ token:token },function (data_json) {
            setTimeout(function () {
                // var response = jQuery.parseJSON(data_json);
               $("#page_equipment_room").html(html_table);
               for (var i = 0; i < data_json.length; i++) {
                $(".datatable3 tbody").append(
                    '<tr>'+
                        '<td>'+data_json[i]['Equipment']+'</td>'+
                        '<td>'+data_json[i]['qty']+'</td>'+
                        '<td>'+data_json[i]['Note']+'</td>'+
                    '</tr>' 
                    );
            }
            LoaddataTableStandard('.datatable3');
            },500);
        });                
    }

    function load_multiple()
      {
        $('#multiplePage').append('<table class="table" id ="table_multiple">');
        for (var i = 0; i < 1; i++) {
            $('#table_multiple').append('<tr id = "multiple'+i+'">');
            for (var k = 0; k < 2; k++) {
                if (k == 0) {
                    $('#multiple'+i).append('<td>'+
                                        '<input type="checkbox" class = "chk_e_multiple" name="chk_multiple" value = "Tidak" id = "e_multipleTDK">&nbsp No' +
                                     '</td>'
                                    );
                }
                else
                {
                    $('#multiple'+i).append('<td>'+
                                        '<input type="checkbox" class = "chk_e_multiple" name="chk_multiple" value = "Ya" id = "multipleYA">&nbsp Yes' +
                                     '</td>'
                                    );
                }
                
            }
            $('#multiple'+i).append('</tr>');
        }
        $('#table_multiple').append('</table>');
      }

    function load_e_additional()
      {
        $('#e_additional').append('<table class="table" id ="table_e_additional">');
        for (var i = 0; i < 1; i++) {
            $('#table_e_additional').append('<tr id = "e_additional'+i+'">');
            for (var k = 0; k < 2; k++) {
                if (k == 0) {
                    $('#e_additional'+i).append('<td>'+
                                        '<input type="checkbox" class = "chk_e_additional" name="chk_e_additional" value = "Tidak" id = "e_additionalTDK">&nbsp No' +
                                     '</td>'
                                    );
                }
                else
                {
                    $('#e_additional'+i).append('<td>'+
                                        '<input type="checkbox" class = "chk_e_additional" name="chk_e_additional" value = "Ya" id = "e_additionalYA">&nbsp Yes' +
                                     '</td>'
                                    );
                }
                
            }
            $('#e_additional'+i).append('</tr>');
        }
        $('#table_e_additional').append('</table>');
      }

      function load_person_support()
      {
        $('#person_support').append('<table class="table" id ="table_person_support">');
        for (var i = 0; i < 1; i++) {
            $('#table_person_support').append('<tr id = "tr_person_support'+i+'">');
            for (var k = 0; k < 2; k++) {
                if (k == 0) {
                    $('#tr_person_support'+i).append('<td>'+
                                        '<input type="checkbox" class = "chk_person_support" name="chk_person_support" value = "Tidak" id = "person_supportTDK">&nbsp No' +
                                     '</td>'
                                    );
                }
                else
                {
                    $('#tr_person_support'+i).append('<td>'+
                                        '<input type="checkbox" class = "chk_person_support" name="chk_person_support" value = "Ya" id = "person_supportYA">&nbsp Yes' +
                                     '</td>'
                                    );
                }
                
            }
            $('#tr_person_support'+i).append('</tr>');
        }
        $('#table_person_support').append('</table>');
      }



      function LoadEnd()
      {
        var url = base_url_js+"api/get_time_opt_reservation";
        var time = "<?php echo $time ?>";
        var data = {
            time : time
        }
        var token = jwt_encode(data,"UAP)(*");
        $.post(url,{ token:token },function (data_json) {
          for (var i = 0; i < data_json.length; i++) {
              var selected = (i==0) ? 'selected' : '';
              $('#End').append('<option value="'+ data_json[i]  +'" '+selected+'>'+data_json[i]+'</option>');
          }
        }).done(function () {
          //loadAlamatSekolah();
        });
        

      }
</script>