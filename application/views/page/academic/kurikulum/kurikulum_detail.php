

<style>
    .tab-pane {
        margin-bottom: 20px;
    }
    .table-smt thead th {
        text-align: center;
    }
    .btn-silabus,.btn-sap {
        border-radius: 20px;
        padding: 1px;
        padding-left: 5px;
        padding-right: 5px;
    }
</style>

<div class="col-md-12">
    <div class="tabbable tabbable-custom tabbable-full-width">
        <ul class="nav nav-tabs">
            <li class="active"><a href="#tab_mata_kuliah" data-toggle="tab">Mata Kuliah</a></li>
        </ul>
        <div class="tab-content row">
            <!--=== Overview ===-->
            <div class="tab-pane active" id="tab_mata_kuliah">

                <div id="DataMataKuliah"></div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {

        var token = "<?php echo $token; ?>";
        var url = base_url_js+"api/__getKurikulumByYear";
        window.allSmt = [];


        $.post(url,{token:token},function (data_json) {
            allSmt = [];

            if(data_json!=''){
                if(data_json.MataKuliah.length>0){
                    LoadDetailMK(data_json.MataKuliah);
                } else {
                    $('#DataMataKuliah').html('--- Mata Kuliah Belum Ditambahkan ---');
                    loading_modal_hide();
                }
                loadSemesterAdd();
            } else {
                loading_modal_hide();
            }


        });
    });

    $(document).on('click','.btn-add-mksmt',function () {
       var CurriculumID = $(this).attr('data-id');
       var Semester = $(this).attr('data-smt');
    });

    function LoadDetailMK(MataKuliah) {

         // console.log(MataKuliah);
        for(var i=0;i<MataKuliah.length;i++){
            if(MataKuliah.length==8){
                $('.btn-addsmt').prop('disabled',true);
            } else {
                $('.btn-addsmt').prop('disabled',false);
            }

            allSmt.push(parseInt(MataKuliah[i].Semester));

            $('#DataMataKuliah').append('<div class="col-md-12"> <div class="widget box">' +
                '                    <div class="widget-header" id="widgetSmt'+i+'">' +
                '                        <h4><i class="icon-reorder"></i> SEMESTER '+MataKuliah[i].Semester+'</h4>' +
                '<div class="toolbar no-padding">' +
                '    <div class="btn-group">' +
                '                        <span data-smt="'+MataKuliah[i].Semester+'" class="btn btn-xs btn-add-mksmt">' +
                '    <i class="icon-plus"></i> Add Mata Kuliah' +
                '     </span>' +
                '    </div>' +
                '</div>' +
                '                    </div>' +
                '                    <div class="widget-content no-padding ">' +
                '                        <table id="tableSemester'+i+'" class="table table-bordered table-striped table-smt">' +
                '                            <thead>' +
                '                            <tr>' +
                '                                <th style="width:10px;">No</th>' +
                '                                <th style="width:110px;">Code</th>' +
                '                                <th>Course</th>' +
                // '                                <th style="width: 15%;">Lecturer</th>' +
                '                                <th style="width: 15%;">Base Prodi</th>' +
                // '                                <th style="width: 5%;">Ed Level</th>' +
                '                                <th style="width: 5%;">Total Credit</th>' +
                '                                <th style="width: 5%;">Pre</th>' +
                // '                                <th style="width: 9%;">Syllabus</th>' +
                // '                                <th style="width: 9%;">SAP</th>' +
                '                            </tr>' +
                // '                            <tr>' +
                // '                                <th style="width:80px;">T</th>' +
                // '                                <th style="width:80px;">P</th>' +
                // '                                <th style="width:80px;">PKL</th>' +
                // '                            </tr>' +
                '                            </thead>' +
                '                            <tbody id="dataSmt'+i+'"></tbody>' +
                '                        </table>' +
                '                    </div>' +
                '                </div></div>');

            var detailSemester = MataKuliah[i].DetailSemester;
            var no=1;
            for(var s=0;s<detailSemester.length;s++){


                var StatusPrecondition = (detailSemester[s].StatusPrecondition==1) ? '<i class="fa fa-check-circle" style="color: green;"></i>' : '<i class="fa fa-minus-circle" style="color: red;"></i>';

                var silabus = (detailSemester[s].StatusSilabus==1) ? '<button class="btn btn-sm btn-default btn-default-danger btn-silabus hide"><i class="fa fa-download" aria-hidden="true"></i></button> ' +
                    '<label class="btn btn-sm btn-default btn-default-success btn-silabus"><i class="fa fa-upload" aria-hidden="true"></i> Upload <input type="file" style="display: none;"></label>' : '<i class="fa fa-minus-circle" style="color: red;"></i>';

                var sap = (detailSemester[s].StatusSAP==1) ? '<button class="btn btn-sm btn-default btn-default-success btn-silabus hide"><i class="fa fa-download" aria-hidden="true"></i></button> ' +
                    '<label class="btn btn-sm btn-default btn-default-success btn-silabus"><i class="fa fa-upload" aria-hidden="true"></i> Upload <input type="file" style="display: none;"></label>' : '<i class="fa fa-minus-circle" style="color: red;"></i>';

                var StatusMK = (detailSemester[s].StatusMK==1) ? 'green' : 'red';

                var urlPDF = base_url_js+'uploads/syllabus_sap/'+detailSemester[s].Syllabus;
                var syllabus = (detailSemester[s].Syllabus!=null && detailSemester[s].Syllabus!=0 && UrlExists(urlPDF)) ?
                    '<a href="'+urlPDF+'" class="btn btn-sm btn-default btn-default-success btn-silabus" download="Syllabus.pdf"><i class="fa fa-download" aria-hidden="true"></i> Download<a/>' :
                    '<label class="btn btn-sm btn-default btn-default-danger btn-silabus"><i class="fa fa-upload" aria-hidden="true"></i> Upload <input type="file" style="display: none;"></label>';

                $('#dataSmt'+i).append('<tr>' +
                    '<td class="td-center">'+(no++)+'</td>' +
                    '<td class="td-center"><span style="color:'+StatusMK+';">'+detailSemester[s].MKCode+'</span></td>' +
                    '<td><div><a href="javascript:void(0)" class="detailMataKuliah" data-smt="'+MataKuliah[i].Semester+'" data-id="'+detailSemester[s].CDID+'"><b>'+detailSemester[s].NameMKEng+'</b></a>' +
                    '</td>' +
                    // '<td><div>'+detailSemester[s].NameLecturer+'</td>' +
                    '<td>'+detailSemester[s].ProdiNameEng+'</td>' +
                    // '<td class="td-center"><div>'+detailSemester[s].EducationLevel+'</div></td>' +
                    '<td class="td-center"><div>'+detailSemester[s].TotalSKS+'</div></td>' +
                    '<td class="td-center"><div>'+StatusPrecondition+'</div></td>' +
                    // '<td class="td-center"><div>'+silabus+'</div></td>' +
                    // '<td class="td-center"><div>'+syllabus+'</div></td>' +
                    // '<td class="td-center"><div>'+sap+'</div></td>' +
                    // '<td class="td-center"><div>'+detailSemester[s].SKSPraktikLapangan+'</div></td>' +
                    '</tr>');
            }

            LoaddataTable(i);
        }

        loading_modal_hide();


    }


    function loadSemesterAdd() {

        $('#addSmt').empty();
        for(var i2=1;i2<=14;i2++){
            // console.log('asds');
            if($.inArray(i2,allSmt)==-1){
                $('#addSmt').append('<li><a href="javascript:void(0)" data-smt="'+i2+'" data-action="add-semester" class="btn-control">Semester '+i2+'</a></li>');
            }

        }
        $('.btn-addsmt').prop('disabled',false);
    }

    function LoaddataTable(element) {
        var table = $('#tableSemester'+element).DataTable({
            'iDisplayLength' : 5,
            'ordering' : false,
            "sDom": "<'row'<'dataTables_header clearfix'<'col-md-3'l><'col-md-9'Tf>r>>t<'row'<'dataTables_footer clearfix'<'col-md-6'i><'col-md-6'p>>>", // T is new
            "oTableTools": {
                "aButtons": [
                    // "copy",
                    // "print",
                    // "csv",
                    {
                        "sExtends" : "xls",
                        "sButtonText" : '<i class="fa fa-download" aria-hidden="true"></i> Excel',
                    },
                    {
                        "sExtends" : "pdf",
                        "sButtonText" : '<i class="fa fa-download" aria-hidden="true"></i> PDF',
                        "sPdfOrientation" : "landscape",
                        // "sPdfMessage" : "Daftar Seluruh Mata Kuliah"
                    }
                ],
                "sSwfPath": "../assets/template/plugins/datatables/tabletools/swf/copy_csv_xls_pdf.swf"
            },
            // initComplete: function () {
            //     var elm = '#widgetSmt'+element;
            //     console.log(elm);
            //     this.api().columns().every( function () {
            //
            //         var column = this;
            //         var select = $('<select class="form-control filter-prodi"><option value=""></option></select>')
            //             .appendTo( $('#widgetSmt0 .dataTables_header .col-md-9'))
            //             .on( 'change', function () {
            //                 var val = $.fn.dataTable.util.escapeRegex(
            //                     $(this).val()
            //                 );
            //
            //                 column
            //                     .search( val ? '^'+val+'$' : '', true, false )
            //                     .draw();
            //             } );
            //         column.data().unique().sort().each( function ( d, j ) {
            //             var f = d.split('div');
            //             if(f.length<=1){
            //                 select.append( '<option value="'+d+'">'+d+'</option>' )
            //             } else {
            //                 select.remove();
            //             }
            //         } );
            //     } );
            // }
        });
    }
</script>