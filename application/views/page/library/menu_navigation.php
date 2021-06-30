<div id="sidebar" class="sidebar-fixed">
    <div id="sidebar-content">
        <!--=== Navigation ===-->
        <ul id="nav">
            <!-- list menu -->
            <li class="<?php if ($this->uri->segment(2) == 'yudisium') {
                            echo "current";
                        } ?>">
                <a href="<?php echo base_url('library/yudisium/final-project'); ?>">
                    <i class="fa fa-graduation-cap"></i>
                    Judiciums Monitoring
                </a>
            </li>

            <li class="<?php if ($this->uri->segment(2) == 'booking') {
                            echo "current";
                        } ?>">
                <a href="<?php echo base_url('library/booking/list'); ?>">
                    <i class="fa fa-th-list"></i>
                    Booking List
                </a>
            </li>
        </ul>
        <div class="sidebar-widget align-center">
            <div class="btn-group" data-toggle="buttons" id="theme-switcher">
                <label class="btn active">
                    <input type="radio" name="theme-switcher" data-theme="bright"><i class="fa fa-sun-o"></i> Bright
                </label>
                <label class="btn">
                    <input type="radio" name="theme-switcher" data-theme="dark"><i class="fa fa-moon-o"></i> Dark
                </label>
            </div>
        </div>
    </div>
    <div id="divider" class="resizeable"></div>
</div>
<!-- /Sidebar -->