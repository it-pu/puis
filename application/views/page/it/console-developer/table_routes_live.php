<div class="panel panel-default">
    <div class="panel-heading">
        <h4 class="panel-title">Data Live</h4>
    </div>
    <div class="panel-body" style="min-height: 100px;">
        <table class="table" id = "Tbl_routes_live">
            <thead>
                <tr>
                    <td style="width: 8%">No</td>
                    <td>Slug</td>
                    <td>Controller</td>
                    <td>Type</td>
                    <td>Department</td>
                    <td>Updated by</td>
                    <td>Updated at</td>
                    <td>Action</td>
                </tr>
            </thead>
        </table>
    </div>
    <div class="panel-footer" style="text-align: right;">
        <?php if ($_SERVER['SERVER_NAME'] != 'pcam.podomorouniversity.ac.id'): ?>
            <button class="btn btn-danger" id="btnMigrateLive">Migrate to Server Local</button>
        <?php endif ?>
    </div>
    <p style="color: red;">* Mohon Migrate data ke server local jika data local telah di migrate ke server live, dengan klik tombol Migrate to Server Local</p>
    <p style="color: red;">* Jika data local masih ada dan tombol Migrate to Server Local telah diklik maka data routes local akan digantikan oleh data server live</p>

</div>
<script type="text/javascript">
    var App_table_routes_live = {
        LoadData : function(){
             var recordTable = $('#Tbl_routes_live').DataTable({ 
                 "processing": true,
                 "serverSide": false,
                 "pageLength": 10,
                 "ajax":{
                     url : base_url_js+"it/console-developer/routes/submit", // json datasource
                     ordering : false,
                     type: "post",  // method  , by default get
                     // data : {token : token} 
                     data: function(token){
                               // Read values
                                var data = {
                                       action : 'read',
                                       server : 'live',
                                   };
                               // Append to data
                               token.token = jwt_encode(data,'UAP)(*');
                     }                                   
                 },
                   'columnDefs': [
                      {
                         'targets': 0,
                         'searchable': false,
                         'orderable': false,
                         'className': 'dt-body-center',
                      },
                          {
                         'targets': 7,
                         'searchable': false,
                         'orderable': false,
                         'className': 'dt-body-center',
                         'render': function (data, type, full, meta){
                             var btnAction = '<div class="btn-group">' +
                                 '  <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">' +
                                 '    <i class="fa fa-pencil"></i> <span class="caret"></span>' +
                                 '  </button>' +
                                 '  <ul class="dropdown-menu" style="min-width:50px !important;">' +
                                 '    <li><a href="javascript:void(0);" class="btnEdit" data-id="'+full[9]+'" data = "'+full[10]+'" server = "live"><i class="fa fa fa-edit"></i></a></li>' +
                                 '    <li role="separator" class="divider"></li>' +
                                 '    <li><a href="javascript:void(0);" class="btnRemove" data-id="'+full[9]+'" server = "live"><i class="fa fa fa-trash"></i></a></li>' +
                                 '  </ul>' +
                                 '</div>';
                             return btnAction;
                         }
                      },
                   ],
                 'createdRow': function( row, data, dataIndex ) {
                     $(row).attr('data-id',data[9]);    
                 },
                 "order": [[ 6, "desc" ]],
                 dom: 'l<"toolbar">frtip',
                 initComplete: function(){
                   
                }  
             });
             recordTable.on( 'order.dt search.dt', function () {
                            recordTable.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
                                cell.innerHTML = i+1;
                            } );
                        } ).draw();
             oTable2 = recordTable;
        }
    };

    $(document).ready(function(){
        App_table_routes_live.LoadData();
    });

    $(document).off('click', '.btnEdit').on('click', '.btnEdit',function(e) {
        var ID = $(this).attr('data-id');
        var server = $(this).attr('server');
        var Token = $(this).attr('data');
        var data = jwt_decode(Token);
        for(var key in data) {
            if (key == 'Type') {
                $(".input[name='Type'] option").filter(function() {
                   //may want to use $.trim in here
                   return $(this).val() == data.Type; 
                 }).prop("selected", true);
            }
            else if(key == 'Department'){
                $(".input[name='Department'] option").filter(function() {
                   //may want to use $.trim in here
                   return $(this).val() == data.Department; 
                }).prop("selected", true);
                $(".input[name='Department']").select2({

                });
            }
            else
            {
                $('.input[name="'+key+'"]').val(data[key]);
            }
        }
        
        $('#btnSave').attr('action','edit');
        $('#btnSave').attr('data-id',ID);
        $('#btnSave').attr('server',server);
    })


    $(document).off('click', '#btnMigrateLive').on('click', '#btnMigrateLive',function(e) {
        var S_checkbox = $('.select_migrate2');
        var selector = $(this);
        if (S_checkbox.length) {
            var arr = [];
            oTable2.$('input[type="checkbox"]').each(function(){
              var tr = $(this).closest('tr');
              if(this.checked){
                 var ID = tr.attr('data-id');
                 var dt = [];
                 // total col = 5
                 for (var i = 1; i <= 6; i++) {
                     var dt_html = tr.find('td:eq('+i+')').html();
                     dt.push(dt_html);
                 }
                 var temp = {
                    ID : ID,
                    datahtml : dt,
                 }
                 arr.push(temp);
              }
               
            });

            if (arr.length > 0) {
                var html = '';
                    html += '<div class = "row">'+
                                '<div class = "col-md-12">'+
                                    '<div class="table-responsive">'+
                                    '<table class="table table-striped table-bordered table-hover table-checkable tableData" id = "TblModal2">'+
                                          '<thead>'+
                                              '<tr>'+
                                                  '<th style = "width:5%;">No</th>'+
                                                  '<th>Slug</th>'+
                                                  '<th>Controller</th>'+
                                                  '<th>Type</th>'+
                                                  '<th>Department</th>'+
                                                  '<th>Updated by</th>'+
                                                  '<th>Updated at</th>'+
                                               '</tr>'+
                                            '</thead>';
                    html += '<tbody>';
                    for (var i = 0; i < arr.length; i++) {
                        html += '<tr data-id = "'+arr[i].ID+'">';
                        var datahtml = arr[i].datahtml;
                        html += '<td>'+(i+1)+'</td>';
                        for (var j = 0; j < datahtml.length; j++) {
                            html += '<td>'+datahtml[j]+'</td>';
                        }
                        html += '</tr>';
                    }

                    html += '</tbody></table>';
                    html += '</div></div></div>'; 

                     var footer = '<button type="button" id="ModalbtnCancleForm" data-dismiss="modal" class="btn btn-default">Cancel</button>'+
                         '<button type="button" id="ModalbtnSaveForm2" class="btn btn-success">Procces</button>';

                    $('#GlobalModalLarge .modal-header').html('<h4 class="modal-title">'+'List Checklist Data Live'+'</h4>');
                    $('#GlobalModalLarge .modal-body').html(html);
                    $('#GlobalModalLarge .modal-footer').html(footer);
                    $('#GlobalModalLarge').modal({
                        'show' : true,
                        'backdrop' : 'static'
                    });
            }
            
        }
        else
        {
            oTable2.rows().every(function(index, element) {
              var row = $(this.node());
              var No = row.find('td').eq(0).html();
              row.find('td').eq(0).html('<input type = "checkbox" class = "select_migrate2" checked> '+No );
            });
        }
    })

    $(document).off('click', '#ModalbtnSaveForm2').on('click', '#ModalbtnSaveForm2',function(e) {
        var arr = [];
        var selector = $(this);
        $('#TblModal2 tbody tr').each(function(){
             var ID = $(this).attr('data-id');
             arr.push(ID);
        })
       
        var dataform = {
            data : arr,
            action : 'MigrateLive',
        };
        var token = jwt_encode(dataform,"UAP)(*");
        loading_button2(selector);
        var url = base_url_js + "it/console-developer/routes/submit";
        $.post(url,{ token:token },function (resultJson) {
                
        }).done(function(resultJson) {
            $('#GlobalModalLarge').modal('hide');
            end_loading_button2(selector);
            oTable.ajax.reload( null, false );
            oTable2.ajax.reload( null, false );
            toastr.success('Success');
        }).fail(function() {
            toastr.error("Connection Error, Please try again", 'Error!!');
            end_loading_button2(selector); 
        }).always(function() {
             end_loading_button2(selector);              
        });
    });

    $(document).off('click', '.btnRemove').on('click', '.btnRemove',function(e) {
        var ID = $(this).attr('data-id');
        var action = 'delete';
        var server = $(this).attr('server');
        App_input_routes.SubmitData(action,ID,$(''),server);
    })
</script>