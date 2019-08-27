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
                        foreach ($device_list as $value) {
                            echo "<li><a href='history/userlog?mac=" . $value["mac"] . "'>" . $value["name"] . "</a></li>";
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
            </div>
        </div>
    </div>
</div>
<br>
<div class="row">
    <div class="col-lg-12 col-sm-12">
        <div style="font-size: small;">
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover">
                    <thead style="text-align: center;">
                        <tr style="background-color: #ff6961;">
                            <th style='padding: 7px;'>Alert</th>
                            <th style='padding: 7px;'>Time</th>
                            <th style='padding: 7px;'>Name</th>
                            <th style='padding: 7px;'>Location</th>
                            <th style='padding: 7px;'>Status</th>
                        </tr>
                    </thead>
                    <tbody id="alert">
                    </tbody>
                </table>
            </div>
        </div>


        <div style="font-size: small;">
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover">
                    <thead style="text-align: center;">
                        <tr style="background-color: #fdfd96;">
                            <th style='padding: 7px;'>Alert</th>
                            <th style='padding: 7px;'>Time</th>
                            <th style='padding: 7px;'>Name</th>
                            <th style='padding: 7px;'>Location</th>
                            <th style='padding: 7px;'>User Name</th>
                            <th style='padding: 7px;'>Status</th>
                        </tr>
                    </thead>
                    <tbody id="alertsuspend">
                    </tbody>
                </table>
            </div>
        </div>

        <div style="font-size: small;">
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover">
                    <thead style="text-align: center;">
                        <tr style="background-color: #77dd77;">
                            <th style='padding: 7px;'>Alert</th>
                            <th style='padding: 7px;'>Time</th>
                            <th style='padding: 7px;'>Name</th>
                            <th style='padding: 7px;'>Location</th>
                            <th style='padding: 7px;'>User Name</th>
                            <th style='padding: 7px;'>Status</th>
                        </tr>
                    </thead>
                    <tbody id="alertclosed">
                    </tbody>
                </table>
            </div>
        </div>

        <div id="stopalarm"></div>
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

<?php $this->load->view("layout/scripts"); ?>

<script>
    
    setInterval(function() {
        try {
            $("#log").load("dashboard/rtls");

            //table.ajax.reload();
            $("#alert").load("dashboard/alert");
            $("#gateways").load("dashboard/gateways");

            $("#alertsuspend").load("dashboard/getsuspendalert");
            $("#alertclosed").load("dashboard/getclosedalert");
        } catch (err) {
            console.log("error");
        }
    }, 10000);

</script>

<?php $this->load->view('layout/down'); ?>