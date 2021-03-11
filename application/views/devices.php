<?php $this->load->view('layout/up') ?>

<ul class="nav nav-tabs" id="pills-tab" role="tablist">
    <label for="d_type" style="padding-top: 6px; margin-right: 16px;"> Device Type: </label><?php foreach (@$devices_type as $key => $dtvalue) { ?>
        <li class="nav-item">
            <a class="nav-link <?php if (@$dtvalue["id"] == "1") { ?>active<?php } ?>" id="pills-<?php echo @$dtvalue["id"]; ?>-tab" data-toggle="pill" href="#pills-<?php echo $dtvalue["id"]; ?>" role="tab" aria-selected="<?php if ($dtvalue["id"] == "1") { ?>true<?php } else { ?>false<?php } ?>"><?php echo $dtvalue["name"]; ?></a>
        </li>
    <?php } ?>

    <li class="nav-item">
        <a class="nav-link" id="pills-add-tab" data-toggle="modal" data-target='#addDeviceTypeModal' href="#pills-add" role="tab" aria-selected="false"><strong> + </strong></a>
    </li>
    <li class="nav-item">
        <a class="nav-link" id="btn_add_device" data-toggle='modal' data-target='#addDeviceModal' role="tab" aria-selected="false" style="color: white;background-color: #007bff;">Add Device</a>
    </li>
</ul>
<div class="tab-content" id="pills-tabContent">
    <?php foreach (@$devices_type as $key => $dtvalue) { ?>
        <div class="tab-pane fade <?php if (@$dtvalue["id"] == "1") { ?>show active<?php } ?>" id="pills-<?php echo @$dtvalue["id"]; ?>" role="tabpanel" aria-labelledby="pills-<?php echo $dtvalue["id"]; ?>-tab">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive" id="datatable">
                                <link rel="stylesheet" href="">
                                <table id="dataTable" class="table table-striped table-bordered table-hover" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <td>#</td>
                                            <td>Status</td>
                                            <td>Serial Number</td>
                                            <td>Name</td>
                                            <td>Description</td>
                                            <td>Type</td>
                                            <td>Update Date</td>
                                            <td>Edit</td>
                                        </tr>
                                    </thead>
                                    <tbody id="devices">
                                        <?php
                                            foreach (@$devices as $key => $value) {
                                                if (@$value["type_id"] == @$dtvalue["id"]) {
                                                    echo "<tr>";
                                                    echo "<td>" . @$value["id"] . "</td>";
                                                    if (@$value["status"] == 1) {
                                                        echo "<td>" . "<b style='color:green;'>&#11044;</b>" . "</td>";
                                                    } else {
                                                        echo "<td>" . "<b style='color:red;'>&#11044;</b>" . "</td>";
                                                    }
                                                    echo "<td>" . @$value["mac"] . "</td>";
                                                    echo "<td>" . @$value["name"] . "</td>";
                                                    echo "<td>" . @$value["description"] . "</td>";
                                                    echo "<td>" . @$dtvalue["name"] . "</td>";
                                                    echo "<td>" . @$value["update_date"] . "</td>";
                                                    echo "<td>" . "<button type='button' 
                                                    data-toggle='modal' 
                                                    data-target='#addDeviceModal' 
                                                    data-id='" . $value["id"] . "' 
                                                    data-mac='" . $value["mac"] . "' 
                                                    data-name='" . $value["name"] . "'  
                                                    data-description='" . $value["description"] . "' 
                                                    data-type_id='" . $value["type_id"] . "' 
                                                    data-status='" . $value["status"] . "' 
                                                    class='btn btn-success btn-sm'>Edit</button>" . "</td>";
                                                    echo "</tr>";
                                                }
                                            }
                                            ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>
</div>

<div class="modal fade bd-modal-lg" id="addDeviceTypeModal" tabindex="-1" role="dialog" aria-labelledby="addDeviceTypeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalLabel">Add Device Type</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body" id="addTypeModalBody">
                <form id="add_device_type_form" action="#" method="post">
                    <div class="row">
                        <div class="col-5">
                            <div class="form-group">
                                <label for="mac">Device Type Name</label>
                                <input type="text" class="form-control" id="device_type_name" name="name" placeholder="" value="">
                            </div>
                        </div>
                        <div class="col-5">
                            <div class="form-group">
                                <label for="name">Description</label>
                                <input type="text" class="form-control" id="device_type_description" name="description" placeholder="" value="">
                            </div>
                        </div>
                        <div class="col-2" style="vertical-align: middle">
                            <div class="form-group">
                                <button type="button" id="btn_add_type" class="btn btn-outline-primary btn-sm"><i class="fa fa-plus" aria-hidden="true"></i></button>
                            </div>
                        </div>
                    </div>
                </form>
                <hr>
                <div id="device_types_list">
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade bd-modal-lg" id="addDeviceModal" tabindex="-1" role="dialog" aria-labelledby="addDeviceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalLabel">Add Device</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body" id="addmodalbody">
                <form id="form_edit" action="devices/add" method="post">
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <input type="hidden" id="eid" name="id" value="">
                                <label for="mac">Device SN</label>
                                <input type="Devicesn" class="form-control" id="emac" name="mac" placeholder="" value="">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label for="name">Device Name</label>
                                <input type="Devicesn" class="form-control" id="ename" name="name" placeholder="" value="">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label for="description">Description</label>
                                <input type="Devicesn" class="form-control" id="edescription" name="description" placeholder="" value="">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label for="type_id">Device Type</label>
                                <select class="form-control" id="etype_id" name="type_id">
                                    <?php
                                    print_r($devices_type);
                                    foreach (@$devices_type as $key => $dtvalue) { ?>
                                        <option value="<?php echo $dtvalue["id"]; ?>"><?php echo $dtvalue["name"]; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <div class="custom-control custom-switch">
                                <input type="hidden" id="estatus" name="status" value="1" />
                                <input type="checkbox" class="custom-control-input" id="ecstatus" name="cstatus" checked>
                                <label class="custom-control-label" for="ecstatus">Active</label>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="form-group" style="text-align: right;">
                        <button type="submit" class="btn btn-primary" id="btn_device_add">Add Device</button>
                    </div>
                </form>

                <div class="row" id="delet_device" style="display:none;">
                    <div class="col-12">
                        <form action="<?php echo base_url('devices/delete'); ?>" method="post">
                            <input type="hidden" id="did" name="id" value="" required />
                            <button type="submit" class="btn btn-danger ">Delete Device</button>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<?php $this->load->view('layout/down') ?>

<script>
    $("#ecstatus").change(function() {
        if (this.checked) {
            $("#estatus").val("1");
        } else {
            $("#estatus").val("0");
        }
    });

    $('.btn-success').each(function() {
        var $this = $(this);

        $this.on("click", function() {
            $('#form_edit').attr('action', '<?php echo base_url('devices/edit'); ?>');
            $('#hid').val($(this).data('id'));
            $('#eid').val($(this).data('id'));
            $('#did').val($(this).data('id'));

            $('#emac').val($(this).data('mac'));
            $('#ename').val($(this).data('name'));
            $('#edescription').val($(this).data('description'));
            $('#etype_id').val($(this).data('type_id'));

            $('#estatus').val($(this).data('status'));

            if ($(this).data('status') == 1) {
                $('#ecstatus').prop('checked', true);
            } else {
                $('#ecstatus').prop('checked', false);
            }
            $('#btn_device_add').html("Save");
            $('#delet_device').show();
        });
    });

    $(document).ready(function() {
        $("#device_types_list").load("<?php echo base_url('devices/device_types_index'); ?>");
    });

    $(document).ready(function() {
        $('#dataTable').each(function() {
            var $this = $(this);
            $this.DataTable();
        });
    });


    $('#btn_add_device').on("click", function() {
        $('#form_edit').attr('action', '<?php echo base_url('devices/add'); ?>');
        $('#delet_device').hide();
        $('#eid').val("");
        $('#ename').val("");
        $('#estatus').val("1");
        $('#ecstatus').prop('checked', true);
        $('#edescription').val("");
        $('#btn_device_add').html("Add Device");
        $('#etype_id').find('option:eq(0)').prop('selected', true);
    });


    $('#btn_add_type').on("click", function() {
        if ($('#device_type_name').val() == "") {
            alert("Write a asset type");
        } else {
            var data = $('#add_device_type_form').serializeArray();
            $.post("<?php echo base_url('devices/add_device_type'); ?>", data);
            $('#device_type_name').val("");
            $("#device_types_list").load("<?php echo base_url('devices/device_types_index'); ?>");
            $("#device_types_list").load("<?php echo base_url('devices/device_types_index'); ?>");
        }

    });
</script>