<style>
    #tableAttendance tr th {
        text-align: center;
        background: #607d8b;
        color: #FFFFFF;
    }

    .td-center {
        text-align: center;
    }

    .labelAttd {
        min-width: 27px !important;
        min-height: 16px !important;
        display: inline-block !important;
    }

    #tableAttendance .label-success {
        background-color: #47824a;
    }

    #tableAttdLec tr th {
        text-align: center;
        background: #607d8b;
        color: #FFFFFF;
    }

    #tableAttdStd tr th {
        text-align: center;
        background: #576f58;
        color: #FFFFFF;
    }

    #tableAttendance .btn-sm {
        padding: 0px 9px;
    }

    #tableLecAttd tr td:first-child {
        text-align: center;
    }

    #tableSecAttd tr th {
        text-align: center;
        background: #8e5d59;
        color: #FFFFFF;
    }
</style>

<div class="row">
    <div class="col-md-12">
        <div class="well">
            <div class="row">
                <div class="col-xs-2">
                    <select class="form-control filterAttd" id="filterProgramCampus"></select>
                </div>
                <div class="col-xs-2">
                    <select class="form-control filterAttd" id="filterSemester"></select>
                </div>
                <div class="col-xs-4">
                    <select class="form-control filterAttd" id="filterBaseProdi">
                        <option value="">-- All Programme Study --</option>
                        <option disabled>------------------------------------------</option>
                    </select>
                </div>
                <div class="col-xs-2">
                    <select class="form-control filterAttd" id="filterDay">
                        <option value="">-- Show All Day --</option>
                        <option disabled>-------------------</option>
                    </select>
                </div>

                <div class="col-xs-2">
                    <button class="btn btn-block btn-default btn-default-primary">Blast</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div id="loadTable"></div>
        <!-- <table id="example" class="table table-bordered" width="100%">
        </table> -->
    </div>
</div>


<script>
    $(document).ready(function() {

        setLoadFullPage();

        loadSelectOptionProgramCampus('#filterProgramCampus', '');
        loSelectOptionSemester('#filterSemester', '');
        loadSelectOptionBaseProdi('#filterBaseProdi', '');
        fillDays('#filterDay', 'Eng', '');

        var loadFirst = setInterval(function() {

            var filterProgramCampus = $('#filterProgramCampus').val();
            var filterSemester = $('#filterSemester').val();

            if (filterProgramCampus != '' && filterProgramCampus != null &&
                filterSemester != '' && filterSemester != null) {
                loadAttendance();
                clearInterval(loadFirst);
            }

        }, 1000);

    });

    $('.filterAttd').change(function() {
        loadAttendance();
    });

    function loadAttendance() {

        $('#loadTable').html('<table class="table table-bordered table-hover" id="tableAttendance"></table>');

        var filterProgramCampus = $('#filterProgramCampus').val();
        var filterSemester = $('#filterSemester').val();


        if (filterProgramCampus != '' && filterProgramCampus != null &&
            filterSemester != '' && filterSemester != null) {

            var ProgramsCampusID = filterProgramCampus;
            var SemesterID = filterSemester.split('.')[0];

            var filterBaseProdi = $('#filterBaseProdi').val();
            var DayID = $('#filterDay').val();

            var ProdiID = (filterBaseProdi != '' && filterBaseProdi != null) ? filterBaseProdi.split('.')[0] : '';

            var data = {
                ProgramsCampusID: ProgramsCampusID,
                SemesterID: SemesterID,
                ProdiID: ProdiID,
                DayID: DayID
            };

            var token = jwt_encode(data, 'UAP)(*');

            var dataTable = $('#tableAttendance').DataTable({
                "processing": true,
                "serverSide": true,
                "iDisplayLength": 25,
                "ordering": false,
                "language": {
                    "searchPlaceholder": "Group, Code, Course"
                },
                columns: [{
                        name: 'no',
                        title: 'No',
                        width: '3%'
                    },
                    {
                        name: 'course',
                        title: 'Course',
                        width: '30%'
                    },
                    {
                        name: 'group',
                        title: 'Group',
                        width: '6%'

                    },
                    {
                        name: 'credit',
                        title: 'Crdt',
                        width: '1%'
                    },
                    {
                        name: 'std',
                        title: 'Std',
                        width: '3%'
                    },
                    {
                        title: 'Lecturer',
                        width: '20%'
                    },
                    {
                        title: 'Schedule',
                        width: '11%'
                    },
                    {
                        title: '1'
                    },
                    {
                        title: '2'
                    },
                    {
                        title: '3'
                    },
                    {
                        title: '4'
                    },
                    {
                        title: '5'
                    },
                    {
                        title: '6'
                    },
                    {
                        title: '7'
                    },
                    {
                        title: '8'
                    },
                    {
                        title: '9'
                    },
                    {
                        title: '10'
                    },
                    {
                        title: '11'
                    },
                    {
                        title: '12'
                    },
                    {
                        title: '13'
                    },
                    {
                        title: '14'
                    },
                    {
                        title: '15'
                    },
                    {
                        title: '16'
                    }
                ],

                rowsGroup: ['no:name', 'course:name', 'group:name', 'credit:name', 'std:name'],

                "responsive": true,
                "ajax": {
                    url: base_url_js + 'api2/__getMonitoringAttendance', // json datasource
                    ordering: false,
                    data: {
                        token: token
                    },
                    type: "post", // method  , by default get
                    error: function() { // error handling
                        $(".employee-grid-error").html("");
                        $("#employee-grid").append('<tbody class="employee-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
                        $("#employee-grid_processing").css("display", "none");
                    },
                    complete: function() {
                        $('.img-fitter').imgFitter({

                            // CSS background position
                            backgroundPosition: 'center center',

                            // for image loading effect
                            fadeinDelay: 400,
                            fadeinTime: 1200

                        });
                    }
                }
            });

        }



    }
</script>