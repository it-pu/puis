
<div class="row" style="margin-top: 30px;">
    <div class="col-md-6">
        <table class="table">
            <tr>
                <td style="width: 15%;">Language</td>
                <td style="width: 1%;">:</td>
                <td>
                    <select style="max-width: 150px;" id="LangID" class="form-control"></select>
                </td>
            </tr>
            <tr>
                <td style="width: 15%;">Phone</td>
                <td style="width: 1%;">:</td>
                <td>
                    <input type="text" id="Tlp" class="form-control required" placeholder="ex: 08XXXXXXXXX">
                </td>
            </tr>
            <tr>
                <td>Description</td>
                <td>:</td>
                <td>
                    <textarea id="Description" class="form-control"></textarea>
                </td>
            </tr>
            <tr>
                <td colspan="3" style="text-align: right;">
                    <button class="btn btn-success" id="btnSave">Save</button>
                </td>
            </tr>
        </table>
    </div>

    <div class="col-md-6" style="border-left: 1px solid #CCCCCC;">
        <div id="viewDataDesc"></div>
    </div>
</div> 

<script>
    $(document).ready(function () {

        window.G_Type = 'call';

        loadSelectOptionLanguageProdi('#LangID','');

        $('#Description').summernote({
            placeholder: 'Text your announcement',
            tabsize: 2,
            height: 300,
            toolbar: [
                // [groupName, [list of button]]
                ['style', ['bold', 'italic', 'underline', 'clear']],
                ['font', ['strikethrough', 'superscript', 'subscript']],
                ['fontsize', ['fontsize']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['height', ['height']]
            ],
            callbacks: {
                  onPaste: function (e) {
                    var bufferText = ((e.originalEvent || e).clipboardData || window.clipboardData).getData('text/html');
                    e.preventDefault();
                    var div = $('<div />');
                    div.append(bufferText);
                    div.find('*').removeAttr('style');
                    setTimeout(function () {
                      document.execCommand('insertHtml', false, div.html());
                    }, 10);
                  }
                }
        });

        loadDataCall();

        var firsLoad = setInterval(function () {

            var LangID = $('#LangID').val();
            if(LangID!='' && LangID!=null){
                loadDataOption();
                clearInterval(firsLoad);
            }

        },1000);

    });

    $('#LangID').change(function () {
        var LangID = $('#LangID').val();
        if(LangID!='' && LangID!=null){
            loadDataOption();
        }
    });

    function loadDataCall() {
        var data = {
            action : 'readProdiTexting',
            Type : G_Type
        };
        var token = jwt_encode(data,'UAP)(*');
        var url = base_url_js+'api-prodi/__crudDataProdi';

        $.post(url,{token:token},function (jsonResult) {
            $('#viewDataDesc').empty();
            if(jsonResult.length>0){

                $.each(jsonResult,function (i,v) {
                    $('#viewDataDesc').append('<div class="well"><h3 style="margin-top: 5px;"><b>'+v.Language+'</b></h3><p>'+v.Tlp+'</p><div>'+v.Description+'</div></div>');
                });

            } else {
                $('#viewDataDesc').html('<div class="well">Data not yet</div>');
            }

        });
    }

    function loadDataOption() {
        var LangID = $('#LangID').val();
        if(LangID!='' && LangID!=null){
            var data = {
                action : 'readDataProdiTexting',
                Type : G_Type,
                LangID : LangID
            };
            var token = jwt_encode(data,'UAP)(*');
            var url = base_url_js+'api-prodi/__crudDataProdi';
            $.post(url,{token:token},function (jsonResult) {
                if(jsonResult.length>0){
                    $('#Description').summernote('code', jsonResult[0].Description);
                    $('#Tlp').val(jsonResult[0].Tlp);
                } else {
                    $('#Description').summernote('code', '');
                    $('#Tlp').val('');
                }

            });
        }
    }

    $('#btnSave').click(function () {

        var LangID = $('#LangID').val();
        var Description = $('#Description').val();
        var Tlp = $('#Tlp').val();
        if(LangID!='' && LangID!=null &&
            Description!='' && Description!=null &&
            Tlp!='' && Tlp!=null){
            var prodi_texting = {
                    Type : G_Type,
                    LangID : LangID,
                    Description : Description,
                    UpdatedBy : sessionNIP,
            };

            var calldetail = {
              Tlp : Tlp,
            }
            var data = {
                action : 'saveProdiCall',
                calldetail : calldetail,
                prodi_texting : prodi_texting,
            };

            var token = jwt_encode(data,'UAP)(*');
            var url = base_url_js+'api-prodi/__crudDataProdi';
            $.post(url,{token:token},function (jsonResult) {

                toastr.success('Data saved','Success');
                loadDataCall();
            })

        }

    });

</script>
    