<!--=== CSS ===-->

<!-- Bootstrap -->
<link href="<?php echo base_url('assets/template/bootstrap/css/bootstrap.min.css'); ?>" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url('assets/custom/custom.css'); ?>" rel="stylesheet" type="text/css" />

<!-- jQuery UI -->
<!--<link href="plugins/jquery-ui/jquery-ui-1.10.2.custom.css" rel="stylesheet" type="text/css" />-->
<!--[if lt IE 9]>
<link rel="stylesheet" type="text/css" href="plugins/jquery-ui/jquery.ui.1.10.2.ie.css"/>
<![endif]-->

<!-- Theme -->
<link href="<?php echo base_url('assets/template/css/main.css'); ?>" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url('assets/template/css/plugins.css'); ?>" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url('assets/template/css/responsive.css'); ?>" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url('assets/template/css/icons.css'); ?>" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url('assets/template/plugins/animate/animate.css'); ?>" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url('assets/template/plugins/toastr/toastr.min.css'); ?>" rel="stylesheet" type="text/css" />

<link rel="stylesheet" href="<?php echo base_url('assets/template/css/fontawesome/font-awesome.min.css'); ?>">
<link rel="stylesheet" href="<?php echo base_url('assets/template/css/fontawesome4/css/font-awesome.min.css'); ?>">
<!--<link rel="stylesheet" href="--><?php //echo base_url('assets/plugins/datetimepicker/css/bootstrap-datetimepicker.min.css'); 
                                    ?>
<!--">-->

<link rel="stylesheet" href="<?php echo base_url('assets/plugins/bootstrap-toggle/css/bootstrap-toggle.min.css'); ?>">
<!--[if IE 7]>
<link rel="stylesheet" href="assets/css/fontawesome/font-awesome-ie7.min.css">
<![endif]-->

<!--[if IE 8]>
<link href="assets/css/ie8.css" rel="stylesheet" type="text/css" />
<![endif]-->


<link rel="stylesheet" href="<?php echo base_url('assets/datepicter/css/bootstrap-datetimepicker.min.css'); ?>">

<!-- Check Box -->
<link rel="stylesheet" href="<?php echo base_url('assets/checkbox/checkbox.css'); ?>">

<link rel="stylesheet" href="<?php echo base_url('assets/summernote/summernote.css'); ?>">

<link rel="stylesheet" href="<?php echo base_url('assets/font/custom-font.css'); ?>">

<style media="screen">
    .project-switcher {
        background-color: #0f1f4b;
    }

    .dropdown-menu li a:hover {
        background: #0f1f4b;
    }

    #ui-datepicker-div {
        z-index: 1041 !important;
    }

    #sidebar ul#nav ul.sub-menu li.current a {
        color: #ffffff;
        background: #083f8882;
    }

    #sidebar ul#nav ul.sub-menu li.current a i {
        color: #ffffff;
    }

    #sidebar ul#nav>li.current {
        background: #083f8894;
    }

    #sidebar ul#nav>li.current>a {
        border-right: 10px solid #b30011;
    }

    #sidebar ul#nav>li.current>a,
    #sidebar ul#nav>li.current>a>.fa {
        color: #ffffff;
        text-shadow: none;
    }

    #sidebar ul#nav li a:hover {
        background: #083f8814;
    }

    .theme-dark #content {
        background-color: #ffffff;
    }

    /*.dropdown-menu {*/
    /*min-width: 100%;*/
    /*}*/

    .left-margin {
        margin-left: 5px;
    }

    .right-margin,
    .margin-right {
        margin-right: 5px;
    }

    .td-center,
    .th-center,
    .tr-center {
        text-align: center;
    }

    .head-center th {
        text-align: center;
    }


    /* Untuk datatable */
    .filter-prodi {
        width: 250px;
        float: right;
        margin-right: 10px;
    }

    h3.heading-small {
        border-left: 15px solid #ff9800;
        padding-left: 10px;
        margin-bottom: 15px;
        font-weight: bold;
    }


    /* Add by Adhi 20180702 */
    .btn-convert {
        background-color: hsl(145, 62%, 68%) !important;
        background-repeat: repeat-x;
        filter: progid:DXImageTransform.Microsoft.gradient(startColorstr="#cdf3dd", endColorstr="#7adfa4");
        /*background-image: -khtml-gradient(linear, left top, left bottom, from(#cdf3dd), to(#7adfa4));*/
        background-image: -moz-linear-gradient(top, #cdf3dd, #7adfa4);
        background-image: -ms-linear-gradient(top, #cdf3dd, #7adfa4);
        background-image: -webkit-gradient(linear, left top, left bottom, color-stop(0%, #cdf3dd), color-stop(100%, #7adfa4));
        background-image: -webkit-linear-gradient(top, #cdf3dd, #7adfa4);
        background-image: -o-linear-gradient(top, #cdf3dd, #7adfa4);
        background-image: linear-gradient(#cdf3dd, #7adfa4);
        border-color: #7adfa4 #7adfa4 hsl(145, 62%, 63%);
        color: #333 !important;
        text-shadow: 0 1px 1px rgba(255, 255, 255, 0.33);
        -webkit-font-smoothing: antialiased;
    }


    .ui-autocomplete {
        display: block;
        position: absolute;
        z-index: 1000;
        cursor: default;
        padding: 0;
        margin-top: 2px;
        list-style: none;
        background-color: #ffffff;
        border: 1px solid #ccc;
        -webkit-border-radius: 5px;
        -moz-border-radius: 5px;
        border-radius: 5px;
        -webkit-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
        -moz-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
        box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);

        width: auto !important;
        min-width: 115px !important;
    }

    .ui-autocomplete>li {
        padding: 3px 10px;
    }

    .ui-autocomplete>li.ui-menu-item a {
        /*font-weight: bold;*/
        color: #333;
        text-decoration: none;
    }

    .ui-autocomplete>li.ui-menu-item:hover {
        background: #083f88;
        color: #FFFFFF;
    }

    .ui-autocomplete>li.ui-menu-item:hover a {
        color: #FFFFFF;
    }



    .ui-helper-hidden-accessible {
        display: block;
    }


    .table-responsive {
        min-height: .01%;
        overflow-x: auto;
    }

    .daterangepicker .ranges {
        width: 250px;
    }

    .daterangepicker .ranges .input-mini {
        width: 110px !important;
    }

    .toast-top-right {
        top: 50% !important;
        right: 50% !important;
        left: 50% !important;
        bottom: 50% !important;
    }

    .panel-primary>.panel-heading {
        border-radius: 0px;
    }
</style>

<!--=== JavaScript ===-->
<script type="text/javascript" src="<?php echo base_url('assets/template/js/libs/jquery-1.10.2.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('assets/template/plugins/jquery-ui/jquery-ui-1.10.2.custom.min.js'); ?>"></script>

<script type="text/javascript" src="<?php echo base_url('assets/template/bootstrap/js/bootstrap.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('assets/template/js/libs/lodash.compat.min.js'); ?>"></script>

<!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
<!--[if lt IE 9]>
<script src="assets/js/libs/html5shiv.js"></script>
<![endif]-->

<!-- Smartphone Touch Events -->
<script type="text/javascript" src="<?php echo base_url('assets/template/'); ?>plugins/touchpunch/jquery.ui.touch-punch.min.js"></script>
<script type="text/javascript" src="<?php echo base_url('assets/template/'); ?>plugins/event.swipe/jquery.event.move.js"></script>
<script type="text/javascript" src="<?php echo base_url('assets/template/'); ?>plugins/event.swipe/jquery.event.swipe.js"></script>

<!-- General -->
<script type="text/javascript" src="<?php echo base_url('assets/template/'); ?>js/libs/breakpoints.js"></script>
<script type="text/javascript" src="<?php echo base_url('assets/template/'); ?>plugins/respond/respond.min.js"></script> <!-- Polyfill for min/max-width CSS3 Media Queries (only for IE8) -->
<script type="text/javascript" src="<?php echo base_url('assets/template/'); ?>plugins/cookie/jquery.cookie.min.js"></script>
<script type="text/javascript" src="<?php echo base_url('assets/template/'); ?>plugins/slimscroll/jquery.slimscroll.min.js"></script>
<script type="text/javascript" src="<?php echo base_url('assets/template/'); ?>plugins/slimscroll/jquery.slimscroll.horizontal.min.js"></script>
<script type="text/javascript" src="<?php echo base_url('assets/template/'); ?>plugins/toastr/toastr.min.js"></script>

<!-- Page specific plugins -->
<!-- Charts -->
<!--[if lt IE 9]>
<script type="text/javascript" src="plugins/flot/excanvas.min.js"></script>
<![endif]-->
<script type="text/javascript" src="<?php echo base_url('assets/template/'); ?>plugins/sparkline/jquery.sparkline.min.js"></script>
<!-- <script type="text/javascript" src="<?php echo base_url('assets/template/'); ?>plugins/flot/jquery.flot.min.js"></script>
<script type="text/javascript" src="<?php echo base_url('assets/template/'); ?>plugins/flot/jquery.flot.tooltip.min.js"></script>
<script type="text/javascript" src="<?php echo base_url('assets/template/'); ?>plugins/flot/jquery.flot.resize.min.js"></script>
<script type="text/javascript" src="<?php echo base_url('assets/template/'); ?>plugins/flot/jquery.flot.time.min.js"></script>
<script type="text/javascript" src="<?php echo base_url('assets/template/'); ?>plugins/flot/jquery.flot.growraf.min.js"></script>
<script type="text/javascript" src="<?php echo base_url('assets/template/'); ?>plugins/easy-pie-chart/jquery.easy-pie-chart.min.js"></script> -->

<script type="text/javascript" src="<?php echo base_url('assets/template/'); ?>plugins/daterangepicker/moment.min.js"></script>
<script type="text/javascript" src="<?php echo base_url('assets/moment-range/'); ?>moment-range.js"></script>
<!--<script type="text/javascript" src="--><?php //echo base_url('assets/template/'); 
                                            ?>
<!--plugins/daterangepicker/moment_id.js"></script>-->
<script type="text/javascript" src="<?php echo base_url('assets/template/'); ?>plugins/daterangepicker/daterangepicker.js"></script>
<script type="text/javascript" src="<?php echo base_url('assets/template/'); ?>plugins/blockui/jquery.blockUI.min.js"></script>

<script type="text/javascript" src="<?php echo base_url('assets/template/'); ?>plugins/fullcalendar/fullcalendar.min.js"></script>

<!-- Noty -->
<!--<script type="text/javascript" src="--><?php //echo base_url('assets/template/'); 
                                            ?>
<!--plugins/noty/jquery.noty.js"></script>-->
<!--<script type="text/javascript" src="--><?php //echo base_url('assets/template/'); 
                                            ?>
<!--plugins/noty/layouts/top.js"></script>-->
<!--<script type="text/javascript" src="--><?php //echo base_url('assets/template/'); 
                                            ?>
<!--plugins/noty/themes/default.js"></script>-->

<!-- DataTables -->
<!-- DataTables -->

<!--<script type="text/javascript" src="--><?php //echo base_url('assets/template/'); 
                                            ?>
<!--plugins/datatables/jquery.dataTables.min.js"></script>-->
<script type="text/javascript" src="<?php echo base_url('assets/template/'); ?>plugins/datatables/tes/jquery.dataTables.js"></script>
<!--<script type="text/javascript" src="--><?php //echo base_url('assets/template/'); 
                                            ?>
<!--plugins/datatables/tes/dataTables.bootstrap.min.js"></script>-->
<script type="text/javascript" src="<?php echo base_url('assets/template/'); ?>plugins/datatables/tabletools/TableTools.min.js"></script> <!-- optional -->
<script type="text/javascript" src="<?php echo base_url('assets/template/'); ?>plugins/datatables/colvis/ColVis.min.js"></script> <!-- optional -->
<script type="text/javascript" src="<?php echo base_url('assets/template/'); ?>plugins/datatables/DT_bootstrap.js"></script>
<!--<script type="text/javascript" src="--><?php //echo base_url('assets/template/'); 
                                            ?>
<!--plugins/datatables/dataTables.rowReorder.js"></script>-->

<!-- Plugin DataTbales -->
<script type="text/javascript" src="<?php echo base_url('assets/datatables/dataTables.rowsGroup.js'); ?>"></script>

<!-- Forms -->
<script type="text/javascript" src="<?php echo base_url('assets/template/'); ?>plugins/uniform/jquery.uniform.min.js"></script>
<script type="text/javascript" src="<?php echo base_url('assets/template/'); ?>plugins/select2/select2.min.js"></script>

<!-- Select 2 -->
<!--<link href="--><?php //echo base_url('assets/select2/css/select2.css'); 
                    ?>
<!--" rel="stylesheet" />-->
<!--<link href="--><?php //echo base_url('assets/select2/select2-bootstrap.min.css'); 
                    ?>
<!--" rel="stylesheet" />-->
<!--<style>-->
<!--    .select2-container--bootstrap .select2-results__group{-->
<!--        background: #efefef;-->
<!--    }-->
<!--    .select2-container--bootstrap .select2-selection {-->
<!--        border-radius: 0px;-->
<!--        -webkit-box-shadow: inset 0 1px 1px rgba(0,0,0,.075);-->
<!--        box-shadow: inset 0 1px 1px rgba(0,0,0,.075);-->
<!---->
<!--    }-->
<!--    .select2-dropdown--below , .select2-container--bootstrap {-->
<!--        min-width: 300px;-->
<!--    }-->
<!--</style>-->
<!--<script  type="text/javascript" charset="UTF-8" src="--><?php //echo base_url('assets/select2/js/select2.js') 
                                                            ?>
<!--"></script>-->
<!--<script>$.fn.select2.defaults.set( "theme", "bootstrap" );</script>-->

<!-- Pickers -->
<script type="text/javascript" src="<?php echo base_url('assets/template/'); ?>plugins/pickadate/picker.js"></script>
<script type="text/javascript" src="<?php echo base_url('assets/template/'); ?>plugins/pickadate/picker.date.js"></script>
<script type="text/javascript" src="<?php echo base_url('assets/template/'); ?>plugins/pickadate/picker.time.js"></script>
<script type="text/javascript" src="<?php echo base_url('assets/template/'); ?>plugins/bootstrap-colorpicker/bootstrap-colorpicker.min.js"></script>

<!-- App -->
<script type="text/javascript" src="<?php echo base_url('assets/template/'); ?>js/app.js"></script>
<script type="text/javascript" src="<?php echo base_url('assets/template/'); ?>js/plugins.js"></script>
<script type="text/javascript" src="<?php echo base_url('assets/template/'); ?>js/plugins.form-components.js"></script>

<!-- Dual Box -->
<script type="text/javascript" src="<?php echo base_url('assets/template/'); ?>plugins/duallistbox/jquery.duallistbox.min.js"></script>


<!-- Form Validation -->
<script type="text/javascript" src="<?php echo base_url('assets/template/'); ?>plugins/validation/jquery.validate.min.js"></script>

<!-- IMG Fitter -->
<script type="text/javascript" src="<?php echo base_url('assets/'); ?>plugins/img-fitter/jquery.imgFitter.js"></script>



<!-- Demo JS -->
<script type="text/javascript" src="<?php echo base_url('assets/template/'); ?>js/custom.js"></script>
<script type="text/javascript" src="<?php echo base_url('assets/template/'); ?>js/demo/pages_calendar.js"></script>
<script type="text/javascript" src="<?php echo base_url('assets/template/'); ?>js/demo/form_components.js"></script>
<!-- <script type="text/javascript" src="<?php echo base_url('assets/template/'); ?>js/demo/charts/chart_filled_blue.js"></script> -->
<!-- <script type="text/javascript" src="<?php echo base_url('assets/template/'); ?>js/demo/charts/chart_simple.js"></script> -->

<!-- JWT Encode -->
<script type="text/javascript" src="<?php echo base_url('assets/plugins/'); ?>jwt/encode/hmac-sha256.js"></script>
<script type="text/javascript" src="<?php echo base_url('assets/plugins/'); ?>jwt/encode/enc-base64-min.js"></script>
<script type="text/javascript" src="<?php echo base_url('assets/plugins/'); ?>jwt/encode/jwt.encode.js"></script>

<!-- JWT Decode -->
<script type="text/javascript" src="<?php echo base_url('assets/plugins/'); ?>jwt/decode/build/jwt-decode.min.js"></script>

<!-- <script type="text/javascript" src="<?php echo base_url('assets/plugins/datetimepicker/js/bootstrap-datetimepicker.min.js'); ?>"></script> -->

<script type="text/javascript" src="<?php echo base_url('assets/inputmask/jquery.inputmask.bundle.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('assets/datepicter/js/bootstrap-datetimepicker.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('assets/plugins/bootstrap-toggle/js/bootstrap-toggle.min.js'); ?>"></script>

<!-- Countdown -->
<script type="text/javascript" src="<?php echo base_url('assets/'); ?>countdown/jquery.countdown.min.js"></script>

<!-- BootBox -->
<script type="text/javascript" src="<?php echo base_url('assets/'); ?>bootbox/bootbox.min.js"></script>

<script type="text/javascript" src="<?php echo base_url('assets/summernote/summernote.js'); ?>"></script>

<!-- Socket js -->
<script type="text/javascript" src="<?php echo base_url('node_modules/socket.io/node_modules/socket.io-client/socket.io.js'); ?>"></script>

<script type="text/javascript" src="<?php echo base_url(); ?>assets/custom/jquery.maskMoney.js"></script>

<!-- Jquery Excel -->
<script type="text/javascript" src="<?php echo base_url(); ?>assets/jquerytable2excel/jquery.table2excel.js"></script>
<!--<script type="text/javascript" src="--><?php //echo base_url();
                                            ?>
<!--assets/jquerytable2excel/table2excel.js"></script>-->

<!-- Custom -->
<script type="text/javascript">
    window.base_url_js = "<?php echo base_url(); ?>";
    window.base_url_files = "<?= url_files; ?>";
    window.url_rest_server = "<?= url_rest_server; ?>";
    //window.base_url_js = "<?php //echo 'http://10.1.10.27:8080/siak3/'; 
                            ?>//";
    window.base_url_img_employee = "<?php echo base_url('uploads/employees/'); ?>";
    window.base_url_img_student = "<?php echo base_url('uploads/students/'); ?>";
    window.sessionNIP = "<?php echo $this->session->userdata('NIP'); ?>";
    window.sessionName = "<?php echo $this->session->userdata('Name'); ?>";
    window.sessionIDdepartementNavigation = "<?php echo $this->session->userdata('IDdepartementNavigation'); ?>";
    window.timePerCredits = "<?php echo $this->session->userdata('timePerCredits'); ?>";

    window.sessionUrlPhoto = "<?php echo $imgProfile = (file_exists('./uploads/employees/' . $this->session->userdata('Photo')))
                                    ?  url_pas . 'uploads/employees/' . $this->session->userdata('Photo')
                                    : url_pas . 'images/icon/no_image.png'; ?>";

    window.base_url_sign_out = "<?php echo url_sign_out ?>";
    window.base_url_portal_students = "<?php echo url_sign_in_students ?>";
    window.base_url_portal_lecturers = "<?php echo url_sign_in_lecturers ?>";

    window.allowDepartementNavigation = [];

    window.daysEng = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
    window.daysInd = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];

    window.DummyEmail = 'nandang.mulyadi@podomorouniversity.ac.id';

    window.timeOption = {
        format: 'hh:ii',
        weekStart: 1,
        todayBtn: 0,
        autoclose: 1,
        todayHighlight: 0,
        startView: 1,
        minView: 0,
        maxView: 1,
        forceParse: 1
    };

    <?php $PositionMain = $this->session->userdata('PositionMain'); ?>
    window.PositionMain = <?php echo json_encode($PositionMain) ?>;
    $(document).ready(function() {
        // "use strict";


        App.init(); // Init layout and core plugins
        Plugins.init(); // Init all plugins
        FormComponents.init(); // Init all form-specific plugins

        $('.img-fitter').imgFitter({
            // CSS background position
            backgroundPosition: 'center center',
            // for image loading effect
            fadeinDelay: 400,
            fadeinTime: 1200
        });

    });

    $(document).on('click', '.btnActionLogOut', function() {
        var url = base_url_js + "auth/logMeOut";
        loading_page('#NotificationModal .modal-body');
        $.post(url, function(result) {
            setTimeout(function() {
                window.location.href = base_url_sign_out;
            }, 500);
        });
    });

    function load_navigation() {
        localStorage.getItem('departement');
    }


    $.fn.extend({
        animateCss: function(animationName, callback) {
            var animationEnd = (function(el) {
                var animations = {
                    animation: 'animationend',
                    OAnimation: 'oAnimationEnd',
                    MozAnimation: 'mozAnimationEnd',
                    WebkitAnimation: 'webkitAnimationEnd',
                };

                for (var t in animations) {
                    if (el.style[t] !== undefined) {
                        return animations[t];
                    }
                }
            })(document.createElement('div'));

            this.addClass('animated ' + animationName).one(animationEnd, function() {
                $(this).removeClass('animated ' + animationName);

                if (typeof callback === 'function') callback();
            });

            return this;
        },
    });

    function loadCoutDown(element, EndSessions, Refreshpage) {

        var ens = EndSessions.split(':');
        var start = moment();
        var end = moment().hours(ens[0]).minutes(ens[1]).seconds(ens[2]);

        var en = moment().valueOf();
        var d = end.diff(start);
        var fiveSeconds = parseInt(en) + parseInt(d);


        $(element)
            .countdown(fiveSeconds, function(event) {
                $(this).text(
                    // event.strftime('%D days %H:%M:%S')
                    event.strftime('%H:%M:%S')
                );
            })
            .on('finish.countdown', function() {

                alert('Time has run out');

                if (Refreshpage == 1) {
                    window.location.href = "";
                }

            });
    }

    toastr.options = {
        "closeButton": true,
        "debug": false,
        "newestOnTop": true,
        "progressBar": false,
        "positionClass": "toast-top-right",
        "preventDuplicates": true,
        "onclick": null,
        "showDuration": "300",
        "hideDuration": "1000",
        "timeOut": "5000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    };

    function clearDotMaskMoney(dataPrice) {
        var Price = '';
        var arrP = dataPrice.split('.');
        if (arrP.length > 0) {
            for (var i = 0; i < arrP.length; i++) {
                Price = Price + '' + arrP[i];
            }
        }

        return parseFloat(Price);
    }

    function loading_page(element) {
        $(element).html('<div class="row">' +
            '<div class="col-md-12" style="text-align: center;">' +
            '<h3 class="animated flipInX"><i class="fa fa-circle-o-notch fa-spin fa-fw"></i> <span>Loading page . . .</span></h3>' +
            '</div>' +
            '</div>');
    }

    function loading_page2(selector) {
        selector.html('<div class="row">' +
            '<div class="col-md-12" style="text-align: center;">' +
            '<h3 class="animated flipInX"><i class="fa fa-circle-o-notch fa-spin fa-fw"></i> <span>Loading page . . .</span></h3>' +
            '</div>' +
            '</div>');
    }

    function loading_anytext(element, text) {
        $(element).html('<div class="row">' +
            '<div class="col-md-12" style="text-align: center;">' +
            '<h3 class="animated flipInX"><i class="fa fa-circle-o-notch fa-spin fa-fw"></i> <span>' + text + ' . . .</span></h3>' +
            '</div>' +
            '</div>');
    }

    function loading_page_simple(element, position) {
        var arrp = ['center', 'left', 'right'];
        var p = (typeof position !== 'undefined' && position != '' && position != null && $.inArray(position, arrp) != -1) ?
            'text-align:' + position + ';' :
            '';
        $(element).html('<div style="margin-top: 1px;' + p + '">' +
            '<h5 class="animated flipInX"><i class="fa fa-circle-o-notch fa-spin fa-fw"></i> <span>Loading page . . .</span></h5>' + '</div>');
    }

    function loading_text(element) {
        $(element).html('<i class="fa fa-circle-o-notch fa-spin fa-fw"></i> Loading...');
    }

    function loading_button(element) {
        $('' + element).html('<i class="fa fa-refresh fa-spin fa-fw right-margin"></i> Loading...');
        $('' + element).prop('disabled', true);
    }

    function loading_button2(selector) {
        selector.html('<i class="fa fa-refresh fa-spin fa-fw right-margin"></i> Loading...');
        selector.prop('disabled', true);
    }

    function end_loading_button2(selector, html = 'Save') {
        selector.prop('disabled', false).html(html);
    }

    function loading_buttonSm(element) {
        $('' + element).html('<i class="fa fa-refresh fa-spin fa-fw"></i>');
        $('' + element).prop('disabled', true);
    }

    function loading_modal_show() {
        $('#NotificationModal .modal-header').addClass('hide');
        $('#NotificationModal .modal-body').html('<center>' +
            '                    <i class="fa fa-refresh fa-spin fa-3x fa-fw"></i>' +
            '                    <br/>' +
            '                    Loading Data . . .' +
            '                </center>');
        $('#NotificationModal .modal-footer').addClass('hide');
        $('#NotificationModal').modal({
            'backdrop': 'static',
            'show': true
        });
    }

    function loading_modal_hide() {
        $('#NotificationModal').modal('hide');
    }

    function loading_data(element) {
        $('' + element).html('<i class="fa fa-refresh fa-spin fa-fw"></i> please, waiting');
    }

    function convertDateMMtomm(mounth) {
        var arr_mounth = {
            'January': 0,
            'February': 1,
            'March': 2,
            'April': 3,
            'May': 4,
            'June': 5,
            'July': 6,
            'August': 7,
            'September': 8,
            'October': 9,
            'November': 10,
            'December': 11
        };

        return arr_mounth[mounth];
    }

    function dateTimeNow() {
        var phpNowDateTime = '<?= date("Y-m-d H:i:s"); ?>';
        // return moment().format('YYYY-MM-DD HH:mm:ss');
        return phpNowDateTime;
    }

    function getDateTimeNow() {
        var phpNowDateTime = '<?= date("Y-m-d H:i:s"); ?>';
        // return moment().format('YYYY-MM-DD HH:mm:ss');
        return phpNowDateTime;
    }

    function getDateNow() {
        var phpNowDateTime = '<?= date("Y-m-d"); ?>';
        // return moment().format('YYYY-MM-DD HH:mm:ss');
        return phpNowDateTime;
    }

    function getTimeNow() {
        var phpNowTime = '<?= date("H:i:s"); ?>';
        // return moment().format('YYYY-MM-DD HH:mm:ss');
        return phpNowTime;
    }

    function ucwords(str) {
        return str.toLowerCase().replace(/\b[a-z]/g, function(letter) {
            return letter.toUpperCase();
        });
    }

    function log(data) {
        console.log(data);
    }

    function genCharArray() {
        var charA = 'A',
            charZ = 'Z';
        var a = [],
            i = charA.charCodeAt(0),
            j = charZ.charCodeAt(0);
        for (; i <= j; ++i) {
            a.push(String.fromCharCode(i));
        }
        return a;
    }

    function loadSelectOptionSemester(element, selected) {

        var token = jwt_encode({
            action: 'read'
        }, 'UAP)(*');
        var url = base_url_js + 'api/__crudTahunAkademik';
        $.post(url, {
            token: token
        }, function(jsonResult) {

            if (jsonResult.length > 0) {
                for (var i = 0; i < jsonResult.length; i++) {
                    var dt = jsonResult[i];
                    var sc = (selected == dt.ID) ? 'selected' : '';
                    // var v = (option=="Name") ? dt.Name : dt.ID;
                    $(element).append('<option value="' + dt.ID + '.' + dt.Name + '" ' + sc + '>' + dt.Name + '</option>');
                }
            }
        });

    }

    function loadSelectOptionSemester_admission(element, selected) {

        var token = jwt_encode({
            action: 'read'
        }, 'UAP)(*');
        var url = base_url_js + 'api/__crudTahunAkademik';
        $.post(url, {
            token: token
        }, function(jsonResult) {

            if (jsonResult.length > 0) {
                for (var i = 0; i < jsonResult.length; i++) {
                    var dt = jsonResult[i];
                    var sc = (selected == dt.Year && dt.Code == 1) ? 'selected' : '';
                    // var v = (option=="Name") ? dt.Name : dt.ID;
                    $(element).append('<option value="' + dt.ID + '.' + dt.Name + '" ' + sc + '>' + dt.Name + '</option>');
                }
            }
        });

    }

    function loadSelectOptionProgramCampus(element, selected) {
        var url = base_url_js + 'api/__crudProgramCampus';
        var token = jwt_encode({
            action: 'read'
        }, 'UAP)(*');
        $.post(url, {
            token: token
        }, function(data_json) {
            if (data_json.length > 0) {
                var option = $(element);
                for (var i = 0; i < data_json.length; i++) {
                    selected = (selected == data_json[i].ID) ? 'selected' : '';
                    option.append('<option value="' + data_json[i].ID + '" ' + selected + '>' + data_json[i].Name + '</option>');
                }
            }
        });
    }

    function loadSelectOptionBaseProdi(element, selected, filtered = [], show = 1) {
        var url = base_url_js + "api/__getBaseProdiSelectOption";
        $.get(url, function(data) {
            for (var i = 0; i < data.length; i++) {
                var selc = (data[i].ID == selected) ? 'selected' : '';
                if (filtered.length == 0) {
                    if (show == 1) {
                        $('' + element).append('<option value="' + data[i].ID + '.' + data[i].Code + '" ' + selc + '>' + data[i].Level + ' - ' + data[i].NameEng + '</option>');
                    } else {
                        /*
                            for auth APS
                        */
                    }

                } else {
                    for (var k = 0; k < filtered.length; k++) {
                        var ex = filtered[k].split('.');
                        if (ex[0] == data[i].ID) {
                            $('' + element).append('<option value="' + data[i].ID + '.' + data[i].Code + '" ' + selc + '>' + data[i].Level + ' - ' + data[i].NameEng + '</option>');
                        }
                    }
                }

            }
        });
    }

    function loadSelectOptionJudiciumsYear(element, selected) {
        var url = base_url_js + 'api3/__crudYudisium';
        var token = jwt_encode({
            action: 'getJudiciumsYear'
        }, 'UAP)(*');

        $.post(url, {
            token: token
        }, function(jsonResult) {

            // console.log(jsonResult);

            if (jsonResult.length > 0) {
                $.each(jsonResult, function(i, v) {
                    var sc = (v.ID == selected) ? 'selected' : '';
                    $(element).append('<option value="' + v.ID + '" ' + sc + '>' + v.Title + '</option>');

                });
            }

        });

    }

    function loadSelectOptionLecturerStatus(element, selected) {

        var url = base_url_js + 'api/__crudLecturer';
        var token = jwt_encode({
            action: 'getLecturerStatus'
        }, 'UAP)(*');

        $.post(url, {
            token: token
        }, function(jsonResult) {

            if (jsonResult.length > 0) {
                $.each(jsonResult, function(i, v) {

                    var sc = (selected == v.IDStatus) ? 'selected' : '';
                    var color = (parseInt(v.IDStatus) < 0) ?
                        'style="color:red;"' :
                        '';
                    $(element).append('<option value="' + v.IDStatus + '" ' + color + ' ' + sc + '>' + v.Description + '</option>');

                });
            }

        });

    }

    function loadSelectOptionLevelEducation(element, selected) {
        var url = base_url_js + "api/__getLevelEducation";
        $.get(url, function(jsonResult) {

            $.each(jsonResult, function(i, v) {

                var sc = (selected != '' && typeof selected !== 'undefined' && parseInt(selected) == parseInt(v.ID)) ?
                    'selected' : '';
                $(element).append('<option value="' + v.ID + '" ' + sc + '>' + v.Level + ' - ' + v.Description + '</option>');
            });

        });
    }

    function loadSelectOptionLecturerAcademicPosition(element, selected) {
        var url = base_url_js + "api/__getLecturerAcademicPosition";
        $.get(url, function(jsonResult) {

            $.each(jsonResult, function(i, v) {

                var sc = (selected != '' && typeof selected !== 'undefined' && parseInt(selected) == parseInt(v.ID)) ?
                    'selected' : '';
                $(element).append('<option value="' + v.ID + '" ' + sc + '>' + v.Position + '</option>');
            });

        });
    }

    function loadSelectOptionCurriculum(element, selected) {
        var url = base_url_js + "api/__getKurikulumSelectOption";
        $.get(url, function(data_json) {
            // console.log(data_json);
            for (var i = 0; i < data_json.length; i++) {

                var sc = '';
                if (selected != '' && selected != null && typeof selected !== undefined) {
                    sc = (data_json[i].ID == selected) ? 'selected' : '';
                } else {
                    sc = (data_json[i].StatusSemester == '1') ? 'selected' : '';
                }

                $(element).append('<option value="' + data_json[i].ID + '.' + data_json[i].Year + '" ' + sc + '>' + data_json[i].NameEng + '</option>');
            }
        });
    }

    function loadSelectOptionCurriculumNoSelect(element) {
        var url = base_url_js + "api/__getKurikulumSelectOption";
        $.get(url, function(data_json) {
            // console.log(data_json);
            for (var i = 0; i < data_json.length; i++) {
                $(element).append('<option value="' + data_json[i].ID + '.' + data_json[i].Year + '">' + data_json[i].NameEng + '</option>');
            }
        });
    }



    function loadSelectOptionCompanyType(element, selected) {
        var url = base_url_js + 'api/__getCompanyType';
        $.getJSON(url, function(jsonResult) {

            if (jsonResult.length > 0) {
                var option = $(element);
                if (jsonResult.length > 0) {
                    for (var i = 0; i < jsonResult.length; i++) {
                        var data = jsonResult[i];
                        option.append('<option value="' + data.ID + '">' + data.name + '</option>')
                            .val(selected).trigger('change');
                    }
                }
            }

        });

    }

    function loadSelectOptionLoc_Province(element, selected) {
        var url = base_url_js + 'api/__getProvince';
        $.getJSON(url, function(jsonResult) {
            $.each(jsonResult, function(i, v) {

                var sc = (selected == v.ProvinceID) ? 'selected' : '';
                $(element).append('<option value="' + v.ProvinceID + '" ' + sc + '>' + v.ProvinceName + '</option>');

            })
        });
    }

    function loadSelectOptionLoc_Regions(ProvinceID, element, selected) {
        var url = base_url_js + 'api/__getRegions/' + ProvinceID;
        $.getJSON(url, function(jsonResult) {
            $.each(jsonResult, function(i, v) {

                var sc = (selected == v.RegionID) ? 'selected' : '';
                $(element).append('<option value="' + v.RegionID + '" ' + sc + '>' + v.RegionName + '</option>');

            })
        });
    }

    function loadSelectOptionLoc_District(RegionID, element, selected) {
        var url = base_url_js + 'api/__getDistric/' + RegionID;
        $.getJSON(url, function(jsonResult) {
            $.each(jsonResult, function(i, v) {

                var sc = (selected == v.DistrictID) ? 'selected' : '';
                $(element).append('<option value="' + v.DistrictID + '" ' + sc + '>' + v.DistrictName + '</option>');

            })
        });
    }

    function loadSelectOptionRangeEmployees(element, selected) {
        var url = base_url_js + 'api/__getRangeEmployees';
        $.getJSON(url, function(jsonResult) {
            $.each(jsonResult, function(i, v) {

                var sc = (selected == v.ID) ? 'selected' : '';

                var opt = v.RangeStart + ' - ' + v.RangeEnd;
                if (v.Type == '1') {
                    opt = '&#60; ' + v.RangeEnd;
                } else if (v.Type == '2') {
                    opt = v.RangeStart + ' &#62;';
                }

                $(element).append('<option value="' + v.ID + '" ' + sc + '>' + opt + '</option>');

            })
        });
    }

    function loadSelectOptionGrossRevenue(element, selected) {
        var url = base_url_js + 'api/__getGrossRevenue';
        $.getJSON(url, function(jsonResult) {
            $.each(jsonResult, function(i, v) {

                var sc = (selected == v.ID) ? 'selected' : '';

                var opt = v.RangeStart + ' - ' + v.RangeEnd;
                if (v.Type == '1') {
                    opt = '&#60; ' + v.RangeEnd;
                } else if (v.Type == '2') {
                    opt = v.RangeStart + ' &#62;';
                }

                $(element).append('<option value="' + v.ID + '" ' + sc + '>' + opt + '</option>');

            })
        });
    }


    function loadSelectOptionCurriculumForlap(element, selected) {
        var url = base_url_js + "api/__getKurikulumSelectOption";
        $.get(url, function(data_json) {
            // console.log(data_json);
            for (var i = 0; i < data_json.length; i++) {

                var sc = '';
                if (selected != '' && selected != null && typeof selected !== undefined) {
                    sc = (data_json[i].ID == selected) ? 'selected' : '';
                } else {
                    sc = (data_json[i].StatusSemester == '1') ? 'selected' : '';
                }

                var yn = parseInt(data_json[i].Year) + 1;
                $(element).append('<option value="' + data_json[i].ID + '.' + data_json[i].Year + '" ' + sc + '>Tahun ' + data_json[i].Year + ' / ' + yn + ' </option>');
            }
        });
    }

    function loadSelectOptionForce(element, selected) {
        var url = base_url_js + "api/__getKurikulumSelectOption";
        $.get(url, function(data_json) {
            // console.log(data_json);
            for (var i = 0; i < data_json.length; i++) {
                var selected = (data_json[i].ID == selected) ? 'selected' : '';
                $(element).append('<option value="' + data_json[i].ID + '.' + data_json[i].Year + '" ' + selected + '>Asc. Year - ' + data_json[i].Year + '</option>');
            }
        });
    }

    function loadSelectOptionStudentYear(element, selected) {
        var url = base_url_js + "api/__getStudentYear";
        $.get(url, function(jsonResult) {
            // console.log(data_json);

            $.each(jsonResult, function(i, v) {
                var selected = (v.Year == selected && selected != '' && selected != null &&
                    typeof selected !== 'undefined') ? 'selected' : '';
                $(element).append('<option value="' + v.Year + '" ' + selected + '>Class of ' + v.Year + '</option>');
            })
        });

    }

    function loadSelectOptionCurriculumASC(element, selected) {
        var url = base_url_js + "api/__getKurikulumSelectOptionASC";
        $.get(url, function(data_json) {
            // console.log(data_json);
            for (var i = 0; i < data_json.length; i++) {
                var selected = (data_json[i].ID == selected) ? 'selected' : '';
                $(element).append('<option value="' + data_json[i].ID + '.' + data_json[i].Year + '" ' + selected + '>' + data_json[i].NameEng + '</option>');
            }
        });
    }

    function loadSelectOptionClassOf_ASC(element, selected) {
        var url = base_url_js + "api/__getKurikulumSelectOptionASC";
        $.get(url, function(data_json) {
            // console.log(data_json);
            for (var i = 0; i < data_json.length; i++) {
                var sc = (data_json[i].ID == selected) ? 'selected' : '';
                $(element).append('<option value="' + data_json[i].ID + '.' + data_json[i].Year + '" ' + sc + '>Class of - ' + data_json[i].Year + '</option>');
            }
        });
    }

    function loadSelectOptionClassOf_DESC(element, selected, label = '') {
        var url = base_url_js + "api/__getKurikulumSelectOptionDSC";

        var viewLabel = 'Class of - ';
        if (label == 'HideLabel') {
            viewLabel = '';
        }

        $.get(url, function(data_json) {
            // console.log(data_json);
            for (var i = 0; i < data_json.length; i++) {
                var sc = (data_json[i].Year == selected) ? 'selected' : '';
                $(element).append('<option value="' + data_json[i].Year + '" ' + sc + '>' + viewLabel + data_json[i].Year + '</option>');
            }
        });
    }

    function setLoadFullPage() {
        $('.fixed-header').addClass('sidebar-closed');
        $('.list-group-item').removeClass('active-left-menu');
        $('.collapse').removeClass('in');
    }

    function loadSelectOptionClassOf_Year(element, selected) {

        var url = base_url_js + "api/__getKurikulumSelectOptionASC";
        $.get(url, function(data_json) {
            // console.log(data_json);
            for (var i = 0; i < data_json.length; i++) {
                var sc = (data_json[i].Year == selected) ? 'selected' : '';
                $(element).append('<option value="' + data_json[i].Year + '" ' + sc + '>Class of - ' + data_json[i].Year + '</option>');
            }
        });
    }

    function loadSelectOptionBaseProdiAll(element, selected) {
        var url = base_url_js + "api/__getBaseProdiSelectOptionAll";
        $.get(url, function(data) {
            for (var i = 0; i < data.length; i++) {
                var selc = (data[i].ID == selected) ? 'selected' : '';
                $('' + element).append('<option value="' + data[i].ID + '.' + data[i].Code + '" ' + selc + '>' + data[i].NameEng + '</option>');
            }
        });
    }

    function loadSelectOptionEducationLevel(element, selected) {
        var url = base_url_js + "api/__geteducationLevel";
        $.get(url, function(data) {
            for (var i = 0; i < data.length; i++) {
                var selc = (data[i].ID == selected) ? 'selected' : '';
                $('' + element).append('<option value="' + data[i].ID + '" ' + selc + '>' + data[i].Name + '</option>');
            }
        });
    }

    function loadSelectOPtionAllSemester(element, selected, SemesterID, IsSemesterAntara) {

        var url = base_url_js + 'api/__crudTahunAkademik';
        var data = {
            action: 'DataSemester',
            SemesterID: SemesterID,
            IsSemesterAntara: IsSemesterAntara
        };
        var token = jwt_encode(data, 'UAP)(*');
        $.post(url, {
            token: token
        }, function(jsonResult) {
            for (var i = 0; i < jsonResult.length; i++) {
                var color = (jsonResult[i].Semester > 8) ? 'red' : '#333';
                if (jsonResult[i].Semester <= 14) {
                    $(element).append('<option style="color: ' + color + ';" value="' + jsonResult[i].Semester + '|' + jsonResult[i].Curriculum.ID + '.' + jsonResult[i].Curriculum.Year + '|' + jsonResult[i].Curriculum.NameEng + '">Semester ' + jsonResult[i].Semester + '</option>');
                }

            }
        });

    }

    const checkboxListStatuStudent = async (selector) => {
        var url = base_url_js + 'api/__crudStatusStudents';
        var data = {
            action: 'read'
        };

        var token = jwt_encode(data, 'UAP)(*');
        loading_page2(selector);
        try {
            const fetch = await AjaxSubmitFormPromises(url, token);
            let html = '<div class = "row">';

            for (var i = 0; i < fetch.length; i++) {
                html += '<div class = "col-sm-6">' +
                    '<div class="checkbox">' +
                    '<label>' +
                    '<input type="checkbox" class="checkboxStatus"  value="' + fetch[i].ID + '">' + fetch[i].Description +
                    '</label>' +
                    '</div>' +
                    '</div>';
            }

            html += '</div>';

            selector.html(html);

        } catch (err) {
            toastr.info('something wrong, please contact IT');
        }


    }

    function loadSelectOptionStatusStudent(element, selected) {

        var url = base_url_js + 'api/__crudStatusStudents';
        var data = {
            action: 'read'
        };

        var token = jwt_encode(data, 'UAP)(*');

        $.post(url, {
            token: token
        }, function(jsonResult) {
            if (jsonResult.length > 0) {
                for (var s = 0; s < jsonResult.length; s++) {
                    var d = jsonResult[s];
                    var sc = (selected == d.ID) ? 'selected' : '';
                    $(element).append('<option value="' + d.ID + '" ' + sc + '>' + d.Description + '</option>');
                }
            }

        });
    }

    function loadSelectOptionTypeMK(element, selected) {

        var dataTypeMK = [{
                ID: '1',
                Type: 'Mandiri'
            },
            {
                ID: '2',
                Type: 'MKDU'
            },
            {
                ID: '3',
                Type: 'MKU'
            }
        ];

        for (var i = 0; i < dataTypeMK.length; i++) {
            var d = dataTypeMK[i];
            var sc = (selected != '' && typeof selected !== 'undefined' && d.ID == selected) ? 'selected' : '';
            $(element).append('<option value="' + d.ID + '" ' + sc + '>' + d.Type + '</option>');
        }

    }

    function load_SO_ProdiGroup(ProdiID, element, selected) {

        var url = base_url_js + 'api/__crudProdiGroup';
        var data = {
            action: 'readProdiGroup',
            ProdiID: ProdiID
        };
        var token = jwt_encode(data, 'UAP)(*');
        $.post(url, {
            token: token
        }, function(jsonResult) {
            if (jsonResult.length > 0) {
                for (var i = 0; i < jsonResult.length; i++) {
                    var d = jsonResult[i];
                    var sc = (typeof selected !== 'undefined' && selected != '' && d.ID == selected) ? 'selected' : '';
                    $(element).append('<option value="' + d.ID + '" ' + sc + '>' + d.Code + '</option');
                }
            }
        });

    }

    $(document).on('click', 'button[data-toggle=collapse]', function() {
        var cl = $(this).attr("class").split(" ");

        if ($.inArray('btn-danger', cl) == 1) {
            $(this).removeClass('btn-danger');
            $(this).addClass('btn-info');
            $(this).children().removeClass('fa-minus-circle');
            $(this).children().addClass('fa-plus-circle');
        } else {
            $(this).addClass('btn-danger');
            $(this).removeClass('btn-info');
            $(this).children().addClass('fa-minus-circle');
            $(this).children().removeClass('fa-plus-circle');
        }

    });

    function loadSelectOptionAllMataKuliah(element) {

        var url = base_url_js + 'api/__getAllMK';
        var option = $('' + element);

        $.get(url, function(data) {
            for (var i = 0; i < data.length; i++) {
                option.append('<option value="' + data[i].ID + '.' + data[i].MKCode + '">' + data[i].Code + ' | ' + data[i].MKCode + ' - ' + data[i].NameEng + '</option>');
            }
        });
    }

    function loadSelectOptionAllMataKuliahForPraSyarat(element, selected) {

        var url = base_url_js + 'api/__getAllMK';
        var option = $('' + element);
        $.get(url, function(data) {
            for (var i = 0; i < data.length; i++) {

                option.append('<option value="' + data[i].ID + '.' + data[i].MKCode + '">' + data[i].Code + ' | ' + data[i].MKCode + ' - ' + data[i].NameEng + '</option>')
                    .val(selected).trigger('change');

            }
        });
    }

    function loadSelectOptionAllMataKuliahSingle(element, selected) {
        var url = base_url_js + 'api/__getAllMK';
        var option = $('' + element);

        $.get(url, function(data) {

            for (var i = 0; i < data.length; i++) {
                option.append('<option value="' + data[i].ID + '.' + data[i].MKCode + '" >' + data[i].Code + ' | ' + data[i].MKCode + ' - ' + data[i].NameEng + '</option>')
                    .val('' + selected).trigger('change');
            }
        });
    }

    function loadSelectOptionLecturersSingle(element, selected) {

        var url = base_url_js + 'api/__getDosenSelectOption';
        $.get(url, function(data) {
            var option = $('' + element);
            for (var i = 0; i < data.length; i++) {
                option.append('<option value="' + data[i].NIP + '">' + data[i].NIP + ' | ' + data[i].Name + '</option>')
                    .val(selected).trigger('change');
            }
        });
    }

    function loadSelectOptionLecturersSingle_NIDN(element, selected) {

        var url = base_url_js + 'api/__getDosenSelectOption';
        $.get(url, function(data) {
            var option = $('' + element);
            for (var i = 0; i < data.length; i++) {
                var NIDN = (data[i].NIDN != '' && data[i].NIDN != null && data[i].NIDN != 0) ? data[i].NIDN : '-';
                option.append('<option value="' + data[i].NIP + '">' + NIDN + ' | ' + data[i].Name + '</option>')
                    .val(selected).trigger('change');
            }
        });
    }

    function loadSelectOptionSurvQuestionType(element, selected) {

        var data = {
            action: 'getSurvQuestionType'
        };

        var token = jwt_encode(data, 'UAP)(*');
        var url = base_url_js + 'apimenu/__crudSurvey';

        $.post(url, {
            token: token
        }, function(jsonResult) {
            $.each(jsonResult, function(i, v) {
                var sc = (selected == v.ID) ? 'selected' : '';
                $(element).append('<option value="' + v.ID + '" ' + sc + '>' + v.Description + '</option>');
            });
        });

    }

    function loadSelectOptionSurvQuestionCategory(element, selected) {

        var data = {
            action: 'getQuestionCategory',
            DepartmentID: sessionIDdepartementNavigation
        };

        var token = jwt_encode(data, 'UAP)(*');
        var url = base_url_js + 'apimenu/__crudSurvey';
        $.post(url, {
            token: token
        }, function(jsonResult) {
            if (jsonResult.length > 0) {
                $.each(jsonResult, function(i, v) {
                    var sc = (selected == v.ID) ? 'selected' : '';
                    $(element).append('<option value="' + v.ID + '" ' + sc + '>' + v.Description + '</option>');
                })
            }
        });

    }

    function loadSelectOptionConf(element, jenis, selected) {

        var table = jenis;

        var url = base_url_js + "api/__crudKurikulum";
        var data = {
            action: 'read',
            table: table
        };

        var token = jwt_encode(data, 'UAP)(*');

        $.post(url, {
            token: token
        }, function(data_json) {
            if (data_json.length > 0) {
                for (var i = 0; i < data_json.length; i++) {
                    var selc = (selected == data_json[i].ID) ? 'selected' : '';

                    $('' + element).append('<option value="' + data_json[i].ID + '" ' + selc + '>' + data_json[i].Name + '</option>');
                }
            }
        })
    }

    function loadSelectOptionClassGroup(element, selected) {

        var url = base_url_js + 'academic/kurikulum/getClassGroup';
        var token = jwt_encode({
            action: 'read_json'
        }, 'UAP)(*');

        $.post(url, {
            token: token
        }, function(data_json) {

            var option = $('' + element);
            if (data_json.length > 0) {
                for (var i = 0; i < data_json.length; i++) {
                    option.append('<optgroup id="opt' + i + '" label="' + data_json[i].optgroup.ProdiName + '"></optgroup>');
                    var opt = $('#opt' + i);
                    var detail = data_json[i].options;
                    for (var x = 0; x < detail.length; x++) {
                        opt.append('<option value="' + detail[x].ID + '">' + detail[x].Name + '</option>');
                    }
                }
            }


        });

    }


    function loadSelectOptionClassGroupAttendance(SemesterID, element, selected, showColom = '') {

        var url = base_url_js + 'api/__crudSchedule';
        var token = jwt_encode({
            action: 'getClassGroup',
            SemesterID: SemesterID
        }, 'UAP)(*');

        $.post(url, {
            token: token
        }, function(jsonResult) {

            for (var i = 0; i < jsonResult.length; i++) {

                var d = jsonResult[i];

                var textV = (typeof showColom !== 'undefined' && showColom != '') ?
                    d['' + showColom] :
                    d.Name;

                $(element).append('<option value="' + d.ScheduleID + '.' + d.ClassGroup + '">' + d.ClassGroup + ' - ' + textV + '</option>');
            }

        });

    }

    function loadSelectOptionClassroom(element, selected) {
        var url = base_url_js + 'api/__crudClassroom';
        var token = jwt_encode({
            action: 'read'
        }, 'UAP)(*');

        var option = $('' + element);
        $.post(url, {
            token: token
        }, function(data_json) {
            if (data_json.length > 0) {
                // $(element).empty();
                for (var i = 0; i < data_json.length; i++) {
                    var selec = (selected == data_json[i].ID) ? 'selected' : '';
                    option.append('<option value="' + data_json[i].ID + '" ' + selec + '>' + data_json[i].Room + ' | Seat : ' + data_json[i].Seat + ' | Exam : ' + data_json[i].SeatForExam + '</option>');
                }
            }

        });
    }

    function loadSelect2OptionClassroom(element, selected) {

        var url = base_url_js + 'api/__crudClassroom';
        var token = jwt_encode({
            action: 'read'
        }, 'UAP)(*');

        var option = $('' + element);
        $.post(url, {
            token: token
        }, function(data_json) {
            if (data_json.length > 0) {
                for (var i = 0; i < data_json.length; i++) {
                    option.append('<option value="' + data_json[i].ID + '.' + data_json[i].Seat + '.' + data_json[i].SeatForExam + '">' + data_json[i].Room + ' | Seat : ' + data_json[i].Seat + ' | Exam : ' + data_json[i].SeatForExam + '</option>')
                        .val(selected).trigger('change');
                }
            }

        });

    }

    function loadSelectOptionTimePerCredit(element, selected) {
        var url = base_url_js + 'api/__crudTimePerCredit';
        var token = jwt_encode({
            action: 'read'
        }, 'UAP)(*');

        var option = $('' + element);
        $.post(url, {
            token: token
        }, function(data_json) {
            if (data_json.length > 0) {
                // $(element).empty();
                for (var i = 0; i < data_json.length; i++) {
                    var selec = (selected == data_json[i].Time) ? 'selected' : '';
                    option.append('<option value="' + data_json[i].Time + '" ' + selec + '>' + data_json[i].Time + ' minute</option>');
                }
            }

        });
    }

    function loadSelectOptionCategoryLecturerEvaluation(element, selected) {
        var url = base_url_js + 'api/__crudLecturerEvaluation';
        var token = jwt_encode({
            action: 'readLECategory'
        }, 'UAP)(*');
        $.post(url, {
            token: token
        }, function(jsonResult) {

            if (jsonResult.length > 0) {
                for (var i = 0; i < jsonResult.length; i++) {
                    var d = jsonResult[i];
                    var sc = (selected != '' && selected == d.ID) ? 'selected' : '';
                    $(element).append('<option value="' + d.ID + '" ' + sc + '>' + d.Category + '</option>');
                }
            }
        });
    }

    function loadSelectOptionDistrict_select2(element, selected) {
        var url = base_url_js + 'api/__getWilayahURLJson';
        $.getJSON(url, function(jsonResult) {

            if (jsonResult.length > 0) {
                $.each(jsonResult, function(i, v) {
                    $(element).append('<option value="' + v.RegionID + '">' + v.RegionName + '</option>')
                        .val(selected).trigger('change');
                });
            }

        });
    }


    function loadSelectOptionUniversity(element, selected) {

        var url = base_url_js + 'api/__loadUniversity';
        $.get(url, function(data) {
            var option = $(element);
            for (var i = 0; i < data.length; i++) {
                option.append('<option value="' + data[i].ID + '">' + data[i].Name_University + '</option>')
                    .val(selected).trigger('change');
            }
        });
    }

    function loadSelectOptionMajorEmployees(element, selected) {

        var url = base_url_js + 'api/__loadMajorEmployee';
        $.get(url, function(data) {
            var option = $(element);
            for (var i = 0; i < data.length; i++) {
                option.append('<option value="' + data[i].ID + '">' + data[i].Name_MajorProgramstudy + '</option>')
                    .val(selected).trigger('change');
            }
        });
    }

    function loadSelectOptionProgramStudyEmployees(element, selected) {

        var url = base_url_js + 'api/__loadMajorEmployee';
        $.get(url, function(data) {
            var option = $(element);
            for (var i = 0; i < data.length; i++) {
                option.append('<option value="' + data[i].ID + '">' + data[i].Name_MajorProgramstudy + '</option>')
                    .val(selected).trigger('change');
            }
        });
    }

    function loadSelectOptionStatusMarketing(element, selected) {
        var data = {
            action: 'status_PS'
        };

        var token = jwt_encode(data, 'UAP)(*');
        var url = base_url_js + 'rest2/__crudProspectiveStudents';

        $.post(url, {
            token: token
        }, function(jsonResult) {

            if (jsonResult.length > 0) {
                $.each(jsonResult, function(i, v) {

                    var sc = (selected != '' && selected != null && selected !== 'undefined' && selected == v.ID) ?
                        'selected' : '';

                    var dsc = (v.Status == '1') ? 'disabled' : '';
                    var clsHide = (v.Status == '1') ? 'hide' : '';

                    $(element).append('<option class="' + clsHide + '" value="' + v.ID + '" ' + sc + ' ' + dsc + '>' + v.Description + '</option>');

                });
            }

        });
    }

    function loadSelectOptionScheoolBy(CityID, element, selected) {
        var url = base_url_js + 'api/__getSchoolByCityID/' + CityID;
        $.getJSON(url, function(jsonResult) {
            // console.log(jsonResult);
            if (jsonResult.length > 0) {
                $.each(jsonResult, function(i, v) {
                    var fID = (i == 0) ? v.ID : fID;
                    var sc = (selected != '' && selected != null && selected !== 'undefined') ?
                        selected : fID;
                    $(element).append('<option value="' + v.ID + '">' + v.SchoolName + '</option>')
                        .val(sc).trigger('change');

                });
            }
        });
    }

    function momentRange(start, end) {
        // var fromDate = moment();
        // var toDate = moment().add(15, 'days');

        var fromDate = moment(start);
        var toDate = moment(end);

        var range = moment().range(fromDate, toDate);
        var diff = range.diff('days');

        var array = range.toArray('days');
        // $.each(array, function(i, e) {
        //     $("#rangec").append("<li>" + moment(e).format("DD MM YYYY") + "</li>");
        // });

        var res = {
            diff: diff,
            details: array
        };

        return res;

    }

    function loSelectOptionSemester(element, selected) {
        var url = base_url_js + 'api/__crudSemester';
        var token = jwt_encode({
            action: 'read',
            order: 'DESC'
        }, 'UAP)(*');

        $.post(url, {
            token: token
        }, function(data_json) {

            var option = $(element);
            if (data_json.length > 0) {
                for (var i = 0; i < data_json.length; i++) {
                    var selc = '';
                    if (selected == 'selectedNow') {
                        selc = (data_json[i].Status == 1) ? 'selected' : '';

                    } else {
                        if (selected != '' && selected != null && typeof selected !== undefined) {
                            selc = (selected == data_json[i].ID) ? 'selected' : '';
                        } else {
                            selc = (data_json[i].Status == 1) ? 'selected' : '';
                        }

                    }



                    option.append('<option value="' + data_json[i].ID + '.' + data_json[i].Year + '.' + data_json[i].Code + '" ' + selc + '>' + data_json[i].Name + '</option>');

                }
            }
        });
    }

    function loSelectOptionSemester2(selector, selected) { // created by adhi 2020-02-13
        var url = base_url_js + 'api/__crudSemester';
        var token = jwt_encode({
            action: 'read',
            order: 'DESC'
        }, 'UAP)(*');

        $.post(url, {
            token: token
        }, function(data_json) {

            var option = selector;
            if (data_json.length > 0) {
                for (var i = 0; i < data_json.length; i++) {
                    var selc = '';
                    if (selected == 'selectedNow') {
                        selc = (data_json[i].Status == 1) ? 'selected' : '';

                    } else {
                        if (selected != '' && selected != null && typeof selected !== undefined) {
                            selc = (selected == data_json[i].ID) ? 'selected' : '';
                        } else {
                            selc = (data_json[i].Status == 1) ? 'selected' : '';
                        }

                    }



                    option.append('<option value="' + data_json[i].ID + '.' + data_json[i].Year + '.' + data_json[i].Code + '" ' + selc + ' status = "' + data_json[i].Status + '" >' + data_json[i].Name + '</option>');

                }
            }
        });
    }

    function loSelectOptionSemesterAntara(element, selected) {
        var url = base_url_js + 'api/__crudSemester';
        var token = jwt_encode({
            action: 'readAntara',
            order: 'DESC'
        }, 'UAP)(*');

        $.post(url, {
            token: token
        }, function(data_json) {

            var option = $(element);
            if (data_json.length > 0) {
                for (var i = 0; i < data_json.length; i++) {
                    var selc = '';
                    if (selected == 'selectedNow') {
                        selc = (data_json[i].Status == 1) ? 'selected' : '';

                    } else {
                        if (selected != '' && selected != null && typeof selected !== undefined) {
                            selc = (selected == data_json[i].ID) ? 'selected' : '';
                        } else {
                            selc = (data_json[i].Status == 1) ? 'selected' : '';
                        }

                    }



                    option.append('<option value="' + data_json[i].ID + '.' + data_json[i].Year + '.' + data_json[i].Code + '" ' + selc + '>' + data_json[i].Name + '</option>');

                }
            }
        });
    }

    function loadSelectOptionEmployeesSingle(element, selected) {
        var url = base_url_js + 'api/__crudEmployees';
        var token = jwt_encode({
            action: 'read'
        }, 'UAP)(*');
        $.post(url, {
            token: token
        }, function(data_json) {
            var option = $(element);
            if (data_json.length > 0) {
                for (var i = 0; i < data_json.length; i++) {
                    var data = data_json[i];
                    option.append('<option value="' + data.NIP + '">' + data.NIP + ' - ' + data.Name + '</option>')
                        .val(selected).trigger('change');
                }
            }
        })
    }

    function loadSelectOptionStudentYudisium(element, selected, status, SemesterID = '') {
        var url = base_url_js + 'api/__crudFinalProject';
        var token = jwt_encode({
            action: 'getAllStdReg',
            SemesterID: SemesterID,
            Status: status
        }, 'UAP)(*');
        $.post(url, {
            token: token
        }, function(jsonResult) {

            if (jsonResult.length > 0) {

                $.each(jsonResult, function(i, v) {

                    var mentor1 = (v.Mentor1 != null && v.Mentor1 != '') ? '(' + v.Mentor1 : '';

                    var mentor = '';
                    if (v.Mentor2 != null && v.Mentor2 != '' && v.Mentor1 != null && v.Mentor1 != '') {
                        mentor = mentor1 + ', ' + v.Mentor2 + ')';
                    } else if (v.Mentor2 != null && v.Mentor2 != '' && mentor1 == '') {
                        mentor = mentor1 + ')';
                    }

                    if (mentor != '') {
                        $(element).append('<option value="' + v.NPM + '" >' + v.NPM + ' - ' + v.Name + ' ' + mentor + '</option>')
                            .val(selected).trigger('change');
                    }



                });

            }

        });
    }

    function loadSelectOptionExperiencePosistionLevel(element, selected) {
        var token = jwt_encode({
            action: 'loadPositionLevel'
        }, 'UAP)(*');
        var url = base_url_js + 'api3/__crudTracerAlumni';

        $.post(url, {
            token: token
        }, function(jsonResult) {

            if (jsonResult.length > 0) {
                $.each(jsonResult, function(i, v) {

                    var sc = (selected != '' && selected != null &&
                        selected !== 'undefined' && selected == v.ID) ? 'selected' : '';
                    $(element).append('<option value="' + v.ID + '" ' + sc + '>' + v.Description + '</option>');

                });
            }

        });

    }

    function loadSelectOptionLanguageProdi(element, selected) {

        var token = jwt_encode({
            action: 'readLanguageProdi'
        }, 'UAP)(*');
        var url = base_url_js + 'api-prodi/__crudDataProdi';

        $.post(url, {
            token: token
        }, function(jsonResult) {

            if (jsonResult.length > 0) {
                $.each(jsonResult, function(i, v) {

                    $(element).append('<option value="' + v.ID + '">' + v.Language + '</option>');

                });
            }

        });

    }
    function loadSelectOptionLanguageWebdivisi(element,selected) {

        var token = jwt_encode({action:'readLanguageProdi'},'UAP)(*');
        var url = base_url_js+'api-webdivisi/__crudDataDivisi';

        $.post(url,{token:token},function (jsonResult) {

            if(jsonResult.length>0){
                $.each(jsonResult,function (i,v) {

                    $(element).append('<option value="'+v.ID+'">'+v.Language+'</option>');

                });
            }

        });

    }

    // function loadSelectOptionStudentRegisterSeminarhasil(element,selected) {
    //     var url = base_url_js+'api/__crudFinalProject';
    //     var token = jwt_encode({action : 'getAllStdReg',Status:'3'},'UAP)(*');
    //     $.post(url,{token:token},function (jsonResult) {
    //
    //         if(jsonResult.length>0){
    //
    //             $.each(jsonResult,function (i,v) {
    //
    //                 var mentor1 = (v.Mentor1!=null && v.Mentor1!='') ? '('+v.Mentor1 : '';
    //                 var mentor = (v.Mentor2!=null && v.Mentor2!='') ? mentor1+', '+v.Mentor2+')' : mentor1+')';
    //
    //                 $(element).append('<option value="'+v.NPM+'">'+v.NPM+' - '+v.Name+' '+mentor+'</option>')
    //                     .val(selected).trigger('change');
    //
    //             });
    //
    //         }
    //
    //     });
    // }

    function loadSelectOptionMenuAgregator(element, selected, type) {

        var url = base_url_js + 'api3/__getListMenuAgregator/' + type;
        $.getJSON(url, function(jsonResult) {

            if (jsonResult.length > 0) {
                $.each(jsonResult, function(i, v) {


                    $(element).append('<option value="' + v.ID + '">' + v.Name + '</option>');

                });
            }

        });

    }

    function loadSelectOptionReasonTransferStudent(element, selected) {
        var url = base_url_js + 'api/__crudTransferStudent';
        var token = jwt_encode({
            action: 'readReason'
        }, 'UAP)(*');
        $.post(url, {
            token: token
        }, function(jsonResult) {
            if (jsonResult.length > 0) {
                for (var i = 0; i < jsonResult.length; i++) {
                    var d = jsonResult[i];
                    var sc = (typeof selected !== "undefined" && d.ID == selected) ? 'selected' : '';

                    $(element).append('<option ' + sc + ' value="' + d.ID + '">' + d.Reason + '</option>');

                }
            }
        })
    }

    function loadSelectOptionCRMPeriod(element, selected) {
        var data = {
            action: 'readCRMPeriode'
        };
        var token = jwt_encode(data, 'UAP)(*');
        var url = base_url_js + 'rest2/__crudCRMPeriode';
        $.post(url, {
            token: token
        }, function(jsonResult) {

            $.each(jsonResult, function(i, v) {
                var sc = (parseInt(selected) == parseInt(v.ID)) ? 'selected' : '';
                $(element).append('<option value="' + v.ID + '" ' + sc + '>Period - ' + v.Name + '</option>');
            })

        });
    }

    function loadSelectOptionMarketingActNow(element, selected) {

        var data = {
            action: 'readActiveNow_MA'
        };

        var token = jwt_encode(data, 'UAP)(*');
        var url = base_url_js + 'rest2/__crudMarketingActivity';

        $.post(url, {
            token: token
        }, function(jsonResult) {

            $(element).append('<option value="">-- Not yet select --</option>');

            if (jsonResult.length > 0) {
                $.each(jsonResult, function(i, v) {

                    $(element).append('<option value="' + v.ID + '">' + v.Title + '</option>');


                });
            }


        });

    }

    function loadSelectOptionMonthYear_MA(elementMonth, elementYear, selectedMonth, selectedYear) {

        var data = {
            action: 'readMonthYear_MA'
        };

        var token = jwt_encode(data, 'UAP)(*');
        var url = base_url_js + 'rest2/__crudMarketingActivity';

        $.post(url, {
            token: token
        }, function(jsonResult) {

            // Month
            if (jsonResult.Month.length > 0) {
                $.each(jsonResult.Month, function(i, v) {
                    var mm = moment().months(parseInt(v.Month) - 1).format('MMMM');

                    $(elementMonth).append('<option value="' + v.Month + '">' + mm + '</option>');
                });
            }

            // Year
            if (jsonResult.Year.length > 0) {
                $.each(jsonResult.Year, function(i, v) {
                    // var yy = moment().months(v.Year).format('YYYY');
                    $(elementYear).append('<option value="' + v.Year + '">Year ' + v.Year + '</option>');
                });
            }

        });

    }

    function loadSelectOptionGraduationYear(element, selected) {

        var data = {
            action: 'getGraduationYear'
        };
        var token = jwt_encode(data, 'UAP)(*');
        var url = base_url_js + 'api3/__crudTracerAlumni';

        $.post(url, {
            token: token
        }, function(jsonResult) {
            if (jsonResult.length > 0) {

                $.each(jsonResult, function(i, v) {
                    var sc = (v.GraduationYear == selected) ? 'selected' : '';
                    $(element).append('<option value="' + v.GraduationYear + '" ' + sc + '>' + v.GraduationYear + '</option>');
                });

            }
        });

    }

    function getIDSemesterActive(element) {
        var url = base_url_js + 'api/__getSemesterActive';
        $.getJSON(url, function(jsonResult) {
            // console.log(jsonResult);
            $(element).val(jsonResult.ID);
        });
    }


    // ===== Function HR =====

    function loadSelectOptionStatusEmployee(element, selected) {
        var url = base_url_js + 'api/__getStatusEmployee';
        $.getJSON(url, function(jsonResult) {
            for (var i = 0; i < jsonResult.length; i++) {
                var d = jsonResult[i];
                var sc = (selected != null && selected != '' && typeof selected !== 'undefined' && selected == d.IDStatus) ? 'selected' : '';
                var bg = (d.IDStatus < 0) ? 'style="color:red;"' : '';
                $(element).append('<option value="' + d.IDStatus + '" ' + sc + ' ' + bg + '>' + d.Description + '</option>');
            }
        });
    }

    // ======================

    function fillDays(element, lang, selected) {
        var days = (lang == 'Eng') ? daysEng : daysInd;

        // $('#NameDay1');
        var val = 1;
        for (var i = 0; i < days.length; i++) {
            var selc = (val == selected) ? 'selected' : '';
            var hol = (val == 6 || val == 7) ? 'style="color:red;"' : '';
            $('' + element).append('<option value="' + val + '" ' + selc + ' ' + hol + '>' + days[i] + '</option>');
            val += 1;
        }
    }

    function formatRupiah(bilangan) {
        var ReadMinus = function(bilangan) {
            var bool = false;
            var number_string = bilangan.toString();
            var a = number_string.substr(0, 1);
            var n = number_string.length;
            if (a == '-') {
                bool = true;
                bilangan = number_string.substr(1, n);
            }

            var dt = {
                status: bool,
                bilangan: bilangan,
            };

            return dt;
        }

        var chkminus = ReadMinus(bilangan);
        var minus = (chkminus['status']) ? '- ' : '';
        bilangan = chkminus['bilangan'];

        var number_string = bilangan.toString(),
            sisa = number_string.length % 3,
            rupiah = number_string.substr(0, sisa),
            ribuan = number_string.substr(sisa).match(/\d{3}/g);

        if (ribuan) {
            separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }

        return minus + 'Rp. ' + rupiah + ',-';
    }

    function formatDigitNumber(bilangan) {
        var number_string = bilangan.toString(),
            sisa = number_string.length % 3,
            rupiah = number_string.substr(0, sisa),
            ribuan = number_string.substr(sisa).match(/\d{3}/g);

        if (ribuan) {
            separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }

        return rupiah;
    }

    function UrlExists(url) {
        var http = new XMLHttpRequest();
        http.open('HEAD', url, false);
        http.send();
        return http.status != 404;
    }

    function errorInput(element) {
        $(element).css('border', '1px solid red');
        setTimeout(function() {
            $(element).css('border', '1px solid #cccccc');
        }, 5000);

        return false;
    }

    // BY ADHI
    function Validation_leastCharacter(leastNumber, string, theName) {
        var result = {
            status: 1,
            messages: ""
        };
        var stringLenght = string.length;
        if (stringLenght < leastNumber) {
            result = {
                status: 0,
                messages: theName + " at least " + leastNumber + " character"
            };
        }
        return result;
    }

    function Validation_email(string, theName) {
        var result = {
            status: 1,
            messages: ""
        };
        var regexx = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
        if (!string.match(regexx)) {
            result = {
                status: 0,
                messages: theName + " an invalid email address! "
            };
        }
        return result;
    }

    function Validation_email_gmail(string, theName) {
        var result = {
            status: 1,
            messages: ""
        };
        var regexx = /^[a-z0-9](\.?[a-z0-9]){5,}@g(oogle)?mail\.com$/;
        if (!string.match(regexx)) {
            result = {
                status: 0,
                messages: theName + " only gmail allowed to register! "
            };
        }
        return result;
    }

    function Validation_required(string, theName) {
        var result = {
            status: 1,
            messages: ""
        };
        if (string == "" || string == null) {
            result = {
                status: 0,
                messages: theName + " is required! "
            };
        }
        return result;
    }

    function Validation_numeric(string, theName) {
        var result = {
            status: 1,
            messages: ""
        };
        var regexx = /^\d+$/;
        if (!string.match(regexx)) {
            result = {
                status: 0,
                messages: theName + " only numeric! "
            };
        }
        return result;
    }

    function Validation_decimal(string, theName) {
        var result = {
            status: 1,
            messages: ""
        };
        var regexx = /^\d*\.?\d*$/;
        if (!string.match(regexx)) {
            result = {
                status: 0,
                messages: theName + " only decimal! "
            };
        }
        return result;
    }

    function number_comma_dot(string, theName) {
        var result = {
            status: 1,
            messages: ""
        };
        var regexx = /^(\d+(\.\d{0,2})?|\.?\d{1,2})$/;
        if (!string.match(regexx)) {
            result = {
                status: 0,
                messages: theName + " only number,comma and dot! "
            };
        }
        return result;
    }

    function LoaddataTable(element) {
        var table = $(element).DataTable({
            'iDisplayLength': 5,
            'ordering': true,
           // "sDom": "<'row'<'dataTables_header clearfix'<'col-md-3'l><'col-md-9'Tf>r>>t<'row'<'dataTables_footer clearfix'<'col-md-6'i><'col-md-6'p>>>", // T is new
            // "oTableTools": {
            //     "aButtons": [
            //         // "copy",
            //         // "print",
            //         // "csv",
            //         {
            //             "sExtends": "xls",
            //             "sButtonText": '<i class="fa fa-download" aria-hidden="true"></i> Excel',
            //         },
            //         {
            //             "sExtends": "pdf",
            //             "sButtonText": '<i class="fa fa-download" aria-hidden="true"></i> PDF',
            //             "sPdfOrientation": "landscape",
            //             // "sPdfMessage" : "Daftar Seluruh Mata Kuliah"
            //         }
            //     ],
            //     "sSwfPath": base_url_js + "assets/template/plugins/datatables/tabletools/swf/copy_csv_xls_pdf.swf"
            // },
            "columnDefs": [{
                "targets": 0,
                "orderable": false
            }]
        });

        table.on('order.dt search.dt', function() {
            table.column(0, {
                search: 'applied',
                order: 'applied'
            }).nodes().each(function(cell, i) {
                cell.innerHTML = i + 1;
            });
        }).draw();
    }

    function LoaddataTableStandard(element) {
        var table = $(element).DataTable({
            'iDisplayLength': 5,
            'ordering': true,
            // "sDom": "<'row'<'dataTables_header clearfix'<'col-md-3'l><'col-md-9'Tf>r>>t<'row'<'dataTables_footer clearfix'<'col-md-6'i><'col-md-6'p>>>", // T is new
        });
    }

    function getValueChecbox(element) {
        var allVals = [];
        $('.datatable2 :checked').each(function() {
            allVals.push($(this).val());
        });
        return allVals;
    }

    function loadingStart() {
        $('#NotificationModal .modal-header').addClass('hide');
        $('#NotificationModal .modal-body').html('<center>' +
            '                    <i class="fa fa-refresh fa-spin fa-3x fa-fw"></i>' +
            '                    <br/>' +
            '                    Loading Data . . .' +
            '<button type="button" id="ModalbtnCancleForm" data-dismiss="modal" class="btn btn-default hide">Close</button>' +
            '                </center>');
        $('#NotificationModal .modal-footer').addClass('hide');
        $('#NotificationModal').modal({
            'backdrop': 'static',
            'show': true
        });
    }

    function loadingEnd(timeout) {
        setTimeout(function() {
            $('#NotificationModal').find('#ModalbtnCancleForm').trigger('click');
            $('#NotificationModal').modal('hide');

        }, timeout);
    }

    function PopupCenter(url, title, w, h) {
        // Fixes dual-screen position                         Most browsers      Firefox
        var dualScreenLeft = window.screenLeft != undefined ? window.screenLeft : screen.left;
        var dualScreenTop = window.screenTop != undefined ? window.screenTop : screen.top;

        width = window.innerWidth ? window.innerWidth : document.documentElement.clientWidth ? document.documentElement.clientWidth : screen.width;
        height = window.innerHeight ? window.innerHeight : document.documentElement.clientHeight ? document.documentElement.clientHeight : screen.height;

        var left = ((width / 2) - (w / 2)) + dualScreenLeft;
        var top = ((height / 2) - (h / 2)) + dualScreenTop;
        var newWindow = window.open(url, title, 'scrollbars=yes, width=' + w + ', height=' + h + ', top=' + top + ', left=' + left);

        // Puts focus on the newWindow
        if (window.focus) {
            newWindow.focus();
        }
    }

    // Adi
    function loadSelectOptionPaymentTypeAll(element, selected) {
        var url = base_url_js + "api/__getBasePaymentTypeSelectOption";
        $.get(url, function(data) {
            for (var i = 0; i < data.length; i++) {
                var selc = (data[i].ID == selected) ? 'selected' : '';
                $('' + element).append('<option value="' + data[i].ID + '" ' + selc + '>' + data[i].Abbreviation + '</option>');
            }
        });
    }

    // Adi
    function loadSelectOptionPaymentTypeMHS(element, selected) {
        var url = base_url_js + "api/__getBasePaymentTypeSelectOption";
        $.get(url, function(data) {
            for (var i = 0; i < data.length; i++) {
                if (data[i].ID == 2 || data[i].ID == 3) {
                    var selc = (data[i].ID == selected) ? 'selected' : '';
                    $('' + element).append('<option value="' + data[i].ID + '" ' + selc + '>' + data[i].Abbreviation + '</option>');
                }
            }
        });
    }

    function loadSelectOptionDiscount(element, selected) {
        var url = base_url_js + "api/__getBaseDiscountSelectOption";
        $.get(url, function(data) {
            for (var i = 0; i < data.length; i++) {
                var selc = (data[i].ID == selected) ? 'selected' : '';
                $('' + element).append('<option value="' + data[i].Discount + '" ' + selc + '>' + data[i].Discount + '</option>');
            }
        });
    }

    function loadSelectOptionDivision(element, selected) {

        if (selected == '' || typeof selected === "undefined") {
            $(element).append('<option value="" selected>-- Select Division --</option>');
            $(element).append('<option disabled>----------------</option>');
        }

        var url = base_url_js + 'api/__getDivision';
        $.getJSON(url, function(jsonResult) {
            for (var i = 0; i < jsonResult.length; i++) {
                var d = jsonResult[i];
                var sc = (selected != '' && typeof selected !== "undefined" && selected == d.ID) ? 'selected' : '';
                $(element).append('<option value="' + d.ID + '" ' + sc + '>' + d.Division + '</option>');
            }
        });
    }

    function loadSelectOptionPosition(element, selected) {

        if (selected == '' || typeof selected === "undefined") {
            $(element).append('<option value="" selected>-- Select Position --</option>');
            $(element).append('<option disabled>----------------</option>');
        }

        var url = base_url_js + 'api/__getPosition';
        $.getJSON(url, function(jsonResult) {
            for (var i = 0; i < jsonResult.length; i++) {
                var d = jsonResult[i];
                var sc = (selected != '' && typeof selected !== "undefined" && selected == d.ID) ? 'selected' : '';
                $(element).append('<option value="' + d.ID + '" ' + sc + '>' + d.Position + '</option>');
            }
        });
    }

    function loadSelectOptionReligi(element, selected) {
        var url = base_url_js + 'api/__getAgama';

        $.getJSON(url, function(jsonResult) {
            for (var i = 0; i < jsonResult.length; i++) {
                var d = jsonResult[i];
                var sc = (selected != '' && typeof selected !== "undefined" && d.IDReligion == selected) ? 'selected' : '';
                $(element).append('<option value="' + d.IDReligion + '" ' + sc + '>' + d.Religion + '</option>');
            }
        });
    }


    function loadSelectOptionMaritalStatus(element, selected) {
        var url = base_url_js + 'api/__getMaritalStatus';

        $.getJSON(url, function(jsonResult) {
            for (var i = 0; i < jsonResult.length; i++) {
                var d = jsonResult[i];
                var sc = (selected != '' && typeof selected !== "undefined" && d.ID == selected) ? 'selected' : '';
                $(element).append('<option value="' + d.ID + '" ' + sc + '>' + d.name + '</option>');
            }
        });
    }


    function loadSelectOptionEmployeesStatus(element, selected) {
        var url = base_url_js + 'api/__getStatusEmployee';
        $.getJSON(url, function(jsonResult) {
            for (var i = 0; i < jsonResult.length; i++) {
                var d = jsonResult[i];
                var sc = (selected != '' && typeof selected !== "undefined" && selected == d.IDStatus) ? 'selected' : '';
                var color = (d.IDStatus < 0) ? 'style="color:red;"' : '';
                $(element).append('<option value="' + d.IDStatus + '" ' + color + ' ' + sc + '>' + d.Description + '</option>');
            }
        });
    }

    function loadSelectOptionEmployeesStatus2(element, selected) {
        var url = base_url_js + 'api/__getStatusEmployee2';
        $.getJSON(url, function(jsonResult) {
            for (var i = 0; i < jsonResult.length; i++) {
                var d = jsonResult[i];
                var sc = (selected != '' && typeof selected !== "undefined" && selected == d.IDStatus) ? 'selected' : '';
                var color = (d.IDStatus < 0) ? 'style="color:red;"' : '';
                $(element).append('<option value="' + d.IDStatus + '" ' + color + ' ' + sc + '>' + d.Description + '</option>');
            }
        });
    }

    function loadSelectOptionLecturerStatus2(element, selected) {
        var url = base_url_js + 'api/__getStatusLecturer2';
        $.getJSON(url, function(jsonResult) {
            for (var i = 0; i < jsonResult.length; i++) {
                var d = jsonResult[i];
                var sc = (selected != '' && typeof selected !== "undefined" && selected == d.IDStatus) ? 'selected' : '';
                var color = (d.IDStatus < 0) ? 'style="color:red;"' : '';
                $(element).append('<option value="' + d.IDStatus + '" ' + color + ' ' + sc + '>' + d.Description + '</option>');
            }
        });
    }

    function loadSelectOptionPathway(element, selected) {

        var url = base_url_js + 'rest2/__getPathway';
        $.getJSON(url, function(jsonResult) {
            if (jsonResult.length > 0) {
                $.each(jsonResult, function(i, v) {
                    var sc = (selected != '' && selected == v.ID) ? 'selected' : '';
                    $(element).append('<option value="' + v.ID + '" ' + sc + '>' + v.SchoolMajor + '</option>');
                })
            }
        });

    }

    function loadSelectOptionAccreditation(element, selected) {
        var url = base_url_js + 'api3/__getAccreditation';

        $.getJSON(url, function(jsonResult) {

            if (jsonResult.length > 0) {
                $.each(jsonResult, function(i, v) {
                    var sc = (selected != '' && selected != null && selected !== 'undefined' && v.ID == selected) ?
                        'selected' : '';
                    $(element).append('<option value="' + v.ID + '" ' + sc + '>' + v.Label + '</option>');
                });
            };



        });

    }

    function loadSelectOptionMonth(element, selected) {

        var m = moment();
        for (var i = 0; i < 12; i++) {
            var sc = (selected != '' && selected != null &&
                selected !== 'undefined' && selected == (i + 1)) ? 'selected' : '';
            $(element).append('<option value="' + (i + 1) + '" ' + sc + '>' + m.months(i).format('MMMM') + '</option>');
        }

    }

    function loadYearOfBirth(element, selected) {
        $(element).empty();
        var thisYear = (new Date()).getFullYear();
        var startTahun = parseInt(thisYear) - parseInt(120);
        var selisih = parseInt(thisYear) - parseInt(startTahun);
        for (var i = 0; i <= selisih; i++) {
            var valTh = (parseInt(startTahun) + parseInt(i));
            var sc = (selected != '' && typeof selected !== "undefined" && valTh == selected) ? 'selected' : '';
            $(element).append('<option value="' + valTh + '" ' + sc + '>' + valTh + '</option>');
        }
    }

    function loadMonthBirth(element, selected) {
        $(element).empty();
        var month = {
            01: 'Jan',
            02: 'Feb',
            03: 'Mar',
            04: 'April',
            05: 'Mei',
            06: 'Jun',
            07: 'Jul',
            08: 'Aug',
            09: 'Sep',
            10: 'Okt',
            11: 'Nov',
            12: 'Des'
        };

        for (var key in month) {
            var getKey = key.toString();
            var value = (getKey.length == 1) ? '0' + getKey : key;

            var sc = (selected != '' && selected != null && typeof selected !== "undefined" && value == selected) ? 'selected' : '';
            $(element).append('<option value="' + value + '" ' + sc + '>' + month[key] + '</option>');
        }

    }

    function loadCountDays(Year, Month, element, selected) {

        $(element).empty();
        var countDays = moment(Year + "-" + Month, "YYYY-MM").daysInMonth()
        // get dd
        for (var i = 1; i <= countDays; i++) {

            var getKey = i.toString();
            var value = (getKey.length == 1) ? '0' + getKey : value = i;

            var sc = (selected != '' && typeof selected !== "undefined" && value == selected) ? 'selected' : '';

            $(element).append('<option value="' + value + '" ' + sc + '>' + value + '</option>');
        }
    }

    function viewImageBeforeUpload(input, el_View, el_SizeView,
        el_ExtView, el_size, el_Ext) {

        if (input.files && input.files[0]) {
            var sz = parseFloat(input.files[0].size) / 1000;
            var ext = input.files[0].type.split('/')[1];

            $(el_SizeView).html(sz.toFixed(2));
            $(el_ExtView).html(ext);

            $(el_size).val(sz.toFixed(2));
            $(el_Ext).val(ext);

            var reader = new FileReader();

            reader.onload = function(e) {
                $(el_View).attr('src', e.target.result);
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    function findAndReplace(string, target, replacement) {

        var i = 0,
            length = string.length;

        for (i; i < length; i++) {

            string = string.replace(target, replacement);

        }

        return string;

    }

    function isObject(value) {
        return value && typeof value === 'object' && value.constructor === Object;
    }

    function FormSubmitAuto(action, method, values, blank = '_blank') {
        var form = $('<form/>', {
            action: action,
            method: method
        });
        $.each(values, function() {
            form.append($('<input/>', {
                type: 'hidden',
                name: this.name,
                value: this.value
            }));
        });
        form.attr('target', blank);
        form.appendTo('body').submit();
    }

    /* cara penggunaan FormSubmitAuto
      var url = base_url_js+'finance/export_excel_report';
      data = {
        Data : dataa,
        summary : summary,
        PostPassing : PostPassing,
      }
      var token = jwt_encode(data,"UAP)(*");
      FormSubmitAuto(url, 'POST', [
          { name: 'token', value: token },
      ]);*/

    function loadSelectOptionCurriculum2(element, selected) {
        var url = base_url_js + "api/__getKurikulumSelectOption";
        $.get(url, function(data_json) {
            // console.log(data_json);
            for (var i = 0; i < data_json.length; i++) {
                var selected = (data_json[i].ID == selected) ? 'selected' : '';
                $(element).append('<option value="' + data_json[i].ID + '.' + data_json[i].Year + '" ' + selected + '>' + data_json[i].NameEng + '</option>');
            }
        });
    }

    // change curiculem to TA
    function loadSelectOptionCurriculum3(element, selected) {
        var url = base_url_js + "api/__getKurikulumSelectOption";
        $.get(url, function(data_json) {
            // console.log(data_json);
            for (var i = 0; i < data_json.length; i++) {
                var selected = (data_json[i].ID == selected) ? 'selected' : '';
                $(element).append('<option value="' + data_json[i].ID + '.' + data_json[i].Year + '" ' + selected + '>' + (data_json[i].NameEng).replace('Curriculum', 'TA') + '</option>');
            }
        });
    }

    function loadSelectOptionLembaga(element, selected) {

        var url = base_url_js + 'api3/__crudLembagaSurview';
        var token = jwt_encode({
            action: 'readLembagaSurview'
        }, 'UAP)(*');

        $.post(url, {
            token: token
        }, function(jsonResult) {

            $.each(jsonResult, function(i, v) {

                var sc = (selected != '' && selected == v.ID) ? 'selected' : '';
                $(element).append('<option value="' + v.ID + '" ' + sc + '>' + v.Lembaga + '</option>');

            });

        });

    }

    function loadSelectOptionEULAPUblicationDate(element, selected, filterTo) {

        var data = {
            action: 'getPublicationDate',
            To: (typeof filterTo !== 'undefined' && filterTo != '' && filterTo != null) ? filterTo : ''
        };

        var token = jwt_encode(data, 'UAP)(*');
        var url = base_url_js + 'api4/__crudEula';

        $.post(url, {
            token: token
        }, function(jsonResult) {

            $.each(jsonResult, function(i, v) {

                var RangeStart = moment(v.RangeStart).format('DD MMM YYYY');
                var RangeEnd = moment(v.RangeEnd).format('DD MMM YYYY');


                var sc = (selected == v.ID) ? 'selected' : '';
                $(element).append('<option value="' + v.ID + '" ' + sc + '>' + RangeStart + ' - ' + RangeEnd + ' | ' + v.To + '</option>');

            });

        });

    }

    function loadSelectOptionEULAPUblicationTo(element, selected) {

        var data = {
            action: 'getPublicationTo',
        };

        var token = jwt_encode(data, 'UAP)(*');
        var url = base_url_js + 'api4/__crudEula';

        $.post(url, {
            token: token
        }, function(jsonResult) {

            $.each(jsonResult, function(i, v) {

                var sc = (selected == v.To) ? 'selected' : '';
                $(element).append('<option value="' + v.To + '" ' + sc + '>' + v.To + '</option>');

            });

        });

    }

    function loadSelectOptionEULATitle(element, selected, PublicationID) {

        var data = {
            action: 'getEULATitle',
            EDID: (typeof PublicationID !== 'undefined' && PublicationID != '' && PublicationID != null) ?
                PublicationID : ''
        };

        var token = jwt_encode(data, 'UAP)(*');
        var url = base_url_js + 'api4/__crudEula';

        $.post(url, {
            token: token
        }, function(jsonResult) {

            $.each(jsonResult, function(i, v) {

                var sc = (selected == v.ID) ? 'selected' : '';
                $(element).append('<option value="' + v.ID + '" ' + sc + '>' + v.Title + '</option>');

            });

        });

    }

    function getCustomtoFixed(dataValue, digit) {
        // var exTitik = dataValue.toFixed(4).toString().split('.');
        // var exKoma = dataValue.toFixed(4).toString().split(',');

        // var s = 0;
        // var s2 = 0;
        // var after = 0;

        // var result = dataValue;
        // if(exTitik.length>1){
        //     s = exTitik[1].substr(digit,1);
        //     s2 = exTitik[1].substr(0,digit);
        //     after = (parseFloat(s)<5) ? parseFloat(s2) : parseFloat(s2) + 1;
        //     result = exTitik[0]+'.'+after;


        // } else if(exKoma.length>1){
        //     s = exKoma[1].substr(digit,1);
        //     s2 = exKoma[1].substr(0,digit);
        //     after = (parseFloat(s)<5) ? parseFloat(s2) : parseFloat(s2) + 1;
        //     result = exKoma[0]+'.'+after;
        // }

        var result = parseFloat(dataValue).toFixed(digit);
        return result;
    }

    function checkFormRequired(elm, value) {
        if (value != '' && value != null) {
            $(elm).css('border', '1px solid green');
        } else {
            $(elm).css('border', '1px solid red');
        }

        setTimeout(function() {
            $(elm).css('border', '1px solid #ccc');
        }, 5000);
    }


    function setMenuSelected(classHeader, findTag, classActiveName, ArrayMenu, MenuActive) {

        // ----
        // classHeader = parent classnya (biasanya : .nav-tabs)
        // findTag = tag yang akan di masukan class activenya (biasanya : li)
        // classActiveName = nama class yang active (biasanya : active)
        // ArrayMenu = nama menu2 yang ada (sesuai URI)
        // MenuActive = nama menu yang active
        // ----

        var indexAct = $.inArray(MenuActive, ArrayMenu);
        var elmChild = $(classHeader).children(findTag)[indexAct];
        $(classHeader).find(elmChild).addClass(classActiveName);
    }

    // for text area
    function nl2br(str, replaceMode, isXhtml) {

        var breakTag = (isXhtml) ? '<br />' : '<br>';
        var replaceStr = (replaceMode) ? '$1' + breakTag : '$1' + breakTag + '$2';
        return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, replaceStr);
    }

    function br2nl(str, replaceMode) { // yg biasa digunakan

        var replaceStr = (replaceMode) ? "\n" : '';
        // Includes <br>, <BR>, <br />, </br>
        str = str.replace(/<\s*\/?br\s*[\/]?>/gi, replaceStr);
        return str.replace(/<\s*\/?td\s*[\/]?>/gi, '');
    }
    // for text area

    $(document).on('blur', 'input[typeof=number][data-form=phone]', function() {
        var formPhone = $(this).val();

        if (formPhone != '') {

            var v = formatPhone(formPhone);

            $(this).val(v);

        }
    });

    function formatPhone(number) {

        number = parseInt(number);

        number = '' + number;

        var d = number.substr(0, 2);
        // var l = number.substr(2,number.length);
        var v = '';
        if (d == '62' || d == 62) {
            v = number;
        } else {
            v = '62' + parseInt(number);
        }

        return v;
    }

    function checkValue(v) {
        return (v != '' && v != null) ? v : '';
    }

    function checkValueInt(v) {
        return (v != '' && v != null) ? v : 0;
    }

    window.getUrlParameter = function getUrlParameter(sParam) {
        var sPageURL = decodeURIComponent(window.location.search.substring(1)),
            sURLVariables = sPageURL.split('&'),
            sParameterName,
            i;

        for (i = 0; i < sURLVariables.length; i++) {
            sParameterName = sURLVariables[i].split('=');
            if (sParameterName[0] === sParam) {
                return sParameterName[1] === undefined ? true : sParameterName[1];
            }
        }
    };

    function SelectOptionloadBulan(Element, choice = '') {
        var month = {
            01: 'Jan',
            02: 'Feb',
            03: 'Mar',
            04: 'April',
            05: 'Mei',
            06: 'Jun',
            07: 'Jul',
            08: 'Aug',
            09: 'Sep',
            10: 'Okt',
            11: 'Nov',
            12: 'Des'
        }

        if (choice != '') {
            Element.append('<option value="' + 'all' + '" ' + 'selected' + '>' + '--No Filtering Month--' + '</option>');
        }

        for (var key in month) {
            // var selected = (key==1) ? 'selected' : '';
            var getKey = key.toString();
            if (getKey.length == 1) {
                var value = '0' + getKey;
            } else {
                var value = key;
            }
            Element.append('<option value="' + value + '" ' + '' + '>' + month[key] + '</option>');
        }

        Element.select2({
            // allowClear: true
        });
    }

    function saveTable2Excel(element) {
        var elm = $('.' + element);
        var name = elm.attr('data-name');
        var preserveColors = (elm.hasClass('table2excel_with_colors') ? true : false);
        elm.table2excel({
            exclude: ".noExl",
            filename: name,
            fileext: ".xlsx",
            preserveColors: preserveColors
        });
    }

    function load_data_department_ajax() {
        var def = jQuery.Deferred();
        var url = base_url_js + "api/__getAllDepartementPU";
        $.post(url, function(data_json) {

        }).done(function(data_json) {
            def.resolve(data_json);
        }).fail(function() {
            toastr.info('No Result Data');
            def.reject();
        }).always(function() {

        });
        return def.promise();
    }

    function load_select_ta_mhs(selector = null) {
        var def = jQuery.Deferred();
        var url = base_url_js + "api3/__getAllTA_MHS";
        $.post(url, function(data_json) {

        }).done(function(data_json) {
            if (selector != null) {
                selector.empty();
                for (var i = 0; i < data_json.length; i++) {
                    selector.append('<option value = "' + data_json[i] + '">' + data_json[i] + '</option>')
                }
            }
            def.resolve(data_json);
        }).fail(function() {
            toastr.info('No Result Data');
            def.reject();
        }).always(function() {

        });
        return def.promise();
    }

    /* Adhi 2020-02-14 */
    function AjaxSubmitForm(url = '', token = '', ArrUploadFilesSelector = [], Apikey = '', requestHeader = {}) {
        var def = jQuery.Deferred();
        var form_data = new FormData();
        form_data.append('token', token);
        if (ArrUploadFilesSelector.length > 0) {
            for (var i = 0; i < ArrUploadFilesSelector.length; i++) {
                var NameField = ArrUploadFilesSelector[i].NameField + '[]';
                var Selector = ArrUploadFilesSelector[i].Selector;
                var UploadFile = Selector[0].files;
                for (var count = 0; count < UploadFile.length; count++) {
                    form_data.append(NameField, UploadFile[count]);
                }
            }
        }

        $.ajax({
            type: "POST",
            // url:url+'?apikey='+Apikey,
            url: (Apikey != '') ? url + '?apikey=' + Apikey : url,
            data: form_data,
            contentType: false, // The content type used when sending data to the server.
            cache: false, // To unable request pages to be cached
            processData: false,
            dataType: "json",
            beforeSend: function(xhr) {
                // xhr.setRequestHeader("Hjwtkey",Hjwtkey);
                for (key in requestHeader) {
                    xhr.setRequestHeader(key, requestHeader[key]);
                }

            },
            success: function(data) {
                def.resolve(data);
            },
            error: function(data) {
                def.reject();
            }
        })
        return def.promise();
    }
    /* End */

    function AjaxSubmitFormPromises(url = '', token = '', ArrUploadFilesSelector = [], Apikey = '', requestHeader = {}) {
        return new Promise((resolve, reject) => {
            var form_data = new FormData();
            form_data.append('token', token);
            if (ArrUploadFilesSelector.length > 0) {
                for (var i = 0; i < ArrUploadFilesSelector.length; i++) {
                    var NameField = ArrUploadFilesSelector[i].NameField + '[]';
                    var Selector = ArrUploadFilesSelector[i].Selector;
                    var UploadFile = Selector[0].files;
                    for (var count = 0; count < UploadFile.length; count++) {
                        form_data.append(NameField, UploadFile[count]);
                    }
                }
            }

            $.ajax({
                type: "POST",
                // url:url+'?apikey='+Apikey,
                url: (Apikey != '') ? url + '?apikey=' + Apikey : url,
                data: form_data,
                contentType: false, // The content type used when sending data to the server.
                cache: false, // To unable request pages to be cached
                processData: false,
                dataType: "json",
                beforeSend: function(xhr) {
                    for (key in requestHeader) {
                        xhr.setRequestHeader(key, requestHeader[key]);
                    }

                },
                success: function(data) {
                    resolve(data);
                },
                error: function(data) {
                    reject();
                }
            })
        })
    }

    function AjaxSubmitFormPromisesNoToken(url = '', data = {}, ArrUploadFilesSelector = [], Apikey = '', requestHeader = {}) {
        return new Promise((resolve, reject) => {
            var form_data = new FormData();
            for (key in data) {
                form_data.append(key, data[key]);
            }

            if (ArrUploadFilesSelector.length > 0) {
                for (var i = 0; i < ArrUploadFilesSelector.length; i++) {
                    var NameField = ArrUploadFilesSelector[i].NameField + '[]';
                    var Selector = ArrUploadFilesSelector[i].Selector;
                    var UploadFile = Selector[0].files;
                    for (var count = 0; count < UploadFile.length; count++) {
                        form_data.append(NameField, UploadFile[count]);
                    }
                }
            }

            $.ajax({
                type: "POST",
                // url:url+'?apikey='+Apikey,
                url: (Apikey != '') ? url + '?apikey=' + Apikey : url,
                data: form_data,
                contentType: false, // The content type used when sending data to the server.
                cache: false, // To unable request pages to be cached
                processData: false,
                dataType: "json",
                beforeSend: function(xhr) {
                    for (key in requestHeader) {
                        xhr.setRequestHeader(key, requestHeader[key]);
                    }

                },
                success: function(data) {
                    resolve(data);
                },
                error: function(data) {
                    reject();
                }
            })
        })
    }


    function timeout(ms) { // promises
        return new Promise(resolve => setTimeout(resolve, ms));
    }

    const setBintangFinance = (JmlBintang) => {
        let html = '';
        let str = '';
        for (var i = 1; i <= JmlBintang; i++) {
            str += '*';
        }

        html = '<span style = "color:red;">' + str + '</span>';
        return html;
    };


    const IntakePaymentDetailDiscount = {
        htmlWr: (dataTuitionFee) => {
            const PaymentTypeData = IntakePaymentDetailDiscount.getPaymentTypeData(dataTuitionFee);
            let html = '<div class = "row">' +
                '<div class = "col-md-12">' +
                '<table class ="table table-striped table-bordered table-hover table-checkable tableData">' +
                '<thead>' +
                '<tr>';
            html += '<th>@</th>'
            for (var i = 0; i < PaymentTypeData.length; i++) {
                html += '<th>' + PaymentTypeData[i] + '</th>';
            }

            html += '</tr>' +
                '</thead>' +
                '<tbody>' +
                '<tr>' +
                '<td>Harga</td>';
            for (var i = 0; i < PaymentTypeData.length; i++) {
                for (key in dataTuitionFee) {
                    if (key == PaymentTypeData[i]) {
                        html += '<td style = "color:green;">Rp. ' + dataTuitionFee[key] + '</td>';
                        break;
                    }
                }
            }
            html += '</tr>';
            html += '<tr>' +
                '<td>Discount</td>';
            for (var i = 0; i < PaymentTypeData.length; i++) {
                for (key in dataTuitionFee) {
                    const s = key.split('-');

                    if (s[0] == 'Discount' && s[1] == PaymentTypeData[i]) {
                        html += '<td style = "font-weight:bold;">' + dataTuitionFee[key] + '%</td>';
                        break;
                    }
                }
            }
            html += '</tr>';

            html += '<tr>' +
                '<td>PotonganLain</td>';
            for (var i = 0; i < PaymentTypeData.length; i++) {
                for (key in dataTuitionFee) {
                    const s = key.split('-');

                    if (s[0] == 'PotonganLain' && s[1] == PaymentTypeData[i]) {
                        const dataPotongan = dataTuitionFee[key];
                        let liPot = '';
                        for (var z = 0; z < dataPotongan.length; z++) {
                            liPot += '<li style = "color:blue;">' + dataPotongan[z].DiscountName + ' : ' + formatRupiah(dataPotongan[z].DiscountValue) + '</li>';
                        }
                        html += '<td>' + liPot + '</td>';
                        break;
                    }
                }
            }
            html += '</tr>';
            html += '</tbody>' +
                '</table>' +
                '</div>' +
                '</div>';
            return html;
        },

        getPaymentTypeData: (dataTuitionFee) => {
            let PaymentTypeData = [];
            for (key in dataTuitionFee) {
                const s = key.split('-');
                if (s[0] == 'Discount') {
                    PaymentTypeData.push(s[1]);
                }
            }
            return PaymentTypeData;
        },
    };


    /*ADDED BY FEBRI @ FEB 2020*/
    function loadSelectOptionCountry(element, selected) {
        var url = base_url_js + 'api/__getCountry';
        $.getJSON(url, function(jsonResult) {
            $(element).append('<option>-- Select Country --</option>');
            $.each(jsonResult, function(i, v) {
                var sc = (selected == v.ctr_code) ? 'selected' : '';
                $(element).append('<option value="' + v.ctr_code + '" ' + sc + '>' + v.ctr_name + '</option>');
            })
            $(element).select2({
                'width': '100%'
            });
        });
    }
    /*END ADDED BY FEBRI @ FEB 2020*/

    /*ADDED BY Adhi @ Sep 2020*/
    const ValidationGenerate = {
        required: (val, theName) => {
            const chk = Validation_required(val, theName);
            if (chk.status == 0) {
                toastr.info(chk.messages);
                return false;
            }
            return true
        },

        moreZero: (val, theName) => {
            try {
                if (parseInt(val) <= 0) {
                    toastr.info(theName + ' have to more than zero');
                    return false;
                }

            } catch (err) {
                return false;
            }

            return true;
        },

        initializeProcess: (selector) => {
            let BoolProcess = true;
            selector.each(function(e) {
                const rule = ($(this).attr('rule')).split(',');
                const valueData = $(this).val();
                const theName = $(this).attr('name');
                for (var i = 0; i < rule.length; i++) {
                    const nameRule = rule[i];
                    if (nameRule != '' && nameRule !== undefined) {
                        try {
                            const pr = ValidationGenerate[nameRule](valueData, theName);
                            if (!pr) {
                                BoolProcess = false;
                                return;
                            }
                        } catch (err) {
                            BoolProcess = false;
                            return;
                        }
                    }
                }
            })

            if (!BoolProcess) {
                return false;
            }
            return true;
        },

        file_validation: (ev, TheName = '', max_upload_per_file = 4, allow_extension = ['pdf,jpeg,jpg,png'], max_size = 5000000) => {
            var files = ev[0].files;
            var error = '';
            var msgStr = '';
            if (files.length > 0) {
                if (files.length > max_upload_per_file) {
                    msgStr += 'Upload File ' + TheName + ' 1 Document should not be more than ' + max_upload_per_file + ' Files<br>';

                } else {
                    for (var count = 0; count < files.length; count++) {
                        var no = parseInt(count) + 1;
                        var name = files[count].name;
                        var extension = name.split('.').pop().toLowerCase();
                        if (jQuery.inArray(extension, ['pdf']) == -1) {
                            msgStr += 'Upload File ' + TheName + ' Invalid Type File<br>';
                        }

                        var oFReader = new FileReader();
                        oFReader.readAsDataURL(files[count]);
                        var f = files[count];
                        var fsize = f.size || f.fileSize;

                        if (fsize > max_size) {
                            msgStr += 'Upload File ' + TheName + ' Image File Size is very big<br>';
                        }

                    }
                }
            } else {
                msgStr += 'Upload File ' + TheName + ' Required';
            }
            return msgStr;
        },
    }
    /*END ADDED BY Adhi @ Sep 2020*/

    function loadSelectOptionEmployees_kelompok_profesi(selector, selected = '') {
        var url = base_url_js + 'api/__getemployees_kelompok_profesi';
        $.getJSON(url, function(jsonResult) {
            $.each(jsonResult, function(i, v) {

                var sc = (selected == v.ID_kelompok_profesi) ? 'selected' : '';
                selector.append('<option value="' + v.ID_kelompok_profesi + '" ' + sc + '>' + v.Name + '</option>');

            })
        });
    }

    function loadSelectOptionStatus_NITK(selector, selected = '') {
        loadSelectOptionEmployees_kelompok_profesi(selector, selected)
    }


    // Sample query like in es6
    // const arrayOfObject = [{ name: 'Paul', country: 'Canada', }, { name: 'Lea', country: 'Italy', }, { name: 'John', country: 'Italy' }];

    // console.log(filterLikeByValue(arrayOfObject, 'lea')); // [{name: 'Lea', country: 'Italy'}]
    // console.log(filterLikeByValue(arrayOfObject, 'ita')); // [{name: 'Lea', country: 'Italy'}, {name: 'John', country: 'Italy'}]
    function filterLikeByValue(array, string) {
        return array.filter(o =>
            Object.keys(o).some(k => o[k].toLowerCase().includes(string.toLowerCase())));
    }
</script>


<!-- ====== FIREBASE SETTING ====== -->

<!-- The core Firebase JS SDK is always required and must be listed first -->
<script src="https://www.gstatic.com/firebasejs/7.23.0/firebase-app.js"></script>
<script src="https://www.gstatic.com/firebasejs/7.23.0/firebase-messaging.js"></script>
<script src="https://www.gstatic.com/firebasejs/7.13.0/firebase-database.js"></script>

<!-- TODO: Add SDKs for Firebase products that you want to use
https://firebase.google.com/docs/web/setup#available-libraries -->

<script src="https://www.gstatic.com/firebasejs/7.23.0/firebase-analytics.js"></script>


<script>
    // Your web app's Firebase configuration
    // For Firebase JS SDK v7.20.0 and later, measurementId is optional
    var firebaseConfig = {
        apiKey: "AIzaSyDNEjQGVH_D0gdXJ17IB_AUs1Z2uCdzWJs",
        authDomain: "pals-194015.firebaseapp.com",
        databaseURL: "https://pals-194015.firebaseio.com",
        projectId: "pals-194015",
        storageBucket: "pals-194015.appspot.com",
        messagingSenderId: "652219708720",
        appId: "1:652219708720:web:cea24855ef4135ce9584f5",
        measurementId: "G-WKXRKEJ5XD"
    };
    // Initialize Firebase
    firebase.initializeApp(firebaseConfig);
    firebase.analytics();

    const messaging = firebase.messaging();
    // messaging.usePublicVapidKey("BELr-_XwFphNa8xc_VVgWqEBVHlDPFuZOGcvg_rF7PFIx3P0oriPnFCqMasfkh5TDSfjZmypdCht9iIDtMsy714");

    //
    // messaging.getToken().then((currentToken) => {
    //     // console.log(currentToken);
    //     updateFCMToken(currentToken);
    // }).catch((err) => {
    //     console.log('An error occurred while retrieving token. ', err);
    //     // showToken('Error retrieving Instance ID token. ', err);
    //     // setTokenSentToServer(false);
    // });

    messaging.requestPermission().then(function() {
            console.log('Have permission');
            return messaging.getToken();
        })
        .then(function(token) {
            updateFCMToken(token);
        })
        .catch(function(err) {
            console.log(err);
            console.log('Not have permission');
        });

    messaging.onMessage(function(payload) {
        const title = payload.notification.title;
        const icon = (typeof payload.notification.icon !== "undefined" && payload.notification.icon != '') ?
            payload.notification.icon : './images/icon/favicon.png';
        const options = {
            body: payload.notification.body,
            icon: icon
        };
        // toastr.info(payload.notification.body,title);

        var notification = new Notification(title, options);
    });
</script>


<!--Added by Adhi 2020-12-08 -->
<script>
    const success_message_response = (message) => {
        toastr.success(message);
    }
    const error_message_response = (message) => {
        toastr.info(message);
    }

    $(document).ready(function(e) {
        <?php
        if ($this->session->flashdata('form_response_status')) {
            echo $this->session->flashdata('form_response_status') . '_message_response("' . $this->session->flashdata('form_response_message') . '");';
        }
        ?>
    })

    const response_form = (response) => {
        // response = JSON.parse(response);
        if (response.redirect) {
            window.location = response.redirect;
        } else {
            if (response.message) {
                if (response.status == 'success') {
                    success_message_response(response.message);
                } else if (response.status == 'error') {
                    error_message_response(response.message);
                }
            }
        }

    }
</script>
<!--Added by Adhi 2020-12-08 -->