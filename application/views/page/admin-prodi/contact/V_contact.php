
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
                        <th>Action</th>
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
         alert('ok');
       loadDataContact();
    });

    function loadDataContact(){

        var data = {
            action : 'readDataContact',
        };

        var token = jwt_encode(data,'UAP)(*');
        var url = base_url_js+'api-prodi/__crudDataProdi';
        $.post(url,{token:token},function (jsonResult) {

            if(jsonResult.length>0){
                $.each(jsonResult,function (i,v) {
                    $('#dataContact').append('<tr>'+
                        '<td>'+v.Name+'</td>'+
                        '<td>'+v.Email+'</td>'+
                        '<td>'+v.Subject+'</td>'+
                        '<td>'+v.Messages+'</td>'+
                        '<td>'+v.CreateAT+'</td>'+
                        '</tr>');
                });
            }

        });
    }


</script>