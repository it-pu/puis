<div class="tabbable tabbable-custom tabbable-full-width">
    <ul class="nav nav-tabs">
       
        <li class="<?php if($this->uri->segment(2)=='contact' && ($this->uri->segment(3)=='' || $this->uri->segment(3)==null ) ) { echo 'active'; }?>">
            <a href="<?php echo base_url('webdivisi/contact'); ?>">Address</a>
        </li>
        <li class="<?php if($this->uri->segment(3)=='sosmed' ) { echo 'active'; } ?>">
            <a href="<?php echo base_url('webdivisi/contact/sosmed'); ?>">Sosial Media</a>
        </li>
        
    </ul>
    <div style="border-top: 1px solid #cccccc"> 

        <div class="row">
            <div class="col-md-12">
                <?= $pagecontact; ?>
            </div>
        </div>

    </div>
</div>

<div class="row">
<div class="col-md-12">
    <div class="widget box">
        <div class="widget-header">
            <h4><i class="icon-reorder"></i> Data Contact </h4>
            <div class="toolbar no-padding">
                <div class="btn-group">
                    <span class="btn btn-xs widget-collapse"><i class="icon-angle-down"></i></span>
                </div>
            </div>
        </div>
        <div class="widget-content no-padding">
            <table class="table table-striped table-bordered table-hover table-checkable table-responsive">
                <thead>
                    <tr>
                        <!-- <th class="checkbox-column">
                            <input type="checkbox" class="uniform">
                        </th> -->
                        <th data-class="expand">Name</th>
                        <th>Email</th>
                        <th>Subject</th>
                        <th>Messages</th>
                        <th>Update Date</th>
                        <!-- <th>Action</th> -->
                    </tr>
                </thead>
                <tbody id="dataContact">
                    
                </tbody>
            </table>
        </div>
    </div>
</div>
</div>

<script type="text/javascript">

    $(document).ready(function(){
       loadDataContact();
    });

    function loadDataContact(){

        var data = {
            action : 'readDataContact',
        };

        var token = jwt_encode(data,'UAP)(*');
        var url = base_url_js+'api-webdivisi/__crudDataDivisi';
        $.post(url,{token:token},function (jsonResult) {

            if(jsonResult.length>0){
                $.each(jsonResult,function (i,v) {
                    $('#dataContact').append('<tr>'+
                        '<td>'+v.Name+'</td>'+
                        '<td>'+v.Email+'</td>'+
                        '<td>'+v.Subject+'</td>'+
                        '<td>'+v.Message+'</td>'+
                        '<td>'+v.CreateAT+'</td>'+
                        '</tr>');
                });
            }

        });
    }


</script>