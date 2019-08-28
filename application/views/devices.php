<?php $this->load->view('layout/up') ?>


<?php //print_r($devices_type); 
?>
<ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
    <?php foreach ($devices_type as $key => $dtvalue) { ?>
    <li class="nav-item">
        <a class="nav-link <?php if ($dtvalue["id"] == "1") { ?>active<?php } ?>" id="pills-<?php echo $dtvalue["id"]; ?>-tab" data-toggle="pill" href="#pills-<?php echo $dtvalue["id"]; ?>" role="tab" aria-selected="<?php if ($dtvalue["id"] == "1") { ?>true<?php } else { ?>false<?php } ?>"><?php echo $dtvalue["name"]; ?></a>
    </li>
    <?php } ?>

    <li class="nav-item">
        <a class="nav-link <?php if (count($devices_type) == 0) {
                                echo "active";
                            } ?>" id="pills-add-tab" data-toggle="modal" data-target='#addDeviceTypeModal' href="#pills-add" role="tab" aria-selected="false"><strong> + </strong></a>
    </li>
</ul>
<div class="tab-content" id="pills-tabContent">
    <?php foreach ($devices_type as $key => $dtvalue) { ?>
    <div class="tab-pane fade <?php if ($dtvalue["id"] == "1") { ?>show active<?php } ?>" id="pills-<?php echo $dtvalue["id"]; ?>" role="tabpanel" aria-labelledby="pills-<?php echo $dtvalue["id"]; ?>-tab">

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header py-3">
                        <?php
                            ?>
                        <h6 class="m-0 font-weight-bold text-primary"><button type="button" data-toggle='modal' data-target='#addDeviceModal' class="btn btn-primary btn-sm">Add Devices </button> - <button type="button" class="btn btn-secondary btn-sm" onclick="sendtoredis()">Update</button> </h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive" id="datatable">
                            <link rel="stylesheet" href="">


                            <table id="dataTable" class="table table-striped table-bordered table-hover">
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
                                <tbody id="gateways">
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
                                                echo "<td>" . "<button type='button' data-toggle='modal' data-target='#editDeviceModal' data-id='" . $value["id"] . "' data-mac='" . $value["mac"] . "' data-name='" . $value["name"] . "'  data-description='" . $value["description"] . "' data-type_id='" . $value["type_id"] . "' data-status='" . $value["status"] . "' class='btn btn-success btn-sm'>Edit</button>" . "</td>";
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
            <div class="modal-body" id="addmodalbody">
                <form action="devices/add_device_type" method="post">
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label for="mac">Device Type Name</label>
                                <input type="text" class="form-control" id="name" name="name" placeholder="">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label for="name">Description</label>
                                <input type="text" class="form-control" id="description" name="description" placeholder="">
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary btn-sm">Add Device Type</button>
                    </div>
                </form>
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
                <form action="devices/add" method="post">
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label for="mac">Device SN</label>
                                <input type="Devicesn" class="form-control" id="mac" name="mac" placeholder="">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label for="name">Device Name</label>
                                <input type="Devicesn" class="form-control" id="name" name="name" placeholder="">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label for="description">Description</label>
                                <input type="Devicesn" class="form-control" id="description" name="description" placeholder="">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label for="type_id">Device Type</label>
                                <select class="form-control" id="type_id" name="type_id">
                                    <?php foreach ($devices_type as $key => $dtvalue) { ?>
                                    <option value="<?php echo $dtvalue["id"]; ?>"><?php echo $dtvalue["name"]; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <div class="custom-control custom-switch">
                                <input type="hidden" id="hstatus" name="hstatus" value="1" />
                                <input type="checkbox" class="custom-control-input" id="status" name="status" checked>
                                <label class="custom-control-label" for="status">Active</label>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary btn-sm">Add Device</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<div class="modal fade bd-modal-lg" id="editDeviceModal" tabindex="-1" role="dialog" aria-labelledby="editGatewayModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalLabel">Edit Device</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body" id="editmodalbody">
                <form action="devices/edit" method="post">
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <input type="hidden" id="eid" name="eid">
                                <label for="emac">Device Serial No</label>
                                <input type="text" class="form-control" id="emac" name="emac">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label for="name">Device Name</label>
                                <input type="text" class="form-control" id="ename" name="ename">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label for="edescription">Description</label>
                                <input type="text" class="form-control" id="edescription" name="edescription">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label for="etype_id">Device Type</label>
                                <select class="form-control" id="etype_id" name="etype_id">
                                    <?php foreach ($devices_type as $key => $dtvalue) { ?>
                                    <option value="<?php echo $dtvalue["id"]; ?>"><?php echo $dtvalue["name"]; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <div class="custom-control custom-switch">
                                <input type="hidden" name="hestatus" id="hestatus" />
                                <input type="checkbox" class="custom-control-input" id="chestatus" name="chestatus" />
                                <label class="custom-control-label" for="chestatus">Active</label>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-info btn-sm">Edit Device</button>
                </form>
                <hr>
                <div class="row">
                    <div class="col-6">
                        <form action="devices/delete" method="post">
                            <input type="hidden" id="hmac" name="hmac" value="" />
                            <input type="hidden" id="hid" name="hid" value="" />
                            <button type="submit" class="btn btn-primary btn-sm">Delete Device</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="dv"></div>
<?php $this->load->view('layout/down') ?>


<script>
    $("#status").change(function() {
        if (this.checked) {
            $("#hstatus").val("1");
        } else {
            $("#hstatus").val("0");
        }
    });


    $("#chestatus").change(function() {
        if (this.checked) {
            $("#hestatus").val("1");
        } else {
            $("#hestatus").val("0");
        }
    });
    $('.btn-success').each(function() {
        var $this = $(this);

        $this.on("click", function() {
            $('#hid').val($(this).data('id'));
            $('#hmac').val($(this).data('mac'));
            $('#eid').val($(this).data('id'));
            $('#emac').val($(this).data('mac'));
            $('#ename').val($(this).data('name'));
            $('#etype_id').val($(this).data('type_id'));
            $('#estatus').val($(this).data('status'));
            if ($(this).data('status') == 1) {
                chestatus
                $('#chestatus').prop('checked', true);
            } else {
                $('#chestatus').prop('checked', false);
            }
            $('#edescription').val($(this).data('description'));
        });
    });


    function sendtoredis() {
        $("#dv").load("devices/toredis");
    }
</script>