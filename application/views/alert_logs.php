<?php $this->load->view('layout/up'); ?>
<link href="<?php echo base_url("assets/css/bootstrap-datepicker.min.css"); ?>" rel="stylesheet">

<div class="col">
    <div class="row">
        <div class="col-1">
            <p class="font-weight-bold">Device:</p>
        </div>
        <div class="col-2">
            <select class="browser-default custom-select" id="device_id">
                <option value="0">All</option>
                <?php foreach (@$devices as $key => $value) { ?>
                    <option value="<?php echo $value["id"]; ?>"><?php echo $value["name"]; ?></option>
                <?php } ?>
            </select>
        </div>
        <div class="col-1">
            <p class="font-weight-bold">Location:</p>
        </div>
        <div class="col-2">
            <select class="browser-default custom-select" id="gatewey_id">
                <option value="0">All</option>
                <?php foreach (@$gateweys as $key => $value) { ?>
                    <option value="<?php echo $value["id"]; ?>"><?php echo $value["name"]; ?></option>
                <?php } ?>
            </select>
        </div>
        <div class="col-1">
            <p class="font-weight-bold">Users:</p>
        </div>
        <div class="col-2">
            <select class="browser-default custom-select" id="user_id">
                <option value="0">All</option>
                <?php foreach (@$users as $key => $value) { ?>
                    <option value="<?php echo $value["id"]; ?>"><?php echo $value["name"]; ?></option>
                <?php } ?>
            </select>
        </div>
    </div><br>
    <div class="row">
        <div class="col-2">
            <p class="font-weight-bold">Start Date : </p>
        </div>
        <div class="col-3"><input id="start" name="start" class="date-pick form-control" value="<?php echo date("m/d/Y"); ?>" data-date-format="mm/dd/yyyy" type="text"></div>
        <div class="col-2">
            <p class="font-weight-bold">Finish Date : </p>
        </div>
        <div class="col-3"><input id="finish" name="finish" class="date-pick form-control" value="<?php echo date("m/d/Y"); ?>" data-date-format="mm/dd/yyyy" type="text" /></div>
        <div class="col-1"><button id="gerReport" class="btn btn-success">Raporla</button></div>
        <div class="col-1"><img height="40px" src="https://de.marketscreener.com/images/loading_100.gif" style="display: none;" id="imgProgress1" /></div>
    </div>
</div>
<hr>
<div class="col-12" id="report" >

        <table id="dataTable" class="table table-striped table-bordered table-hover" style="width: 100%;">
            <thead style="text-align: center;">
                <tr>
                    <th>Alert Name</th>
                    <th>Device</th>
                    <th>Location</th>
                    <th>Suspend User Name</th>
                    <th>Suspend Date</th>
                    <th>Closed User Name</th>
                    <th>Closed Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody id="alert_rules">
                <?php foreach (@$alert_logs as $key => $alert_log) {
                    $alert_name = "";
                    $alert_rules_id = $alert_log["alert_rules_id"];
                    $device_id = $alert_log["device_id"];
                    $device_name = "";
                    $gateway_id = $alert_log["gateway_id"];
                    $gateway_name = "";
                    $suspended_user_id = $alert_log["suspended_user_id"];
                    $suspended_user_name = "";
                    $closed_user_id = $alert_log["closed_user_id"];
                    $closed_user_name = "";
                    $suspend_date = $alert_log["suspend_date"];
                    $close_date = $alert_log["close_date"];
                    $status = $alert_log["status"];
                    $status_name = "";

                    foreach (@$alert_rules as $key => $alert_rule) {
                        if ($alert_rule["id"] == $alert_rules_id) {
                            $alert_name = $alert_rule["name"];
                        }
                    }
                    foreach (@$gateweys as $key => $gatewey) {
                        if ($gatewey["id"] == $gateway_id) {
                            $gateway_name = $gatewey["name"];
                        }
                    }
                    foreach (@$devices as $key => $device) {
                        if ($device["id"] == $device_id) {
                            $device_name = $device["name"];
                        }
                    }
                    foreach (@$users as $key => $user) {
                        if ($user["id"] == $suspended_user_id) {
                            $suspended_user_name = $user["name"];
                        }
                        if ($user["id"] == $closed_user_id) {
                            $closed_user_name = $user["name"];
                        }
                    }
                    switch ($status) {
                        case "1":
                            $status_name = "Opened";
                            break;
                        case 2:
                            $status_name = "Suspended";
                            break;
                        case 3:
                            $status_name = "Closed";
                            break;
                        default:
                            # code...
                            break;
                    }
                    ?>
                        <tr>
                            <td><?php echo @$alert_name; ?></td>
                            <td><?php echo @$device_name; ?></td>
                            <td><?php echo @$gateway_name; ?></td>
                            <td><?php echo @$suspended_user_name; ?></td>
                            <td><?php echo @$suspend_date; ?></td>
                            <td><?php echo @$closed_user_name; ?></td>
                            <td><?php echo @$close_date; ?></td>
                            <td><?php echo @$status_name; ?></td>
                        </tr>
                    <?php } ?>
            </tbody>
        </table>

    
</div>
<div id="areport"></div>


<?php $this->load->view('layout/down'); ?>

<script src="<?php echo base_url("assets/js/moment-with-locales.js"); ?>"></script>
<script src="<?php echo base_url("assets/js/bootstrap-datepicker.min.js"); ?>"></script>
<script>
    $('.datepicker').datepicker({
        format: 'mm/dd/yyyy'
    });

    $('#gerReport').on('click', function(event) {
        $('#report').empty();
        var startDate = $('#start').val().split('/');
        //var startepoch = new Date(startDate[2], startDate[0] - 1, startDate[1], 00, 00, 00).getTime() / 1000;
        var finishDate = $('#finish').val().split('/');
        //var finishepoch = new Date(finishDate[2], finishDate[0] - 1, finishDate[1], 23, 59, 59).getTime() / 1000;
        var device_id = $('#device_id').val();
        var gatewey_id = $('#gatewey_id').val();
        var user_id = $('#user_id').val();
        
        if (device_id != null) {
            $("#imgProgress1").show();
            $("#areport").load("<?php echo base_url("alert")?>/get_logs?device_id="+device_id+ "&gatewey_id="+gatewey_id+"&user_id="+user_id+"&sDate=" + startDate + "&fDate=" + finishDate, function() {
                $("#imgProgress1").hide();
            });
        } else {
            alert('Select device');
        }
    });

    $('input.date-pick').datepicker().on('change', function(ev) {
        var firstDate = $(this).val();
    });

    $(document).ready(function() {
        $('#dataTable').DataTable();
    });
</script>