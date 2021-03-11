<div class="col-lg-3 col-md-6 col-sm-12">
    <div class="row" id="zones"  style="margin:0px;color: white;">
<p style="color: black;">Loading...</p>
    </div>
</div>
<script src="<?php echo base_url("assets/js/jquery.min.js"); ?>"> </script>

<script>
    setInterval(function() {

        var color=["17a2b8","28a745","ffc107","dc3545","da9833","42b883","ff7e67","3c70a4","da9833","233714","6d0c74","fbc1bc","b2e4d5","f2a6a6","d2fafb"]

        var jqxhr = $.getJSON("<?php echo base_url("widgets/zone_widget"); ?>", function() {}).done(function() {
                var data = jqxhr.responseText;
                $("#zones").html("");
                $.each(JSON.parse(data), function(i, item) {
                    //if (item.parent_id == 0) {
                    var gateways = item.gateways;
                    var active_device_count = 0;
                    var total_device_count = 0;
                    $.each(gateways, function(j, gateway) {
                        console.log(gateway.devices);
                        var devices = gateway.devices
                        $.each(devices, function(j, device) {
                            var now = Date.now();
                            var time_def = now / 1000 - device.epoch
                            if (time_def < 30) {
                                active_device_count++;
                            }
                            total_device_count++;
                        });
                    });

                $("#zones")
                .append('<div class="card col- zone-card" style="border-radius: 5px;background-color: #'+color[i]+'!important;"><div class="card-body"><h4 class="card-title">' + active_device_count + ' / ' + total_device_count + '</h4><p class="card-text" style="font-size: small;line-height: 1.5em;white-space: nowrap;">' + item.name + '</p> </div></div>')
            });
            })
            .fail(function() {
                console.log("error");
            })
            .always(function() {
                console.log("complete");
            });
    }, 10000);
</script>