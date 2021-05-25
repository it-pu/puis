<style>
    #tbMonitoringAttdLect thead th {
        text-align: center;
        background-color: #436888;
        color: #ffffff;
    }

    #tbMonitoringAttdLect tbody td {
        text-align: center;
    }
</style>



<div class="row">
    <div class="col-md-8 col-md-offset-2">
        <div class="well">
            <div class="row">
                <div class="col-xs-4">
                    <select class="form-control" id="filterSemester"></select>
                </div>
                <div class="col-xs-5">
                    <select class="form-control" id="filterProgrammeStudy"></select>
                </div>
                <div class="col-xs-3">
                    <button id="btnSave2PDF_monitoringAttdLec" disabled class="btn btn-default btn-default-success btn-block">Download to PDF</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <hr />
        <div id="divShowMonitoringAttd"></div>
    </div>
    <form id="FormHide2PDF" action="<?php echo base_url('save2pdf/monitoringAttdLecturer'); ?>" method="post" target="_blank">
        <textarea id="dataFormHide2PDF" class="hide" hidden name="token"></textarea>
    </form>

</div>


<script>
    $(document).ready(function() {

        setLoadFullPage();

        window.save2PDF = [];

        $('#filterSemester').empty();
        loSelectOptionSemester('#filterSemester', '');

        loadSelectOptionBaseProdi('#filterProgrammeStudy', '');

        window.loadFirstTime = setInterval(function() {
            var filterSemester = $('#filterSemester').val();
            var filterProgrammeStudy = $('#filterProgrammeStudy').val();
            if (filterSemester != '' && filterSemester != null &&
                filterProgrammeStudy != '' && filterProgrammeStudy != null) {
                loadingDataMonitoringAttd();

                clearInterval(loadFirstTime);
            }
        }, 1000);

    });

    $(document).on('change', '#filterSemester,#filterProgrammeStudy', function() {
        loadingDataMonitoringAttd();
    });

    $(document).on('click', '#btnSave2PDF_monitoringAttdLec', function() {
        $('#FormHide2PDF').submit();
    });

    function loadingDataMonitoringAttd() {
        var filterSemester = $('#filterSemester').val();
        var filterProgrammeStudy = $('#filterProgrammeStudy').val();

        if (filterSemester != '' && filterSemester != null &&
            filterProgrammeStudy != '' && filterProgrammeStudy != null) {

            save2PDF = [];
            $('#btnSave2PDF_monitoringAttdLec').prop('disabled', true);

            var SemesterID = filterSemester.split('.')[0];
            var ProdiID = filterProgrammeStudy.split('.')[0];

            var data = {
                action: 'monitoringLecturer',
                SemesterID: SemesterID,
                ProdiID: ProdiID
            };
            var token = jwt_encode(data, 'UAP)(*');
            var url = base_url_js + 'api/__crudAttendance';

            $.post(url, {
                token: token
            }, function(jsonResult) {


                if (jsonResult.length > 0) {

                    $('#divShowMonitoringAttd').html('' +
                        '<div class="table-responsive">' +
                        '            <table class="table table-bordered" id="tbMonitoringAttdLect">' +
                        '                <thead>' +
                        '                <tr>' +
                        '                    <th rowspan="2" style="width: 3%;">No</th>' +
                        '                    <th rowspan="2" style="width: 6%;">Code</th>' +
                        '                    <th rowspan="2">Course</th>' +
                        '                    <th rowspan="2" style="width: 5%;">Group</th>' +
                        '                    <th rowspan="2">Lecturer</th>' +
                        '                    <th rowspan="2" style="width: 7%;">Day</th>' +
                        '                    <th rowspan="2" style="width: 10%;">Time</th>' +
                        '                    <th rowspan="2" style="width: 10%;">Room</th>' +
                        '                    <th colspan="3">Session</th>' +
                        '                </tr>' +
                        '                <tr>' +
                        '                    <th style="width: 5%;">Target</th>' +
                        '                    <th style="width: 5%;">Real</th>' +
                        '                    <th style="width: 5%;">%</th>' +
                        '                </tr>' +
                        '                </thead>' +
                        '                <tbody id="dataRWMonitoringAttdLect"></tbody>' +
                        '            </table><hr/>' +
                        '        </div>');

                    var tr = $('#dataRWMonitoringAttdLect');
                    tr.empty();

                    var no = 1;
                    // var pdf = [];
                    for (var i = 0; i < jsonResult.length; i++) {
                        var d = jsonResult[i];

                        var rwSpan = 1 + parseInt(d.Schedule.length);

                        var lec = (d.Lecturer != null && d.Lecturer != '') ? d.Lecturer : '-';
                        tr.append('<tr>' +
                            '<td rowspan="' + rwSpan + '">' + no + '</td>' +
                            '<td rowspan="' + rwSpan + '">' + d.MKCode + '</td>' +
                            '<td rowspan="' + rwSpan + '" style="text-align: left;">' + d.MKNameEng + '</td>' +
                            '<td rowspan="' + rwSpan + '">' + d.ClassGroup + '</td>' +
                            '<td rowspan="' + rwSpan + '" style="text-align: left;">' + lec + '</td>' +
                            '</tr>');



                        var arr_course_pdf = [];
                        for (var s = 0; s < d.Schedule.length; s++) {

                            var dt2 = d.Schedule[s];
                            var time = dt2.StartSessions.substr(0, 5) + ' - ' + dt2.EndSessions.substr(0, 5);

                            tr.append('<tr>' +
                                '<td>' + dt2.DayEng + '</td>' +
                                '<td>' + time + '</td>' +
                                '<td>' + dt2.Room + '</td>' +
                                '<td>' + d.totalSession + '</td>' +
                                '<td style="background: #f5f5f5;"><span id="idAttd' + i + '_' + s + '">0</span></td>' +
                                '<td><span id="idAttdPercent' + i + '_' + s + '"></span> %</td>' +
                                '</tr>');

                            // Hitung Attd
                            var Attd = dt2.TotalPresent;
                            // for(var a=1;a<=14;a++){
                            //     if(dt2.Attendance[0]['Meet'+a] !=null
                            //         && dt2.Attendance[0]['Meet'+a]!='0'
                            //         && dt2.Attendance[0]['Meet'+a]!=0){
                            //         Attd = Attd + 1;
                            //     }
                            // }

                            $('#idAttd' + i + '_' + s).html(Attd);

                            var percent = (Attd != 0) ? (Attd / d.totalSession * 100) : 0.00;
                            $('#idAttdPercent' + i + '_' + s).html(percent.toFixed(2));

                            var course_pdf = {
                                Day: dt2.DayEng,
                                Time: time,
                                Room: dt2.Room,
                                Target: d.totalSession,
                                Real: Attd,
                                Percent: percent.toFixed(2) + ' %'
                            };
                            arr_course_pdf.push(course_pdf);
                        }

                        var pdf = {
                            No: no,
                            MKCode: d.MKCode,
                            MKNameEng: d.MKNameEng,
                            ClassGroup: d.ClassGroup,
                            Lecturer: lec,
                            Schedule: arr_course_pdf
                        };

                        save2PDF.push(pdf);

                        no++;
                    }
                } else {
                    $('#divShowMonitoringAttd').html('<div style="text-align:center;"><h3 style="color: #ccc;font-weight: bold;">-- Data Not Yet --</h3></div>');
                }

                var token = jwt_encode({
                    Prodi: $('#filterProgrammeStudy option:selected').text(),
                    Semester: $('#filterSemester option:selected').text(),
                    save2PDF: save2PDF
                }, 'UAP)(*');

                $('#dataFormHide2PDF').val(token);


                $('#btnSave2PDF_monitoringAttdLec').prop('disabled', false);

            });

        }

    }
</script>