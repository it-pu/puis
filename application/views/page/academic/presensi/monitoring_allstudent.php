<style>
    #tableAttd thead tr th {
        text-align: center;
        background-color: #436888;
        color: #ffffff;
    }
</style>

<div class="row">
    <div class="col-md-8 col-md-offset-2">
        <div class="well">
            <div class="row">
                <div class="col-xs-3">
                    <select class="form-control filterMonitoring" id="filterSemester"></select>
                </div>
                <div class="col-xs-3">
                    <select class="form-control filterMonitoring" id="filterCurriculum">
                        <option value="">-- All Curriculum --</option>
                        <option disabled>-----------------------------</option>
                    </select>
                </div>
                <div class="col-xs-4">
                    <select class="form-control filterMonitoring" id="filterProgrammeStudy">
                        <option value="">-- All Programme Study --</option>
                        <option disabled>-----------------------------</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <div class="input-group">
                        <span class="input-group-addon">&le;</span>
                        <input type="number" id="formPercentage" class="form-control" placeholder="Under" value="100">
                        <span class="input-group-addon" id="basic-addon2">%</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <hr />
        <div id="divTableAttd"></div>
    </div>
</div>


<script>
    $(document).ready(function() {
        setLoadFullPage();

        loSelectOptionSemester('#filterSemester', '');
        loadSelectOptionCurriculumASC('#filterCurriculum', '');
        loadSelectOptionBaseProdi('#filterProgrammeStudy', '');

        var firsLoad = setInterval(function() {

            var filterSemester = $('#filterSemester').val();
            var formPercentage = $('#formPercentage').val();

            if (filterSemester != '' && filterSemester != null &&
                formPercentage != '' && formPercentage != null) {
                loadDataStudent();
                clearInterval(firsLoad);
            }

        }, 1000);

    });

    $('.filterMonitoring,#formPercentage').change(function() {
        loadDataStudent();
    });

    $('#formPercentage').keyup(function() {
        loadDataStudent();
    });
    $('#formPercentage').blur(function() {
        loadDataStudent();
    });

    function loadDataStudent() {

        // $('#divTableAttd').html('');

        var filterSemester = $('#filterSemester').val();
        var formPercentage = $('#formPercentage').val();

        if (filterSemester != '' && filterSemester != null &&
            formPercentage != '' && formPercentage != null) {

            var filterCurriculum = $('#filterCurriculum').val();
            var Year = (filterCurriculum != '' && filterCurriculum != null) ? filterCurriculum.split('.')[1] : '';

            var filterProgrammeStudy = $('#filterProgrammeStudy').val();
            var ProdiID = (filterProgrammeStudy != '' && filterProgrammeStudy != null) ? filterProgrammeStudy.split('.')[0] : '';
            var SemesterID = filterSemester.split('.')[0];

            $('#divTableAttd').html('<table class="table table-bordered" id="tableAttd">' +
                '            <thead>' +
                '            <tr>' +
                '                <th style="width: 1%;">No</th>' +
                '                <th style="width: 7%;">NIM</th>' +
                '                <th style="width: 20%;">Student</th>' +
                '                <th>Course</th>' +
                '            </tr>' +
                '            </thead>' +
                '        </table>');

            var data = {
                SemesterID: SemesterID,
                Year: Year,
                ProdiID: ProdiID,
                Percentage: formPercentage
            };

            var token = jwt_encode(data, 'UAP)(*');
            var url = base_url_js + 'api/__getMonitoringAllStudent';

            var dataTable = $('#tableAttd').DataTable({
                "processing": true,
                "serverSide": true,
                "iDisplayLength": 10,
                "ordering": false,
                "language": {
                    "searchPlaceholder": "NIM , Name Student"
                },
                "ajax": {
                    url: url, // json datasource
                    data: {
                        token: token
                    },
                    ordering: false,
                    type: "post", // method  , by default get
                    error: function() { // error handling
                        $(".employee-grid-error").html("");
                        $("#employee-grid").append('<tbody class="employee-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
                        $("#employee-grid_processing").css("display", "none");
                    }
                }
            });






        }
    }
</script>