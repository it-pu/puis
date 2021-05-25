<style>
    #tbContrctMonitoring thead th {
        text-align: center;
        background: #20485A;
        color: #FFFFFF;
    }

    #tbContrctMonitoring tbody td {
        text-align: center;
    }
</style>

<div class="row">
    <div class="col-md-8 col-md-offset-2">
        <div class="well">
            <div class="row">
                <div class="col-xs-2">
                    <label>Semester</label>
                    <select class="form-control" id="filterSemester"></select>
                </div>
                <div class="col-xs-3">
                    <label>Status Employees</label>
                    <select class="form-control" id="formStatusEmployee"></select>
                </div>
                <div class="col-xs-3">
                    <label>Status Lecturer</label>
                    <select class="form-control" id="formStatusLecturer"></select>
                </div>
                <div class="col-xs-4">
                    <label>Range Date</label>
                    <input id="filterRangeStart" class="hide" hidden readonly>
                    <input id="filterRangeEnd" class="hide" hidden readonly>
                    <input id="RangeDate" class="hide" hidden readonly>
                    <button class="btn btn-default btn-block" id="formSetRange"><i class="fa fa-calendar" aria-hidden="true"></i> | ( <i id="viewRange"><span></span></i> )</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div style="text-align: right;">
            <button class="btn btn-default btn-default-success" id="btnSave2PDFWithRangeDate" disabled>Download to PDF</button>
            <form id="FormHide2PDF" action="<?php echo base_url('save2pdf/monitoringAttendanceByRangeDate'); ?>" method="post" target="_blank">
                <textarea id="dataFormHide2PDF" class="hide" hidden name="token"></textarea>
            </form>
        </div>
        <hr />

        <div id="divContrctMonitoring"></div>
    </div>
</div>
<ul id="rangec"></ul>

<script>
    $(document).ready(function() {
        setLoadFullPage();
        $('#filterSemester').empty();
        loSelectOptionSemester('#filterSemester', '');

        // loadSelectOptionStatusEmployee('#filterStatusEmployees',4);

        loadSelectOptionEmployeesStatus2('#formStatusEmployee', "");
        loadSelectOptionLecturerStatus2('#formStatusLecturer', 4);

        window.loadFirst = setInterval(function() {
            var filterSemester = $('#filterSemester').val();
            var formStatusEmployee = $('#formStatusEmployee').val();
            var formStatusLecturer = $('#formStatusLecturer').val();

            var filterRangeStart = $('#filterRangeStart').val();
            var filterRangeEnd = $('#filterRangeEnd').val();

            if (filterSemester != '' && filterSemester != null &&
                formStatusEmployee != '' && formStatusEmployee != null &&
                filterRangeStart != '' && filterRangeStart != null &&
                filterRangeEnd != '' && filterRangeEnd != null &&
                formStatusLecturer != '' && formStatusLecturer != null
            ) {
                loadTb();
                clearInterval(loadFirst);
            }

        }, 1000);
    });

    $(document).on('click', '#btnSave2PDFWithRangeDate', function() {
        $('#FormHide2PDF').submit();
    });

    $(document).on('change', '#filterSemester,#formStatusEmployee,#formStatusLecturer', function() {
        loadTb();
    });


    $('#formSetRange').daterangepicker({
            startDate: moment().subtract('days', 29),
            endDate: moment(),
            minDate: '01/01/2014',
            maxDate: moment().add(10, 'days').format('DD/MM/YYYY'),
            // maxDate: '12/12/2018',
            dateLimit: {
                days: 31
            },
            showDropdowns: true,
            showWeekNumbers: true,
            timePicker: false,
            timePickerIncrement: 1,
            timePicker12Hour: true,
            opens: 'left',
            buttonClasses: ['btn btn-default'],
            applyClass: 'btn-sm btn-primary',
            cancelClass: 'btn-sm',
            format: 'DD/MM/YYYY',
            separator: ' to ',
            locale: {
                applyLabel: 'Submit',
                fromLabel: 'From',
                toLabel: 'To',
                customRangeLabel: 'Custom Range',
                daysOfWeek: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'],
                monthNames: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
                firstDay: 1
            }
        },

        function(start, end) {

            var range_updated = start.format('DD MMMM YYYY') + ' - ' + end.format('DD MMMM YYYY');

            $('#filterRangeStart').val(start.format('YYYY-MM-DD'));
            $('#filterRangeEnd').val(end.format('YYYY-MM-DD'));
            $('#RangeDate').val(range_updated);

            loadTb();

            $('#viewRange span').html(range_updated);

        });

    $('#viewRange span').html(moment().subtract(29, 'days').format('DD MMMM YYYY') + ' - ' + moment().format('DD MMMM YYYY'));
    $('#RangeDate').val(moment().subtract(29, 'days').format('DD MMMM YYYY') + ' - ' + moment().format('DD MMMM YYYY'));
    $('#filterRangeStart').val(moment().subtract(29, 'days').format('YYYY-MM-DD'));
    $('#filterRangeEnd').val(moment().format('YYYY-MM-DD'));


    function loadTb() {

        loading_page('#divContrctMonitoring');

        var filterSemester = $('#filterSemester').val();
        var formStatusEmployee = $('#formStatusEmployee').val();
        var formStatusLecturer = $('#formStatusLecturer').val();

        var filterRangeStart = $('#filterRangeStart').val();
        var filterRangeEnd = $('#filterRangeEnd').val();
        var RangeDate = $('#RangeDate').val();


        var token2PDF = [];

        if (filterSemester != '' && filterSemester != null &&
            formStatusEmployee != '' && formStatusEmployee != null &&
            filterRangeStart != '' && filterRangeStart != null &&
            filterRangeEnd != '' && filterRangeEnd != null &&
            formStatusLecturer != '' && formStatusLecturer != null
        ) {

            var SemesterID = filterSemester.split('.')[0];
            var StatusEmployeeID = formStatusEmployee;

            var data = {
                action: 'showLecturerMonitoring',
                SemesterID: SemesterID,
                StatusEmployeeID: StatusEmployeeID,
                StatusLecturerID: formStatusLecturer,
                Start: filterRangeStart,
                End: filterRangeEnd
            };

            var token = jwt_encode(data, 'UAP)(*');
            var url = base_url_js + 'api/__crudEmployees';

            $.post(url, {
                token: token
            }, function(jsonResult) {

                var PDFarrDate = [];


                if (jsonResult.length > 0) {
                    var arrDate = momentRange(filterRangeStart, filterRangeEnd);

                    $('#divContrctMonitoring').html('<table class="table table-bordered" id="tbContrctMonitoring">' +
                        '<thead>' +
                        '<tr>' +
                        '<th style="width: 1%;" rowspan="2">No</th>' +
                        '<th rowspan="2" style="width: 3%;">NIP</th>' +
                        '<th rowspan="2" style="width: 15%;">Name</th>' +
                        '<th rowspan="2" style="width: 2%;">Total Credit</th>' +
                        '<th rowspan="2" style="width: 1%;">Credit</th>' +
                        '<th rowspan="2">Group</th>' +
                        '<th rowspan="2" style="width: 10%;">Course</th>' +
                        '<th colspan="' + arrDate.details.length + '">' + RangeDate + '</th>' +
                        '<th rowspan="2" style="width: 3%;">Total Sesi</th>' +
                        '<th rowspan="2" style="width: 3%;">Sesi x Credit</th>' +
                        '</tr>' +
                        '<tr id="trHead"></tr>' +
                        '</thead><tbody id="dataLec"></tbody>' +
                        '</table>');

                    $.each(arrDate.details, function(i, e) {
                        var bg = (moment(e).days() == 0 || moment(e).days() == 6) ? 'background:#bb1818;' : '';
                        $("#trHead").append('<th style="font-size:10px;width: 1%;' + bg + '">' + moment(e).format("DD") + '</th>');
                        PDFarrDate.push(moment(e).format("YYYY-MM-DD"));
                    });

                    var no = 1;
                    for (var i = 0; i < jsonResult.length; i++) {
                        var d = jsonResult[i];

                        var rwSpan = 1 + d.Course.length;

                        $('#dataLec').append('<tr>' +
                            '<td rowspan="' + rwSpan + '">' + no + '</td>' +
                            '<td rowspan="' + rwSpan + '">' + d.NIP + '</td>' +
                            '<td rowspan="' + rwSpan + '" style="text-align: left;">' + d.Name + '</td>' +
                            '<td rowspan="' + rwSpan + '" id="showSum_' + no + '" style=""></td>' +
                            '</tr>');

                        var sumCredit = 0

                        for (var c = 0; c < d.Course.length; c++) {
                            var d_c = d.Course[c];
                            sumCredit = sumCredit + parseInt(d_c.Credit);
                            $('#dataLec').append('<tr id="trS_' + no + '_' + d_c.ScheduleID + '_' + d_c.SDID + '">' +
                                '<td>' + d_c.Credit + '</td>' +
                                '<td>' + d_c.ClassGroup + '</td>' +
                                '<td style="text-align: left;">' + d_c.NameEng + '</td>' +

                                '</tr>');

                            var totalSesi = 0;
                            $.each(arrDate.details, function(i, e) {

                                var bg = ''

                                if ($.inArray(moment(e).format("YYYY-MM-DD"), d_c.Attendance) != -1) {
                                    bg = 'style="background:#4CAF50;font-weight: bold;color:#fff;"';
                                } else if (moment(e).days() == 0 || moment(e).days() == 6) {
                                    bg = 'style="background:#f5f5f5;"';
                                }

                                var sts = 0;
                                if (d_c.Attendance.length > 0) {
                                    for (var sr = 0; sr < d_c.Attendance.length; sr++) {
                                        if (d_c.Attendance[sr] == moment(e).format("YYYY-MM-DD")) {
                                            sts = sts + 1;
                                        }
                                    }
                                }

                                totalSesi = totalSesi + sts;
                                var ssSts = (sts != 0) ? sts : '';


                                $("#trS_" + no + "_" + d_c.ScheduleID + "_" + d_c.SDID).append('<td ' + bg + '>' + ssSts + '</td>');


                            });

                            var totalCredit = (totalSesi != 0) ? totalSesi * parseInt(d_c.Credit) : 0;

                            $("#trS_" + no + "_" + d_c.ScheduleID + "_" + d_c.SDID).append('<td><b>' + totalSesi + '</b></td>');
                            $("#trS_" + no + "_" + d_c.ScheduleID + "_" + d_c.SDID).append('<td style="background: #ffeb3b38;"><b>' + totalCredit + '</b></td>');
                        }

                        $('#showSum_' + no).html(sumCredit);

                        no++;
                    }

                } else {
                    $('#divContrctMonitoring').html('<h4>Data Not Yet</h4>');
                }


                token2PDF = {
                    Semester: $('#filterSemester option:selected').text(),
                    Employees: $('#formStatusLecturer option:selected').text(),
                    RangeDate: RangeDate,
                    PDFarrDate: PDFarrDate,
                    Details: jsonResult
                };


                var token = jwt_encode(token2PDF, 'UAP)(*');
                $('#btnSave2PDFWithRangeDate').prop('disabled', false);
                $('#dataFormHide2PDF').val(token);

            });




        }






    }

    function loadDataPartime() {

        var token = jwt_encode({
            action: 'readPartime'
        }, 'UAP)(*');
        var url = base_url_js + 'api/__crudPartime';

        $.post(url, {
            token: token
        }, function(jsonResult) {



        });

    }
</script>