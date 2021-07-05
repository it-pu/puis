<div class="row">
    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading clearfix">
                <h4 class="panel-title pull-left" style="padding-top: 7.5px;">Input Penjualan Formulir</h4>
            </div>
            <div class="panel-body">
                <div class = "row" style="margin-left: 0px;margin-right: 0px;margin-top: 10px">
                    <div class="col-md-12">

                        <div class="panel panel-primary">
                            <div class="panel-heading clearfix">
                                <h4 class="panel-title pull-left" style="padding-top: 7.5px;">Data Formulir</h4>
                            </div>
                            <div class="panel-body">
                                <div class="form-group">
                                    <div class="row" style="margin-left: 0px;margin-right: 0px;margin-top: 10px">
                                        <div class="col-md-2">
                                            <label>Formulir Code</label>
                                            <select class="select2-select-00 col-md-4 full-width-fix" id="selectFormulirCode">
                                                <option></option>
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label>Program Study 1</label>
                                            <select class="select2-select-00 col-md-4 full-width-fix" id="selectProgramStudy">
                                                <option></option>
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label>Program Study 2</label>
                                            <select class="select2-select-00 col-md-4 full-width-fix" id="selectProgramStudy2">
                                                <option></option>
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label>Tanggal</label>
                                            <input type="text" name="tanggal" id= "tanggal" data-date-format="yyyy-mm-dd" placeholder="Date..." class="form-control">
                                        </div>
                                        <div class="col-md-2">
                                            <label>No Ref</label>
                                            <select class="select2-select-00 col-md-4 full-width-fix" id="No_Ref">
                                                <option></option>
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label>From CRM</label>
                                            <!--           			  					<input type="checkbox" class="FromCrm" name="FromCrm" value="0" checked>No-->
                                            <!--           			  					<input type="checkbox" class="FromCrm" name="FromCrm" value="1"> Yes-->
                                            <div class="input-group" id = "SearchFromCrm">
           			  					    <span class="input-group-addon" id = 'BtnSelectCRM'>
           			  					    	<i class="fa fa-search" aria-hidden="true"></i>
           			  					    </span>
                                                <input type="text" class="form-control" id="ID_Crm" disabled="" value="null" idtable = "0">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class = "row" style="margin-left: 0px;margin-right: 0px;margin-top: 10px">
                    <div class="col-md-6">
                        <div class="panel panel-primary">
                            <div class="panel-heading clearfix">
                                <h4 class="panel-title pull-left" style="padding-top: 7.5px;">Biodata Pembeli</h4>
                            </div>
                            <div class="panel-body">

                                <div class="form-horizontal">
                                    <div class="form-group">
                                        <label class="col-md-3 control-label">Nama </label>
                                        <div class="col-md-9">
                                            <input type="text" name="Name" id= "Name" placeholder="Input Nama Pembeli..." class="form-control">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-3 control-label">Jenis Kelamin </label>
                                        <div class="col-md-9">
                                            <select class="form-control" id="selectGender">
                                                <option value = "L" selected>Laki - Laki</option>
                                                <option value = "P">Perempuan</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">Hp </label>
                                        <div class="col-md-3">
                                            <input type="text" name="hp" id= "hp" placeholder="62811111011" class="form-control">
                                        </div>
                                        <label class="col-sm-3 control-label">Telp Rumah </label>
                                        <div class="col-md-3">
                                            <input type="text" name="telp_rmh" id= "telp_rmh" placeholder="62211111011" class="form-control">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">Email </label>
                                        <div class="col-md-9">
                                            <input type="text" name="email" id= "email" placeholder="" class="form-control">
                                            <br/>
                                            <p style="color: red">* Pastikan email aktif dan benar</p>
                                            <p style="color: red">* Email yang didaftarkan akan menjadi account login pada user dan pertukaran informasi</p>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">Sekolah </label>
                                        <div class="col-md-9">
                                            <input type="text" name="autoCompleteSchool" id= "autoCompleteSchool" placeholder="Autocomplete" class="form-control">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">Sumber Iklan </label>
                                        <div class="col-md-9">
                                            <select class="select2-select-00 col-md-4 full-width-fix" id="selectSourceFrom">
                                                <option></option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="panel panel-primary">
                            <div class="panel-heading clearfix">
                                <h4 class="panel-title pull-left" style="padding-top: 7.5px;">Channel</h4>
                            </div>
                            <div class="panel-body">
                                <div class="form-horizontal">
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">Tipe Channel </label>
                                        <div class="col-md-9">
                                            <table>
                                                <tr>
                                                    <td>
                                                        <label class="radio-inline">
                                                            <input type="radio" name="tipeChannel" value = "Admission Office" class = "tipeChannel"> Admission Office
                                                        </label>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <label class="radio-inline">
                                                            <input type="radio" name="tipeChannel" value = "Event" class = "tipeChannel"> Event
                                                        </label>
                                                    </td>
                                                    <td style="width: 223px;">
                                                        <select class="select2-select-00 col-md-4 full-width-fix" id="selectEvent">
                                                            <option></option>
                                                        </select>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>&nbsp</td>
                                                    <td>&nbsp</td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <label class="radio-inline">
                                                            <input type="radio" name="tipeChannel" value = "School" class = "tipeChannel"> School
                                                        </label>
                                                    </td>
                                                    <td>
                                                        <input type="text" name="autoCompleteSchoolChanel" id= "autoCompleteSchoolChanel" placeholder="Autocomplete" class="form-control">
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">Tipe Pembayaran </label>
                                        <div class="col-md-9">
                                            <table>
                                                <tr>
                                                    <td>
                                                        <label class="radio-inline">
                                                            <input type="radio" name="TypePay" value = "Cash" class = "TypePay" checked> Cash
                                                        </label>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <label class="radio-inline">
                                                            <input type="radio" name="TypePay" value = "Transfer" class = "TypePay"> Transfer
                                                        </label>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">Harga Formulir </label>
                                        <div class="col-md-9">
                                            <input type="text" name="priceFormulir" id= "priceFormulir" placeholder="" class="form-control">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">PIC </label>
                                        <div class="col-md-9">
                                            <label class="control-label" id="viewStdPICName">-</label>
                                            <input id="selectPIC" class="hide">
<!--                                            <select class="select2-select-00 col-md-4 full-width-fix" id="selectPIC">-->
<!--                                                <option></option>-->
<!--                                            </select>-->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class ="form-group">
                            <div align="right">
                                <button class="btn btn-inverse btn-notification" id="btn-proses" action = "<?php echo $action ?>" kode-unique = "<?php echo $CDID ?>">Save</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    window.temp1 = '';
    window.temp2 = '';
    function loadFormulirCodeGlobal()
    {
        <?php
        $Division = $this->session->userdata('PositionMain')['IDDivision'];
        ?>
        $("No_Ref").empty();
        <?php if ($Division == 12): ?>
        var division = $('#Division').val();
        <?php else: ?>
        var division = <?php echo $Division ?>;
        <?php endif ?>
        var selectTahun = '<?php echo $Ta ?>';
        <?php if ($action != 'edit'): ?>
            var url = base_url_js+'rest/__loadDataFormulirGlobal_available';
            var data = {
                selectTahun : selectTahun,
                auth : 's3Cr3T-G4N',
                division : division,
                action : 'add',
                TypeFormulir : 'Off',
            };
            var token = jwt_encode(data,"UAP)(*");
            $.post(url,{token:token},function (data_json) {
                for (var i = 0; i < data_json.length; i++) {
                        var selected = (i==0) ? 'selected' : '';
                        $('#No_Ref').append('<option value="'+data_json[i].FormulirCodeGlobal+'" '+selected+'>'+data_json[i].FormulirCodeGlobal+'</option>');
                    }

                $('#No_Ref').select2({
                    // allowClear: true
                });
                  
                loadingEnd(1000);
            });
        <?php endif ?>

        <?php if ($action == 'edit'): ?>
            var NoRefGlobal = "<?php echo $get2[0]['No_Ref'] ?>";
            $('#No_Ref').append('<option value="'+NoRefGlobal+'" '+'selected'+'>'+NoRefGlobal+'</option>');
            $('#No_Ref').select2({
                // allowClear: true
            });
            $('#No_Ref').prop('disabled',true);
            loadingEnd(1000);
        <?php endif ?>  
    }

    function loadFormulirCode()
    {
        $("#selectFormulirCode").empty();
        <?php if ($action != 'edit'): ?>
        var url = base_url_js+'api/__getFormulirOfflineAvailable/0';
            $.get(url,function (data_json) {
                if(data_json.length > 0)
                {
                    for(var i=0;i<data_json.length;i++){
                        var selected = (i==0) ? 'selected' : '';
                        $('#selectFormulirCode').append('<option value="'+data_json[i].FormulirCode+'" '+selected+'>'+data_json[i].FormulirCode+'</option>');
                    }
                }
                else
                {
                    toastr.error('Formulir Code Offline belum ada yang di print...<br>Silahkan di print dahulu.', 'Failed!!');
                }

                $('#selectFormulirCode').select2({
                    allowClear: true
                });

            }).done(function () {
                // loadSelectSma1();
            });
        <?php endif ?>

        <?php if ($action == 'edit'): ?>
            $('#selectFormulirCode').append('<option value="<?php echo $get1[0]['FormulirCodeOffline'] ?>" '+''+'><?php echo $get1[0]['FormulirCodeOffline'] ?></option>');
            $('#selectFormulirCode').select2({
                allowClear: true
            });
            $('#selectFormulirCode').prop('disabled',true);
        <?php endif ?>
    }

    function loadProgramStudy()
    {
        var url = base_url_js+"api/__getBaseProdiSelectOption";
        $('#selectProgramStudy').empty();
        $('#selectProgramStudy2').empty();
        $('#selectProgramStudy2').append('<option value="'+0+'" '+'selected'+'>'+'--No Choice--'+'</option>');
        $.post(url,function (data_json) {
            for(var i=0;i<data_json.length;i++){
                var selected = (i==0) ? 'selected' : '';
                //var selected = (data_json[i].RegionName=='Kota Jakarta Pusat') ? 'selected' : '';
                $('#selectProgramStudy').append('<option value="'+data_json[i].ID+'" '+selected+'>'+data_json[i].Name+'</option>');
                $('#selectProgramStudy2').append('<option value="'+data_json[i].ID+'" '+''+'>'+data_json[i].Name+'</option>');
            }
            $('#selectProgramStudy').select2({
                //allowClear: true
            });
            $('#selectProgramStudy2').select2({
                //allowClear: true
            });

            <?php if ($action == 'edit'): ?>
            $("#selectProgramStudy option").filter(function() {
                //may want to use $.trim in here
                return $(this).val() == "<?php echo $get1[0]['ID_ProgramStudy'] ?>";
            }).prop("selected", true);
            $('#selectProgramStudy').select2({
                allowClear: true
            });
            <?php endif ?>

            <?php if ($action == 'edit'): ?>
            $("#selectProgramStudy2 option").filter(function() {
                //may want to use $.trim in here
                return $(this).val() == "<?php echo $get1[0]['ID_ProgramStudy2'] ?>";
            }).prop("selected", true);
            $('#selectProgramStudy2').select2({
                allowClear: true
            });
            <?php endif ?>
        }).done(function () {

        });
    }

    function autoCompleteSchool(ID)
    {
        ID.autocomplete({
            minLength: 4,
            select: function (event, ui) {
                event.preventDefault();
                var selectedObj = ui.item;
                // console.log(selectedObj);
                // $("#Nama").appendTo(".foo");
                // ID.val(selectedObj.value);
                ID.val(selectedObj.label);

                var test = ID.attr('name');
                if(test == 'autoCompleteSchoolChanel')
                {
                    temp2 = '';
                    temp2 = selectedObj.value;
                    // console.log(temp2);
                }
                else
                {
                    temp1 = '';
                    temp1 =  selectedObj.value;

                }
                // loadSubMenu();
                console.log(temp1);
                console.log(temp2);
            },
            /*select: function (event,  ui)
            {

            },*/
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

    function loadSumberIklan()
    {
        var url = base_url_js+"api/__getSumberIklan";
        $('#selectSourceFrom').empty()
        $.post(url,function (data_json) {
            for(var i=0;i<data_json.length;i++){
                var selected = (i==0) ? 'selected' : '';
                //var selected = (data_json[i].RegionName=='Kota Jakarta Pusat') ? 'selected' : '';
                $('#selectSourceFrom').append('<option value="'+data_json[i].ID+'" '+selected+'>'+data_json[i].src_name+'</option>');
            }
            $('#selectSourceFrom').select2({
                //allowClear: true
            });

            <?php if ($action == 'edit'): ?>
            $("#selectSourceFrom option").filter(function() {
                //may want to use $.trim in here
                return $(this).val() == "<?php echo $get1[0]['source_from_event_ID'] ?>";
            }).prop("selected", true);
            $('#selectSourceFrom').select2({
                allowClear: true
            });
            <?php endif ?>
        }).done(function () {

        });
    }

    function DomRadioChannel()
    {
        $('input:radio[name="tipeChannel"]').change(
            function(){
                var valuee = this.value;
                switch(valuee) {
                    case "Admission Office":
                        $("#selectEvent").addClass("hide");
                        $("#autoCompleteSchoolChanel").addClass("hide");
                        var url = base_url_js+"api/__getPriceFormulirOffline";
                        $.post(url,function (data_json) {
                            $("#priceFormulir").val(data_json[0].PriceFormulir);
                            $('#priceFormulir').maskMoney('mask', '9894');
                            <?php if ($action == 'edit'): ?>
                            $("#priceFormulir").val("<?php echo $get1[0]['Price_Form'] ?>");
                            $('#priceFormulir').maskMoney('mask', '9894');
                            <?php endif ?>
                        })
                        break;
                    case "Event":
                        var url = base_url_js+"api/__getEvent";
                        $("#autoCompleteSchoolChanel").addClass("hide");
                        $('#selectEvent').empty();
                        $.post(url,function (data_json) {
                            $("#selectEvent").removeClass("hide");
                            for(var i=0;i<data_json.length;i++){
                                var selected = (i==0) ? 'selected' : '';
                                //var selected = (data_json[i].RegionName=='Kota Jakarta Pusat') ? 'selected' : '';
                                $('#selectEvent').append('<option value="'+data_json[i].ID+'" '+selected+' price = "'+data_json[i].evn_price+'">'+data_json[i].evn_name+'</option>');
                            }
                            $('#selectEvent').select2({
                                //allowClear: true
                            });

                            <?php if ($action == 'edit'): ?>
                            $("#selectEvent option").filter(function() {
                                //may want to use $.trim in here
                                return $(this).val() == "<?php echo $get1[0]['price_event_ID'] ?>";
                            }).prop("selected", true);
                            $('#selectEvent').select2({
                                allowClear: true
                            });
                            <?php endif ?>
                        }).done(function () {

                        });
                        break;
                    case "School":
                        $("#autoCompleteSchoolChanel").removeClass("hide");
                        $("#selectEvent").addClass("hide");
                        autoCompleteSchool($("#autoCompleteSchoolChanel"));
                        var url = base_url_js+"api/__getPriceFormulirOffline";
                        $.post(url,function (data_json) {
                            $("#priceFormulir").val(data_json[0].PriceFormulir);
                            $('#priceFormulir').maskMoney('mask', '9894');
                            <?php if ($action == 'edit'): ?>
                            $("#priceFormulir").val("<?php echo $get1[0]['Price_Form'] ?>");
                            $('#priceFormulir').maskMoney('mask', '9894');
                            <?php endif ?>
                        })
                        break;
                    default:
                    // text = "I have never heard of that fruit...";
                }
            });
    }

    $(document).ready(function () {
        loadingStart();
        $('#tanggal').prop('readonly',true);
        $("#tanggal").datepicker({
            dateFormat: 'yy-mm-dd',

        });
        $("#selectEvent").addClass("hide");
        $("#autoCompleteSchoolChanel").addClass("hide");
        loadFormulirCode();
        loadProgramStudy();
        autoCompleteSchool($("#autoCompleteSchool"));
        loadSumberIklan();
        DomRadioChannel();

        $('#priceFormulir').maskMoney({thousands:'.', decimal:',', precision:0,allowZero: true});

        FuncEventDom();
        loadFormulirCodeGlobal();

        <?php if ($action == 'edit'): ?>
        $('#tanggal').val("<?php echo $get1[0]['DateSale'] ?>");
        // $("#No_Ref").val("<?php echo $get2[0]['No_Ref'] ?>");
        $("#Name").val("<?php echo $get1[0]['FullName'] ?>");
        $("#hp").val("<?php echo $get1[0]['PhoneNumber'] ?>");
        $("#email").val("<?php echo $get1[0]['Email'] ?>");
        $("#autoCompleteSchool").val("<?php echo $get3[0]['SchoolName'] ?>");
        $("#autoCompleteSchoolChanel").val("<?php echo $get4[0]['SchoolName'] ?>");
        $("#selectGender option").filter(function() {
            //may want to use $.trim in here
            return $(this).val() == "<?php echo $get1[0]['Gender'] ?>";
        }).prop("selected", true);
        $("#telp_rmh").val("<?php echo $get1[0]['HomeNumber'] ?>");

        $('input:radio[name="tipeChannel"][value ="<?php echo $get1[0]['Channel'] ?>"]').prop("checked", true);
        $('input:radio[name="tipeChannel"][value ="<?php echo $get1[0]['Channel'] ?>"]').trigger('change');
        temp1 = "<?php echo $get1[0]['SchoolID'] ?>";
        temp2 = "<?php echo $get1[0]['SchoolIDChanel'] ?>";
        $("#priceFormulir").val("<?php echo $get1[0]['Price_Form'] ?>");
        $('#priceFormulir').maskMoney('mask', '9894');
        $('input:radio[name="TypePay"][value ="<?php echo $get1[0]['TypePay'] ?>"]').prop("checked", true);
        var ID_Crm = "<?php echo $get1[0]['ID_Crm'] ?>";
        if (ID_Crm != 0) {
            var a = 1;
            $('input.FromCrm').prop('checked', false);
            $('.FromCrm[value="'+a+'"]').prop('checked',true);
            $(".FromCrm").trigger('click');

            $("#ID_Crm").val("<?php echo $get1[0]['FullName'] ?>");
            $("#ID_Crm").attr("idtable",ID_Crm);
        }

        $('#viewStdPICName').html("<?= $PICName['Name'] ?>");
        $('#selectPIC').val(<?= $get1[0]['PIC'] ?>);
        <?php endif ?>

    });

    function FuncDisabledInput()
    {
        $("#InputRef").addClass('hide');
    }

    function FuncClickRefCode()
    {
        $(".RefCode").click(function(){
            $('input.RefCode').prop('checked', false);
            $(this).prop('checked',true);
            var valuee = $(this).val();
            $("#InputRef").addClass('hide');
            if (valuee == 0) {
                $("#InputRef").removeClass('hide');
            }

        })
    }

    function FuncEventDom()
    {
        FuncClickRefCode();

        $("#selectEvent").change(function(){
            var option = $('option:selected', this).attr('price');
            $('#priceFormulir').val(option);
            $('#priceFormulir').maskMoney('mask', '9894');
        });

        $('#btn-proses').click(function(){
            var cf = $(".RefCode:checked").val();
            // var bool = (cf == '' || cf == null) ? false : true;
            var bool = true;
            <?php if ($action == 'edit'): ?>
            bool = true;
            <?php endif ?>
            if (!bool) {
                toastr.error('Mohon pilih No Ref','Failed!')
            }
            else
            {
                // loading_button('#btn-proses');
                var selectFormulirCode = $("#selectFormulirCode").val();
                var selectProgramStudy = $("#selectProgramStudy").val();
                var selectProgramStudy2 = $("#selectProgramStudy2").val();


                var Name = $("#Name").val().trim();
                var hp = $("#hp").val().trim();
                var email = $("#email").val().trim();
                // var autoCompleteSchool = $("#autoCompleteSchool").val();
                var autoCompleteSchool = temp1;
                var selectSourceFrom = $("#selectSourceFrom").val();
                var selectGender = $("#selectGender").val();
                var telp_rmh = $("#telp_rmh").val().trim();
                var tipeChannel = $('input[name=tipeChannel]:checked').val();
                var TypePay = $('input[name=TypePay]:checked').val();
                var selectEvent = $("#selectEvent").val();
                // var autoCompleteSchoolChanel = $("#autoCompleteSchoolChanel").val();
                var autoCompleteSchoolChanel = temp2;
                var aksi = $(this).attr('action');
                var CDID = $(this).attr('kode-unique');
                var url = base_url_js+'admission/distribusi-formulir/formulir-offline/save';
                var PIC = $("#selectPIC").val();
                if (PIC == '' || PIC === undefined) {
                    PIC = "<?php echo $this->session->userdata('NIP') ?>";
                }
                var priceFormulir = $("#priceFormulir").val();
                var tanggal = $("#tanggal").val();
                var No_Ref = $("#No_Ref").val();
                if (No_Ref == undefined || No_Ref == null || No_Ref == '') {
                    toastr.error('Mohon Periksa No ref','!Failed');
                    return;
                }
                var ChkFromCrm = $('.FromCrm:checked').val();

                var ID_Crm = $("#ID_Crm").attr('idtable');

                // var output_ok = $('#output_ok').val();
                priceFormulir = priceFormulir.replace(".", "");
                var data = {
                    Action : aksi,
                    selectFormulirCode : selectFormulirCode,
                    selectProgramStudy:selectProgramStudy,
                    selectProgramStudy2 : selectProgramStudy2,
                    Name : Name,
                    hp : hp,
                    email : email,
                    autoCompleteSchool : autoCompleteSchool,
                    selectSourceFrom : selectSourceFrom,
                    selectGender : selectGender,
                    telp_rmh : telp_rmh,
                    tipeChannel : tipeChannel,
                    selectEvent : selectEvent,
                    autoCompleteSchoolChanel : autoCompleteSchoolChanel,
                    CDID : CDID,
                    priceFormulir : priceFormulir,
                    PIC : PIC,
                    tanggal : tanggal,
                    No_Ref : No_Ref,
                    TypePay : TypePay,
                    ChkFromCrm : ChkFromCrm,
                    ID_Crm : ID_Crm
                };

                if (validationInput = validation2(data) && ID_Crm!=null && ID_Crm!='') {

                    var token = jwt_encode(data,"UAP)(*");
                    var urlToken = jwt_encode(url,"UAP)(*");
                    var htmlNotification = '<div class="alert alert-warning" role="alert">'+
                                            '<p><h2><b>Mohon pastikan No Ref yang diinputkan adalah : <insertstyle style="color:red;">'+No_Ref+'</insertstyle></b></h2></p>'+
                                            '</div>'
                                                ;
                    // $('#GlobalModalLarge').attr('style','margin-top: 400px;display: block;')
                    $('#GlobalModalLarge .modal-header').html('<h4 class="modal-title">'+'Konfirmasi No Ref'+'</h4>');
                    $('#GlobalModalLarge .modal-body').html(htmlNotification);
                    $('#GlobalModalLarge .modal-footer').html('<button type="button" id="ModalbtnCancleForm" data-dismiss="modal" class="btn btn-default">Close</button><button type="button" id="ModalbtnSaveForm_Offline" class="btn btn-success" token="'+token+'" urlToken = "'+urlToken+'">Submit</button>');
                    $('#GlobalModalLarge').modal({
                        'show' : true,
                        'backdrop' : 'static'
                    });                            
                    return; // dialihkan ke Modal Konfirmasi Submit                            
                    $.post(url,{token:token},function (data_json) {

                        data_json = JSON.parse(data_json);

                        if(parseInt(data_json.Status)<=0) {


                            toastr.error(data_json.msg, 'Error');
                            $('#btn-proses').prop('disabled',false).html('Save');

                        } else {
                            setTimeout(function () {
                                // clearData();
                                // LoadListPenjualan();
                                $('.pageAnchor[page = "ListPenjualan"]').trigger('click');
                                toastr.options.fadeOut = 10000;
                                toastr.success('Data berhasil disimpan', 'Success!');
                                $('#btn-proses').prop('disabled',false).html('Save');
                                // $('a[href="#tab2primary"]').tab('show');
                            },2000);
                        }


                    });
                }
                else
                {

                    $('#btn-proses').prop('disabled',false).html('Save');
                }
            }

        });


        $("#BtnSelectCRM").click(function(){
            var html = '<div class="row"><div class="col-md-4 col-md-offset-4">' +
                '<div class="well"><select class="form-control" id="filterPeriod"></select>' +
                '</div></div></div>' +
                '<div id="viewTableCRM"></div>';

            $('#GlobalModalLarge .modal-header').html('<h4 class="modal-title">'+'Select Data CRM'+'</h4>');
            $('#GlobalModalLarge .modal-body').html(html);

            loadActivePeriod();

            $('#GlobalModalLarge .modal-footer').html('<button type="button" id="ModalbtnCancleForm" data-dismiss="modal" class="btn btn-default">Cancel</button>');
            $('#GlobalModalLarge').modal({
                'show' : true,
                'backdrop' : 'static'
            });

            $("#GlobalModalLarge").find(".modal-dialog").attr('style','width: 1200px');
            $("#tableData3 tbody").empty();



            $('#filterPeriod').change(function () {
                var filterPeriod = $('#filterPeriod').val();
                loadDataCRM(filterPeriod);
            });

        });



    }

    function loadActivePeriod() {
        var data = {
            action : 'activeCRMPeriode'
        };
        var token = jwt_encode(data,'UAP)(*');
        var url = base_url_js+'rest2/__crudCRMPeriode';

        $.post(url,{token:token},function (jsonResult) {
            var y = jsonResult[0].Year;
            var i = jsonResult[0].ID;

            loadSelectOptionCRMPeriod('#filterPeriod',i);

            loadDataCRM(i);


        });

    }

    function loadDataCRM(PeriodID) {

        $('#viewTableCRM').html('<div class="row">' +
            '                         <div class="col-md-12">' +
            '                             <table class="table table-bordered tableData" id ="tableData3">' +
            '                                 <thead>' +
            '                                 <tr>' +
            '                                     <th width = "3%" style = "text-align: center;background: #20485A;color: #FFFFFF;">No</th>' +
            '                                     <th style = "text-align: center;background: #20485A;color: #FFFFFF;">Candidate Name</th>' +
            '                                     <th style = "text-align: center;background: #20485A;color: #FFFFFF;">School</th>' +
            '                                     <th style = "text-align: center;background: #20485A;color: #FFFFFF;">Pathway</th>' +
            '                                     <th style = "text-align: center;background: #20485A;color: #FFFFFF;">Gender</th>' +
            '                                     <th style = "text-align: center;background: #20485A;color: #FFFFFF; width: 5%;">Prospect Year</th>' +
            '                                     <th style = "text-align: center;background: #20485A;color: #FFFFFF;">Parent Name</th>' +
            '                                     <th style = "text-align: center;background: #20485A;color: #FFFFFF; width: 5%;">Action</th>' +
            '                                 </tr>' +
            '                                 </thead>' +
            '                                 <tbody></tbody>' +
            '                             </table>' +
            '                         </div>' +
            '                     </div>');

        $.fn.dataTable.ext.errMode = 'throw';
        $.fn.dataTableExt.oApi.fnPagingInfo = function (oSettings)
        {
            return {
                "iStart": oSettings._iDisplayStart,
                "iEnd": oSettings.fnDisplayEnd(),
                "iLength": oSettings._iDisplayLength,
                "iTotal": oSettings.fnRecordsTotal(),
                "iFilteredTotal": oSettings.fnRecordsDisplay(),
                "iPage": Math.ceil(oSettings._iDisplayStart / oSettings._iDisplayLength),
                "iTotalPages": Math.ceil(oSettings.fnRecordsDisplay() / oSettings._iDisplayLength)
            };
        };

        var table = $('#tableData3').DataTable( {
            "fixedHeader": true,
            "processing": true,
            "destroy": true,
            "serverSide": true,
            "iDisplayLength" : 10,
            "ordering" : false,
            "ajax":{
                url : base_url_js+"admission/crm/showdata/"+PeriodID, // json datasource
                ordering : false,
                type: "post",  // method  , by default get
                // data : {length : $("select[name='tableData4_length']").val()},
                error: function(){  // error handling
                    $(".employee-grid-error").html("");
                    $("#employee-grid").append('<tbody class="employee-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
                    $("#employee-grid_processing").css("display","none");
                }
            },
            'createdRow': function( row, data, dataIndex ) {
                var btndel = '';
                $(row).attr('idtable',data[13]);
                //$( row ).find('td:eq(12)').remove();
            },
        } );
    }

    $(document).on('click','.btnShowDet',function () {

        var no = $(this).attr('data-no');
        var det_ = $('#det_'+no).val();

        var d = JSON.parse(det_);

        $('#ID_Crm').val(ucwords(d.Name));
        $("#ID_Crm").attr('idtable',d.ID),

        $('#viewStdName').html(ucwords(d.Name));
        $('#Name').val(ucwords(d.Name));


        $('#viewStdGender').html(ckNull(d.Gender));
        var vG = '';
        if(ckNull(d.Gender)!='' && ckNull(d.Gender)=='Male') {
            vG = 'L';
        } else if(ckNull(d.Gender)!='' && ckNull(d.Gender)=='Female') {
            vG = 'P';
        }
        $('#selectGender').val(vG);

        $('#viewStdEmail').html(ckNull(d.Email));
        $('#email').val(ckNull(d.Email));

        $('#viewStdMobile').html(ckNull(d.Mobile));
        $('#hp').val(ckNull(d.Mobile));

        $('#viewStdPhone').html(ckNull(d.Phone));
        $('#telp_rmh').val(ckNull(d.Phone));

        $('#viewStdSchool').html(ckNull(d.SchoolName));
        $('#autoCompleteSchool').val(ckNull(d.SchoolName));
        temp1 = ckNull(d.SchoolID);

        $('#viewStdPICName').html(ckNull(d.PICName));
        $('#selectPIC').val(d.NIP);


    });

    function ckNull(vall) {

        return (vall!=null) ? vall : '';

    }

    function clearData()
    {
        $('.tipeChannel').prop('checked', false);
        $("#selectEvent").addClass("hide");
        $("#autoCompleteSchoolChanel").addClass("hide");
        loadFormulirCode();
        loadProgramStudy();
        $('#Name').val('');
        $('#hp').val('');
        $('#email').val('');
        $('#autoCompleteSchool').val('');
        temp1 = '';
        temp2 = ''
        $('#telp_rmh').val('');
        $('#priceFormulir').val('');
        $("#ID_Crm").attr('idtable',0);
        $("#ID_Crm").val('null');

    }

    function validation2(arr)
    {
        var toatString = "";
        var result = "";
        for(var key in arr) {
            switch(key)
            {
                case "Name" :
                case "hp" :
                // case "email" :
                case "autoCompleteSchool" :
                case "selectSourceFrom" :
                case "selectGender" :
                // case "telp_rmh" :
                case "tipeChannel" :
                // case "selectEvent" :
                // case "autoCompleteSchoolChanel" :
                case "PIC" :
                case "selectProgramStudy" :
                case  "selectFormulirCode" :
                case  "tanggal" :
                    result = Validation_required(arr[key],key);
                    if (result['status'] == 0) {
                        toatString += result['messages'] + "<br>";
                    }
                    break;
                case "ChkFromCrm" :
                    if (arr[key] == 1) {
                        var a = $("#ID_Crm").val();
                        if (a == "null" || a == '') {
                            toatString += 'Please choose CRM Data' + "<br>";
                        }
                    }
                    break;
            }

        }
        if (toatString != "") {
            toastr.error(toatString, 'Failed!!');
            return false;
        }

        return true;
    }


    $(document).off('click', '#ModalbtnSaveForm_Offline').on('click', '#ModalbtnSaveForm_Offline',function(e) {
        $('#ModalbtnCancleForm').addClass('hide');
        var token  = $(this).attr('token');
        var urlToken = $(this).attr('urltoken');
        var url = jwt_decode(urlToken);
        var selector = $(this);
        loading_button2(selector);
        $.post(url,{token:token},function (data_json) {

            data_json = JSON.parse(data_json);

            if(parseInt(data_json.Status)<=0) {
                toastr.error(data_json.msg, 'Error');
                end_loading_button2(selector,'Submit');
                $('#ModalbtnCancleForm').removeClass('hide');

            } else {
                setTimeout(function () {
                    $('.pageAnchor[page = "ListPenjualan"]').trigger('click');
                    toastr.options.fadeOut = 10000;
                    toastr.success('Data berhasil disimpan', 'Success!');
                    $('#GlobalModalLarge').modal('hide');
                },2000);
            }

        });
    })
</script>