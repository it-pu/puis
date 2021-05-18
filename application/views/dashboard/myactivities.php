<style>
    #tableDataLog td:nth-child(1) {
        border-right: 1px solid #CCCCCC;
        text-align: center;
    }
    ul.nav > li > a{cursor: pointer;}
</style>
<div class="row">
    <div class="col-md-12">
        <p style="color: red;">Jika data tidak muncul di table, mohon untuk disable plugin adblock dari browsernya</p>
    </div>
</div>

<div id="activities">
    <div class="nav-tabs">
        <div class="tabbable tabbable-custom tabbable-full-width" style="margin-bottom:0px">
            <ul class="nav nav-tabs">
                <li class="nav-my-act active">
                    <a href="<?=base_url('my-activities')?>">My Activities</a>
                </li>
                <li class="nav-my-team">
                    <a>Activities Team</a>
                </li>            
            </ul>
        </div>
    </div>

    <div class="tabs-content" style="border:1px solid #ddd;border-top:0px;padding: 30px 10px 10px 10px">
        <div class="row">
            <div class="col-md-12">
                <div id="loadTable"></div>
            </div>
        </div>
    </div>

</div>




<script>

    $(document).ready(function () {
        getDataLog();

        /*ADDED BY FEBRI @ MARCH 2020*/
        $(".nav-my-team").click(function(){
            $("#activities ul.nav > li").removeClass("active");
            $(this).addClass("active");
            $("#activities .tabs-content").empty();
            $.ajax({
                type : 'POST',
                url : base_url_js+"my-team",
                dataType : 'html',
                beforeSend :function(){
                    loading_modal_show();
                },error : function(jqXHR){
                    loading_modal_hide();
                    $("body #GlobalModal .modal-header").html("<h1>Error notification</h1>");
                    $("body #GlobalModal .modal-body").html(jqXHR.responseText);
                    $("body #GlobalModal").modal("show");
                },success : function(response){
                    loading_modal_hide();
                    $("#activities .tabs-content").html(response);
                }
            });
        });
        /*END ADDED BY FEBRI @ MARCH 2020*/
    });
	
	function reconfirmAttd() {
        if(confirm('Are you sure?')) {
            loading_modal_show();

            var dataURL = window.location.href;

            var url = base_url_js+'api3/__crudLogging';

            var data = {
                action : 'insertLog',
                dataForm : {
                    NIP : sessionNIP,
                    UserID : sessionNIP,
                    IPPublic : '',
                    URL : dataURL
                }
            };

            var token = jwt_encode(data,'UAP)(*');

            $.post(url,{token:token},function (result) {
                getDataLog();
                toastr.success('Data saved', 'Success');


                setTimeout(function () {
                    loading_modal_hide();
                }, 1000);
            });



        }
    }

    function getDataLog() {
        $('#loadTable').html('<div style="text-align:right;margin-bottom:20px;"><button onclick="reconfirmAttd()" class="btn btn-lg btn-success">Reconfirm attendance</button></div><table class="table table-striped" id="tableDataLog">' +
            '            <thead>' +
            '            <tr>' +
            '                <th style="width: 1%;">No</th>' +
            '                <th style="width: 30%;">Accessed</th>' +
            '                <th>Path</th>' +
            '            </tr>' +
            '            </thead>' +
            '        </table>');

        // var url = base_url_js+'api3/__getDataLogEmployees?u=2017090';
        var url = base_url_js+'api3/__getDataLogEmployees?u='+sessionNIP;

        var dataTable = $('#tableDataLog').DataTable( {
            "processing": true,
            "serverSide": true,
            "iDisplayLength" : 25,
            "ordering" : false,
            "language": {
                "searchPlaceholder": "Search..."
            },
            "ajax":{
                url :url, // json datasource
                ordering : false,
                type: "post",  // method  , by default get
                error: function(){  // error handling
                    loading_modal_hide();
                    $(".employee-grid-error").html("");
                    $("#employee-grid").append('<tbody class="employee-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
                    $("#employee-grid_processing").css("display","none");
                }
            }
        } );

    }


</script>