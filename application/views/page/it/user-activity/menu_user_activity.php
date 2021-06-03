
<div class="" style="margin-top: 30px;">
    <div class="col-md-12">
        <p style="color: red;">Jika data tidak muncul di table, mohon untuk disable plugin adblock dari browsernya</p>
    </div>
    <div class="col-md-12">
        <div class="pull-right">
            <button class="btn btn-default" id="btnClearLog"><i class="fa fa-eraser margin-right"></i> Clear Log</button>
        </div>

        <div class="tabbable tabbable-custom tabbable-full-width">
            <?php $activeMenu = $this->uri->segment(1); ?>
            <ul class="nav nav-tabs">
                <li class="<?php echo ($this->uri->segment(2) == 'user-activity' &&  ($this->uri->segment(3) == '' || $this->uri->segment(3) == null)  ) ? 'active' : '' ?>">
                    <a href="<?php echo base_url('it/user-activity'); ?>">P-Camp</a>
                </li>
                <li class="<?php echo ($this->uri->segment(2) == 'user-activity-lecturer'  ) ? 'active' : '' ?>">
                    <a href="<?php echo base_url('it/user-activity-lecturer'); ?>">Lecturer</a>
                </li>
                <li class="<?php echo ($this->uri->segment(2) == 'user-activity-student'  ) ? 'active' : '' ?>">
                    <a href="<?php echo base_url('it/user-activity-student'); ?>">Student</a>
                </li>
                <li class="hide <?php echo ($this->uri->segment(2) == 'user-activity-log-login'  ) ? 'active' : '' ?>">
                    <a href="<?php echo base_url('it/user-activity-log-login'); ?>">Log Login</a>
                </li>
            </ul>
            <div style="padding-top: 30px;border-top: 1px solid #cccccc">
                <?php echo $page; ?>
            </div>
        </div>
    </div>
</div>




<script>
    $('#btnClearLog').click(function () {

        $('#GlobalModalSmall .modal-header').html('<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
            '<h4 class="modal-title">Clear Log P-Camp</h4>');

        var htmlss = '<div class="form-group">' +
            '<label>Log</label>' +
            '<select id="formTable" class="form-control">' +
            '    <option value="db_employees.log_employees">P-Camp</option>' +
            '    <option value="db_employees.log_lecturers">Lecturer</option>' +
            '    <option value="db_academic.log_student">Student</option>' +
            '</select>' +
            '</div> ' +
            '<div class="form-group">' +
            '<label>Start</label>' +
            '<input class="form-control" id="formStart" type="date">' +
            '</div>' +
            '<div class="form-group">' +
            '<label>End</label>' +
            '<input class="form-control" id="formEnd" type="date">' +
            '</div>';

        $('#GlobalModalSmall .modal-body').html(htmlss);

        $('#GlobalModalSmall .modal-footer').html('<button type="button" class="btn btn-default" data-dismiss="modal">Close</button> ' +
            '<button id="btnSubmitClear" class="btn btn-success">Submit</button>');

        $('#GlobalModalSmall').modal({
            'show' : true,
            'backdrop' : 'static'
        });

    });

    $(document).on('click','#btnSubmitClear',function () {

        var formStart = $('#formStart').val();
        var formEnd = $('#formEnd').val();
        var formTable = $('#formTable').val();

        if(new Date(formEnd)> new Date(formStart) && formTable!=''){

            if(confirm('Are you sure?')){
                loading_buttonSm('#btnSubmitClear');

                var data = {
                    action : 'removeLogging',
                    table : formTable,
                    Start : formStart,
                    End : formEnd
                };

                var token = jwt_encode(data,'UAP)(*');
                var url = base_url_js+'api3/__crudLogging';

                $.post(url,{token:token},function (jsonResult) {

                    toastr.success('Log removed','Susscess');
                    window.dataTable.ajax.reload(null, false);
                    setTimeout(function () {
                        $('#GlobalModalSmall').modal('hide');
                    },500);

                });
            }



        } else {
            alert('Tanggal End harus lebih besar dari Start');
        }


    });
</script>


