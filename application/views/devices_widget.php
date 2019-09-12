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
                        <p class="text-right"><a href="<?php echo base_url("gateways"); ?>">Gateways</a></p>
                        <h4 class="text-right" id="gateway_count">
                            <span class='small'>Loading...</span>
                        </h4>
                    </div>
                </div>
            </div>
        </div>
        <hr>
        <div class="card-body">
            <div class="row">
                <div class="col-5">
                    <div class="icon-big text-center icon-warning">
                        <i class="fa fa-desktop fa-5x"></i>
                    </div>
                </div>
                <div class="col-7">
                    <div class="numbers">
                        <p class="text-right"><a href="<?php echo base_url("devices"); ?>">Devices</a></p>
                        <h4 class="text-right" id="device_count">
                            <span class='small'>Loading...</span>
                        </h4>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
<script>
    setInterval(function() {
        try {
            $("#device_count").load("<?php echo base_url("widgets/device_count"); ?>");
            $("#gateway_count").load("<?php echo base_url("widgets/gateway_count"); ?>");
        } catch (err) {
            console.log("error");
        }
    }, 15000);
</script>