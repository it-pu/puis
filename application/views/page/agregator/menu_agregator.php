<style>
/*.btn-circle {*/
    /*width: 30px;*/
    /*height: 30px;*/
    /*padding: 6px 0px;*/
    /*border-radius: 15px;*/
    /*text-align: center;*/
    /*font-size: 12px;*/
    /*line-height: 1.42857;*/
/*}*/
 
/*.btn-round{*/
    /*border-radius: 8px;*/
/*}*/
</style> 


<style>
    .list-group {
        margin-bottom: 0px;
    }
    .list-group-item {
        /*border: none;*/
        border-left: 0px;
        border-right: 0px;
        border-bottom: 0px solid #ccc !important;
    }

    #menuLeft .panel-default>.panel-heading {
        background-color: #e8e8e8;
    }

    .active-left-menu {
        background: #c8e0ff;
    }

    .alert-note-agregator {
        border-color: #5db2c3 !important;
    }

    h3.header-blue {
        margin-top: 0px;
        border-left: 7px solid #2196F3;
        padding-left: 10px;
        font-weight: bold;
    }
</style>


<div class="row" id = "ParentContent">
    <div class="col-md-2" id="menuLeft">
        <div>
            <a href="<?= base_url('agregator/setting'); ?>" class="btn btn-primary btn-block btn-round"><i class="fa fa-cog"></i> Setting</a>
            <hr/>
        </div>

        <div class="panel-group" id="accordion">

            <?php foreach ($listMenu AS $item){ ?>

            <div class="panel panel-default">
                <div class="panel-heading">
                    <a data-toggle="collapse" data-parent="#accordion" href="#collapse_<?= $item['ID']; ?>">
                        <h4 class="panel-title">
                            <?= $item['Name']; ?>
                        </h4>
                    </a>
                </div>
                <div id="collapse_<?= $item['ID']; ?>" class="panel-collapse collapse">
                    <div class="list-group">
                        <?php foreach ($item['Menu'] AS $itm2){ ?>
                            <a href="<?= base_url($itm2['URL']); ?>" class="list-group-item"><?= $itm2['Name']; ?></a>
                        <?php } ?>
                    </div>
                </div>
            </div>

            <?php } ?>

        </div>
    </div>
    <div class="col-md-10" id = "pageContent">
        <?= $page; ?>

        <div id = "pageInputDescription" style="margin-top: 5px;margin-bottom: 15px;">
            
        </div>

        <?php if(count($Description)>0){

            if($Description[0]['Description']!='' && $Description[0]['Description']!=null){ ?>
                <div class="alert alert-info alert-note-agregator" role="alert"><h3 style="margin-top: 0px;font-weight: bold;">Catatan : </h3><div><?= $Description[0]['Description']; ?></div></div>
            <?php }

        } ?>

    </div>
</div>





<script>
    const ID_agregator_menu = <?php echo $Description[0]['ID'] ?>;

    class Class_DescriptionInput {
        
        constructor(){
            
        }

        domInput = (response) => {
            let valuedata = '';
            let UpdatedAt = '-';
            let UpdatedBy = '-';
            if (typeof response['data'] != "undefined" && response['data'] != "" && response['data'] != null) {
               valuedata = response['data'];
               UpdatedAt = moment(response['UpdatedAt']).format('dddd, DD MMM YYYY HH:mm');
               UpdatedBy = response['UpdatedBy'];
            }
            let html = '<div class = "well"><div style="padding-left:15px;padding-right:15px;"><h3 class="header-blue">Input Description</h2></div>'+
                            '<div class = "row" style = "margin-left:15px;margin-right:15px;>'+
                                '<div class = "col-md-12">'+
                                    '<textarea class = "inputDescription form-control" placeholder = "Input Description" rows = "4"></textarea>'+
                                    '<br/>'+
                                    'Updated : <span style = "color:green;">  '+UpdatedBy+ '</span> at <span style = "color : blue;">'+UpdatedAt+'</span>'+
                                '</div>'+
                                '<div class = "col-md-12" style = "margin-top:10px;">'+
                                    '<div style = "text-align:right;">'+
                                        '<button class = "btn btn-success btnSaveDescription" style = "width:100%;">Save</button>'
                                    '</div>'+
                                '</div>'+
                        '</div></div>';
            $('#pageInputDescription').html(html);
            $('.inputDescription').html(valuedata);
        }

        getDescription = async(Years = null,SemesterID = null) => {
            const url = base_url_js + 'agregator/APT_getDescription';
            const data = {
                ID_agregator_menu : ID_agregator_menu,
            }

            if (Years != null && Years !== undefined) {
                data['Year'] = Years;
            }

            if (SemesterID != null && SemesterID !== undefined) {
                data['SemesterID'] = SemesterID;
            }

            var token = jwt_encode(data,"UAP)(*");

           try{
               const response = await AjaxSubmitFormPromises(url,token);
               this.domInput(response);
           }
           catch(err){
                toastr.info('something wrong get description');
           }
        }

        saveDescription = async(selector,Years = null,SemesterID = null) => {
            const htmlBtn = selector.html();
            const url = base_url_js + 'agregator/APT_saveDescription';
            const data = {
                ID_agregator_menu : ID_agregator_menu,
                Description : $('.inputDescription').val(),
            }

            if (Years != null && Years !== undefined) {
                data['Year'] = Years;
            }

            if (SemesterID != null && SemesterID !== undefined) {
                data['SemesterID'] = SemesterID;
            }

            var token = jwt_encode(data,"UAP)(*");

            if ($('.inputDescription').val() != '' && $('.inputDescription').val() !== undefined && confirm('Are you sure ?') ) {
                loading_button2(selector);
                try{
                     const response = await AjaxSubmitFormPromises(url,token);
                     if (response.status == 1) {
                        toastr.success('Saved');
                     }
                     else
                     {
                        toastr.info(response.msg);
                     }
                }
                catch(err){
                    toastr.error('something wrong');
                }

                end_loading_button2(selector);
            }

        }


    }

    var newDescritionInput =  new Class_DescriptionInput();

    var WaitForLoading = 0;
    var MyVarEbomb;
    var waitForEl_AGG = function(selector, callback) {
      if ($(selector).length) {
        callback();
      } else {
       MyVarEbomb = setTimeout(function() {
          waitForEl_AGG(selector, callback);
        }, 100);
      }
    };
    var App_menu_aggregator = {
        Loadauth : function(){
            var NIP = sessionNIP;
            var getCurrentURL = document.URL;
            var IndexURL = "<?php echo url_pas ?>";
            if (IndexURL+'agregator-aps/setting' != getCurrentURL && IndexURL+'agregator/setting' != getCurrentURL) {
                var dataform = {
                                    NIP : NIP,
                                    getCurrentURL : getCurrentURL,
                                    Type : 'APT',
                                };
                var token = jwt_encode(dataform,"UAP)(*");
                var url = base_url_js + "agregator/authenticate";
                $.post(url,{ token:token },function (resultJson) {
                           
                }).done(function(resultJson) {
                    App_menu_aggregator.PrevilgesUser(resultJson);
                }).fail(function() {
                    toastr.error("Connection Error, Please try again", 'Error!!');
                }).always(function() {

                });
            }
        },
        PrevilgesUser : function(resultJson){
            var AccessPage = resultJson.AccessPage;
            var RuleAccess = resultJson.RuleAccess;
            if (AccessPage == 'No') {
                $('#pageContent').remove();
                $('#ParentContent').append(
                        ' <div class="col-md-10" id = "pageContentAuth">'+
                            '<div class="well">'+
                                '<div class="row">'+
                                    '<div class="col-md-12" style="text-align: center;padding-bottom: 20px;">'+
                                        '<h3>You don\'t have access to this menu</h3>'+
                                    '</div>'+
                               ' </div>'+
                            '</div>'+
                        '</div>'    
                    );
                loadSelectOptionBaseProdi('#filterProdi','');
            }
            else{
                RuleAccess =  jQuery.parseJSON(RuleAccess);
                App_menu_aggregator.LoadProdiAuth(RuleAccess);
            }
        },
        LoadProdiAuth : function(RuleAccess){
            if (RuleAccess.input == 'false') {
                $('#inputForm').attr('class','hide');
                waitForEl_AGG(".inputBtn", function() {
                  $('.inputBtn').addClass('hide');
                });
                setTimeout(function () {
                    clearTimeout(MyVarEbomb);
                },10000);
                $(document).ajaxComplete(function () {
                    $(".inputBtn").addClass('hide');
                });
                $('#ViewData').attr('class','col-md-12');
            }
            else
            {
                $('#inputForm').attr('class','col-md-4');
                // waitForEl_AGG(".inputBtn", function() {
                //   $('.inputBtn').removeClass('hide');
                // });
                $('#ViewData').attr('class','col-md-8');
            }
            // console.log(RuleAccess);
            WaitForLoading = 1;
        },
        loaded : async function(){
            loadingStart();
            App_menu_aggregator.Loadauth();
            loadingEnd(1500);
        }

    };

    const selectFilterOpYear = (selector) => {

        var Year = <?php echo date('Y')  ?>;
        var StartYear = Year - 5;
        selector.empty();
        selector.append('<option value="0" selected>All Year</option>');
        selector.append('<option value="-3">Last 3 Year</option>');
        selector.append('<option value="-5">Last 5 Year</option>');

        var YearPlus = <?php echo date('Y')  ?> + 4;
        for (let i = StartYear; i <= YearPlus; i++) {
            selector.append('<option value="'+i+'">'+i+'</option>');
        }

        // for (let i = StartYear; i <= Year; i++) {
        //     selector.append('<option value="'+i+'">'+i+'</option>');
        // }

    }

    const selectFilterOpYear2 = (selector) => {
        selector.empty();
        selector.append('<option value="0" selected>All Year</option>');
        selector.append('<option value="-3">Last 3 Year</option>');
        selector.append('<option value="-5">Last 5 Year</option>');

    }


    $(document).ready(function () {

        $('.fixed-header').addClass('sidebar-closed');

        $('.list-group-item').removeClass('active-left-menu');
        $('.collapse').removeClass('in');

        var elm = $('a[href="'+base_url_js+'<?= $this->uri->segment(1).'/'.$this->uri->segment(2); ?>"]');
        elm.addClass('active-left-menu');
        elm.parent().parent().addClass('in');

        App_menu_aggregator.loaded();
    });

    $(document).on('click','.btnRemove',function () {

        if(confirm('Hapus data?')){

            var ID = $(this).attr('data-id');
            var table = $(this).attr('data-tb');
            var file = $(this).attr('data-file');

            $('.btnAction').prop('disabled',true);

            var url = base_url_js+'api3/__crudAgregatorTB1';

            var data = {
                action: 'removeDataAgg',
                ID : ID,
                Table : table,
                File : file
            };

            var token = jwt_encode(data,'UAP)(*');

            $.post(url,{token:token},function (result) {
                loadDataTable();
                toastr.success('Data removed','Success');
                setTimeout(function () {
                    // loadDataTable();
                },500);

            });


        }


    });

</script>