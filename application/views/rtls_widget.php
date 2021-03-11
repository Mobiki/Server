<div class="col-md-12">
    <div class="card">
        <div class="card-header py-3">
            <?php
            $currentDate =  time();
            date("Y-m-d", $currentDate)
            ?><form id="rtls_filter">
                <div class="row">

                    <div class="col-md-1">
                        <h6 class="m-0 font-weight-bold text-primary"><?php echo date("d/m/Y", $currentDate); ?></h6>
                    </div>
                    <div class="col-md-2">


                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" id="status" name="status" id="inlineRadio1" value="2" checked>
                            <label class="form-check-label" for="inlineRadio1">All</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" id="status" name="status" id="inlineRadio2" value="3">
                            <label class="form-check-label" for="inlineRadio2"><b title="1" style="color:green;">⬤</b></label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" id="status" name="status" id="inlineRadio3" value="4">
                            <label class="form-check-label" for="inlineRadio3"><b title="0" style="color:red;">⬤</b></label>
                        </div>

                    </div>
                    <div class="col-md-2">
                        <select class="form-control form-control-sm btn-outline-secondary" id="personnel_id" name="personnel_id">
                            <option value="0">All Personnel</option>
                            <?php
                            foreach ($personnel as $key => $person) {
                                echo '<option value="' . $person["id"] . '">' . $person["name"] . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select class="form-control form-control-sm btn-outline-secondary" id="device_type_id" name="device_type_id">
                            <option value="0">All Devices</option>
                            <?php
                            foreach ($device_types as $key => $device_type) {
                                echo '<option value="' . $device_type["id"] . '">' . $device_type["name"] . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select class="form-control form-control-sm btn-outline-secondary" id="gateway_mac" name="gateway_mac">
                            <option value="0">All Locations</option>
                            <?php
                            foreach ($gateways as $key => $gateway) {
                                if ($gateway["status"] == 1) {
                                    echo '<option value="' . $gateway["mac"] . '">' . $gateway["name"] . '</option>';
                                }
                            }
                            ?>
                        </select>
                    </div>
                </div>
            </form>
        </div>
        <div class="card-body">
            <div class="table-responsive" id="datatable">
                <table id="dataTable" class="table table-striped table-bordered table-hover">
                    <thead>
                        <tr>
                            <td>Status</td>
                            <td>Update Date</td>
                            <td>Name</td>
                            <td>Location</td>
                            <td>Battery</td>
                            <td>Rssi</td>
                            <td>Detail</td>
                        </tr>
                    </thead>
                    <tbody id="rtls">
                        <tr>
                            <td colspan="7">Loading...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script src="<?php echo base_url("assets/js/jquery.min.js"); ?>"> </script>
<script>
    function get_data(start) {
        try {
            if (start == 0) {
                var jqxhr = $.get("<?php echo base_url("widgets/rtls"); ?>", function(data) {}).done(function() {
                        $("#rtls").html(jqxhr.responseText);
                        //console.log(jqxhr);
                    })
                    .fail(function() {
                        console.log("error");
                    });
            } else {
                var jqxhr = $.get("<?php echo base_url("widgets/rtls?"); ?>" + $("#rtls_filter").serialize(), function(data) {}).done(function() {
                    $("#rtls").html(jqxhr.responseText);
                    })
                    .fail(function() {
                        console.log("error");
                    });
            }
        } catch (err) {
            console.log("error");
        }
    }
    function get_rtls(start) {
        setInterval(function(start) {
            get_data(start);
        }, 10000);
    }
    get_rtls(0);
    $(".form-check-input").click(function() {
        var radioValue = $("input[name='status']:checked").val();
        if (radioValue) {
            get_rtls(1);
        }
    });
    $("#personnel_id").change(function() {
        get_rtls(2);
    });
    $("#device_type_id").change(function() {
        get_rtls(3);
    });
    $("#gateway_mac").change(function() {
        get_rtls(4);
    });
</script>