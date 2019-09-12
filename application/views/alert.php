<?php $this->load->view('layout/up'); ?>

<div class="card">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary"> <button type="button" data-toggle='modal' data-target='#addAlertModal' class="btn btn-primary btn-sm" id="btn_add_alert">Add Alert Rule </button></h6>
    </div>
    <div class="card-body">
        <div style="font-size: small;">

            <table id="dataTable" class="table table-striped table-bordered table-hover">
                <thead style="text-align: center;">
                    <tr>
                        <th style='padding: 7px;'>Name</th>
                        <th style='padding: 7px;'>Device</th>
                        <th style='padding: 7px;'>Sensor Type</th>
                        <th style='padding: 7px;'>Sensor Value
                            <br>C/L/T/H > /123/1/*/*</th>
                        <th style='padding: 7px;'>Equation</th>
                        <th style='padding: 7px;'>Edit</th>
                    </tr>
                </thead>
                <tbody id="alert_rules">
                    <?php foreach (@$alert_rules as $key => $value) { ?>
                        <tr>
                            <td><?php echo @$value["name"]; ?></td>
                            <td><?php foreach (@$devices as $key => $dvalue) {
                                        if (@$dvalue["id"] == $value["device_id"]) {
                                            echo $dvalue["name"];
                                        }
                                    }

                                    if (@$value["device_id"] == 0) {
                                        echo "All";
                                    } ?></td>
                            <td><?php
                                    foreach (@$devices_type as $key => $dtvalue) {
                                        if (@$dtvalue["id"] == @$value["device_type"]) {
                                            echo @$dtvalue["name"];
                                        }
                                    }
                                    ?>
                            </td>
                            <td><?php echo @$value["sensor_value"]; ?></td>
                            <td><?php
                                    if (@$value["equation"] == 0) {
                                        echo "=";
                                    }
                                    if (@$value["equation"] == 1) {
                                        echo "&gt;";
                                    }
                                    if (@$value["equation"] == 2) {
                                        echo "&lt;";
                                    }
                                    ?></td>
                            <td>
                                <button type="button" class="btn btn-success btn-sm" data-toggle='modal' data-target='#addAlertModal' data-id="<?php echo @$value["id"]; ?>" data-name="<?php echo @$value["name"]; ?>" data-device_id="<?php echo @$value["device_id"]; ?>" data-device_type="<?php echo @$value["device_type"]; ?>" data-sensor_value="<?php echo @$value["sensor_value"]; ?>" data-equation="<?php echo @$value["equation"]; ?>">Edit</button>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>

        </div>
    </div>
</div>



<div class="modal fade bd-modal-lg" id="addAlertModal" tabindex="-1" role="dialog" aria-labelledby="addAlertModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal_title">Add Alert</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body" id="addmodalbody">
                <form id="form_edit" action="alert/add" method="post">
                    <input type="hidden" id="eid" name="id" value="" />
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label for="name">Alert Name</label>
                                <input type="text" class="form-control" id="ename" name="name" placeholder="">
                            </div>
                        </div>
                        <div class="col-6">
                        </div>
                    </div>

                    <div class="row" id="select_device_type">
                        <div class="col-6">
                            <div class="form-group">
                                <label for="device_id">Device</label>
                                <?php if (count($devices) > 0) { ?>
                                    <select class="form-control" id="edevice_id" name="device_id">
                                        <option value="0">All</option>
                                        <?php foreach ($devices as $key => $dvalue) {  ?>
                                            <option value="<?php echo $dvalue["id"]; ?>"><?php echo $dvalue["name"]; ?></option>
                                        <?php } ?>
                                    </select>
                                <?php } else { ?>
                                    <p><a href="devices">Add a device</a></p>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label for="device_type">Device Type</label>
                                <?php if (count($devices_type) > 0) { ?>
                                    <select class="form-control" id="edevice_type" name="device_type">
                                        <?php foreach ($devices_type as $key => $dvalue) {  ?>
                                            <option value="<?php echo $dvalue["id"]; ?>"><?php echo $dvalue["name"]; ?></option>
                                        <?php } ?>
                                    </select>
                                <?php } else { ?>
                                    <p><a href="devices">Add a device type</a></p>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label for="device_id">Sensor Value </label>
                                <input type="text" class="form-control" id="esensor_value" name="sensor_value" placeholder="C/L/T/H > /123/1/*/*">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label for="device_id">Equation</label>
                                <select class="form-control" id="eequation" name="equation">
                                    <option value="0">=</option>
                                    <option value="1">&gt;</option>
                                    <option value="2">&lt;</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group" style="text-align: right;">
                                <?php if (count($devices) > 0 && count($devices_type) > 0) { ?>
                                    <button type="submit" class="btn btn-primary" id="btn_add_alert_rule">Add Alert Rule</button>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </form>
                <div class="row" id="delet_alert" style="display:none;">
                    <div class="col-12">
                        <form action="alert/delete" method="post">
                            <input type="hidden" id="did" name="id" value="" />
                            <button type="submit" class="btn btn-danger">Delete</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('layout/down') ?>

<script type="text/javascript">
    $(document).ready(function() {
        $('#dataTable').DataTable();
    });

    $('.btn-success').each(function() {
        var $this = $(this);
        $this.on("click", function() {
            $('#form_edit').attr('action', '<?php echo base_url("alert/edit"); ?>');
            $('#modal_title').html("Edit Alert");
            $('#btn_add_alert_rule').html("Save");
            $('#eid').val($(this).data('id'));
            $('#did').val($(this).data('id'));
            $('#ename').val($(this).data('name'));
            $('#edevice_id').val($(this).data('device_id'));
            $('#edevice_type').val($(this).data('device_type'));
            $('#esensor_value').val($(this).data('sensor_value'));
            $('#eequation').val($(this).data('equation'));
            $('#delet_alert').show();
        });
    });

    $('#btn_add_alert').on("click", function() {

        $('#modal_title').html("Add Alert");
        $('#btn_add_alert_rule').html("Add Alert Rule");
        $('#form_edit').attr('action', '<?php echo base_url("alert/add"); ?>');
        $('#eid').val("");
        $('#ename').val("");
        $('#edevice_id').val("");
        $('#edevice_type').val("");
        $('#esensor_value').val("");
        $('#eequation').val("");
        $('#delet_alert').hide();
    });
</script>