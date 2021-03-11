<?php $this->load->view('layout/up'); ?>
<style>
    .zone-card{
        border: solid;
        border-color: white;
    }
    </style>
<div class="row">
    <?php $this->load->view("devices_widget"); ?>
    <?php $this->load->view("zone_widget"); ?>
    <?php $this->load->view("alert_widget"); ?>
    

    <div class="col-lg-3 col-md-6 col-sm-12">
        <div class="card card-stats">
            <div class="card-body">
                Live Map
            </div>
            <div class="card-footer">
                <iframe src="<?php echo base_url('widgets/live_map'); ?>" width="100%" height="300px" style="border: 0px"></iframe>
            </div>
        </div>
    </div>
</div>
<br>
<br>
<div class="row">
    <?php $this->load->view("rtls_widget"); ?>
</div>



<?php $this->load->view('layout/down'); ?>

<script>
    $('#collaps').each(function() {
        var $this = $(this);

        $this.on("click", function() {
            var widget_name = $(this).data('widget_name');
            if ($('#' + widget_name + '_footer').is(":hidden")) {
                $('#' + widget_name + '_footer').show();
            } else {
                $('#' + widget_name + '_footer').hide();
            }
        });
    });

    $('#smaller').each(function() {
        var $this = $(this);
        $this.on("click", function() {
            //if ($(this).data('size') > 1) {
            var size = $(this).data('size');
            var widget_name = $(this).data('widget_name');
            $("#" + widget_name + "_id").removeClass();
            size = size - 1;
            $("#" + widget_name + "_id").addClass("col-" + size);
            $(this).data('size', size);
            $(this).next('#bigger').data('size', size);
            //}

        });
    });

    $('#bigger').each(function() {
        var $this = $(this);
        $this.on("click", function() {
            //if ($(this).data('size') < 12) {
            var size = $(this).data('size');

            var widget_name = $(this).data('widget_name');
            $("#" + widget_name + "_id").removeClass();
            size = size + 1;
            $("#" + widget_name + "_id").addClass("col-" + size);
            $(this).data('size', size);
            $(this).next('#smaller').data('size', size);
            //}

        });
    });
</script>