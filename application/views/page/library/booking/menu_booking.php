<div class="tabbable tabbable-custom tabbable-full-width">
    <ul class="nav nav-tabs">
        <li>
            <a href="<?php echo base_url('library/booking/list'); ?>">List</a>
        </li>
    </ul>
    <div style="border-top: 1px solid #cccccc">

        <div class="row">
            <div class="col-md-12" style="margin-top: 30px;">
                <?php echo $page; ?>
            </div>
        </div>

    </div>
</div>

<script>
    $(document).ready(function() {

        var menu_active = "<?php echo $this->uri->segment(3); ?>";
        var arrMenu = ['list'];
        setMenuSelected('.nav-tabs', 'li', 'active', arrMenu, menu_active);

    });
</script>