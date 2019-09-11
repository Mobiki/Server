<?php $this->load->view('layout/up'); ?>

<div class="row">
    <div class="col-lg-3 col-sm-6">
        <div class="card card-stats">
            <div class="card-body">
                <div class="row">
                    <div class="col-5">
                        <div class="icon-big text-center icon-warning">
                            <i class="fa fa-microchip fa-5x"></i>
                        </div>
                    </div>
                    <div class="col-7">
                        <div class="numbers">
                            <p class="text-right">Gateways</p>
                            <h4 class="text-right" id="gatewayInfo"><span class="small" style="color:green" title="Active Gateways">3</span> / <span style="color:dodgerblue" title="ALL Registered Gateways">3</span></h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <div style="font-size: small;">
                    <ul id="gwList">
                    </ul>
                    <ul id="gateways">
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-sm-6">
        <div class="card card-stats">
            <div class="card-body">
                <div class="row">
                    <div class="col-5">
                        <div class="icon-big text-center icon-warning">
                            <i class="fa fa-desktop fa-5x"></i>
                        </div>
                    </div>
                    <div class="col-7">
                        <div class="numbers">
                            <p class="text-right">Devices</p>
                            <h4 class="text-right" id="devicesInfo"><span class="small" style="color:green" title="Active Devices">6</span> / <span style="color:dodgerblue" title="ALL Registered Devices">6</span></h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <div style="font-size: small;">
                    <ul id="dvList">
                        <?php
                        foreach (@$device_list as $value) {
                            echo "<li><a href='history/userlog?mac=" . @$value["mac"] . "'>" . @$value["name"] . "</a></li>";
                        }

                        ?>
                        <li><a href="devices">All Devices</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-6 col-md-12 col-sm-12">
        <div class="card card-stats">
            <div class="card-body">
Live Map
            </div>
            <div class="card-footer">
            <iframe src="<?php echo base_url('livemap/widget'); ?>" width="100%" height="300px" style="border: 0px"></iframe>
            </div>
        </div>
    </div>
</div>
<br>




<div class="row">
    <div class="col-12" id="alert_widget_id" data-test="Testing">
        <div class="card card-stats">
            <div class="card-body" id="alert_body">


                Alerts <button class="btn btn-outline-warning btn-sm" id="collaps" data-size="12" data-widget_name="alert_widget">_</button>
                <button class="btn btn-outline-warning btn-sm" id="smaller" data-size="12" data-widget_name="alert_widget">-</button>
                <button class="btn btn-outline-warning btn-sm" id="bigger" data-size="12" data-widget_name="alert_widget">+</button>


            </div>
        </div>
        <div class="card-footer" id="alert_widget_footer" style="padding: 0px;" style="display: block;">


            <?php $this->load->view("alert_widget"); ?>


        </div>
    </div>
</div>




<br>
<br>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header py-3">
                <?php
                $currentDate =  time();
                date("Y-m-d", $currentDate)
                ?>
                <h6 class="m-0 font-weight-bold text-primary"><?php echo date("d/m/Y", $currentDate); ?></h6>
            </div>
            <div class="card-body">
                <section class="example">
                    <div class="table-responsive" id="datatable">
                        <link rel="stylesheet" href="">


                        <table id="dataTable" class="table table-striped table-bordered table-hover">
                            <thead>
                                <tr>
                                    <td></td>
                                    <td>Update Date</td>
                                    <td>Name</td>
                                    <td>Location</td>
                                    <td>Battery</td>
                                    <td>Rssi</td>
                                    <td>Light</td>
                                    <td>ACC</td>
                                    <td>Temperature</td>
                                    <td>Humidity</td>
                                    <td>Click</td>
                                </tr>
                            </thead>
                            <tbody id="log">

                            </tbody>
                        </table>

                    </div>
                </section>
            </div>
        </div>
    </div>
</div>

<div id="dashboard"></div>


<?php $this->load->view('layout/down'); ?>

<script>
    setInterval(function() {
        try {
            $("#log").load("dashboard/rtls");

            //table.ajax.reload();
            $("#gateways").load("dashboard/gateways");

        } catch (err) {
            console.log("error");
        }
    }, 10000);

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