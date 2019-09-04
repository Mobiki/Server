<?php $this->load->view('layout/up') ?>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <button type="button" id="btn_add_asset" data-toggle='modal' data-target='#addAssetModal' class="btn btn-primary btn-sm">Add Asset</button></button></h6>
            </div>
            <div class="card-body">
                <link rel="stylesheet" href="">
                <table id="dataTable" class="table table-striped table-bordered table-hover" style="width: 100%;">
                    <thead>
                        <tr>
                            <td style="width: 56px;">Status</td>
                            <td>Name</td>
                            <td>Image</td>
                            <td>Stock Code</td>
                            <td>Serial Number</td>
                            <td>Type</td>
                            <td>Manufacturer</td>
                            <td>Department</td>
                            <td>Personnel</td>
                            <td>Device</td>
                            <td>Date Added</td>
                            <td>Date Modified</td>
                            <td>Edit</td>
                        </tr>
                    </thead>
                    <tbody id="assets">
                        <?php
                        foreach (@$assets as $key => $value) {
                            if (@$value["department_id"] == 0) {
                                $department_name = "";
                            } else {
                                foreach (@$departments as $key => $dvalue) {
                                    if ($dvalue["id"] == $value["department_id"]) {
                                        $department_name = $dvalue["name"];
                                        break;
                                    } else {
                                        $type_name = "";
                                    }
                                }
                            }
                            if (@$value["personnel_id"] == 0) {
                                $personnel_name = "";
                            } else {
                                foreach (@$personnel as $key => $dvalue) {
                                    if ($dvalue["id"] == $value["personnel_id"]) {
                                        $personnel_name = $dvalue["name"];
                                        break;
                                    } else {
                                        $type_name = "";
                                    }
                                }
                            }
                            if (@$value["type_id"] == 0) {
                                $type_name = "";
                            } else {
                                foreach (@$asset_type as $key => $dvalue) {
                                    if ($dvalue["id"] == $value["type_id"]) {
                                        $type_name = $dvalue["name"];
                                        break;
                                    } else {
                                        $type_name = "";
                                    }
                                }
                            }
                            if (@$value["device_id"] == 0) {
                                $device_name = "";
                            } else {
                                foreach (@$devices as $key => $dvalue) {
                                    if ($dvalue["id"] == $value["device_id"]) {
                                        $device_name = $dvalue["name"];
                                        break;
                                    } else {
                                        $type_name = "";
                                    }
                                }
                            }
                            echo "<tr>";
                            if (@$value["status"] == 1) {
                                echo "<td>" . "<b style='color:green;'>&#11044;</b>" . "</td>";
                            } else {
                                echo "<td>" . "<b style='color:red;'>&#11044;</b>" . "</td>";
                            }
                            echo "<td>" . @$value["name"] . "</td>";
                            echo "<td>" . "<img style='height: 35px;' src='" . base_url('assets/images/assets/' . @$value["image"])  . "'/></td>";
                            echo "<td>" . @$value["stock_code"] . "</td>";
                            echo "<td>" . @$value["serial_number"] . "</td>";
                            echo "<td>" . $type_name . "</td>";
                            echo "<td>" . @$value["manufacturer"] . "</td>";
                            echo "<td>" . @$department_name . "</td>";
                            echo "<td>" . @$personnel_name . "</td>";
                            echo "<td>" . @$device_name . "</td>";
                            echo "<td>" . @$value["date_added"] . "</td>";
                            echo "<td>" . @$value["date_modified"] . "</td>";
                            echo "<td>" . "<button type='button' 
                                data-toggle='modal' 
                                data-target='#addAssetModal' 
                                data-id='" . @$value["id"] . "' 
                                data-name='" . @$value["name"] . "' 
                                data-image='" . @$value["image"] . "' 
                                data-stock_code='" . @$value["stock_code"] . "' 
                                data-serial_number='" . @$value["serial_number"] . "' 
                                data-manufacturer='" . @$value["manufacturer"] . "' 
                                data-description='" . @$value["description"] . "' 
                                data-department_id='" . @$value["department_id"] . "' 
                                data-personnel_id='" . @$value["personnel_id"] . "' 
                                data-type_id='" . @$value["type_id"] . "' 
                                data-device_id='" . @$value["device_id"] . "' 
                                data-status='" . @$value["status"] . "' 
                            class='btn btn-success btn-sm'>Edit</button>" . "</td>";
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


<div class="modal hide fade" tabindex="-1" role="dialog" id="AssetTypesModal" aria-labelledby="myLargeModalLabel" aria-hidden="true" data-focus-on="input:first">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="asset_types_title">Asset Types</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="asset_types_modal_body">
                <form id="add_asset_type_form" method="post">
                    <div class="row">
                        <div class="col-8">
                            <div class="form-group">
                                <input type="text" class="form-control" id="asset_type_name" name="name" placeholder="" value="" />
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <button type="button" id="btn_add_type" class="btn btn-outline-primary btn-sm"><i class="fa fa-plus" aria-hidden="true"></i></button>
                            </div>
                        </div>
                    </div>
                </form>
                <div id="assete_types_list">

                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal hide fade bd-modal-lg" tabindex="-1" role="dialog" id="addAssetModal" aria-labelledby="myLargeModalLabel" aria-hidden="true" data-focus-on="input:first">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modal_title">Add Assets</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="form_edit" action="<?php echo base_url('assets/add'); ?>" enctype="multipart/form-data" method="post" accept-charset="utf-8">
                    <input type="hidden" id="eid" name="id" value="" />
                    <div class="row">
                        <div class="col-3">
                            <div class="form-group">
                                <label for="image">Asset photo</label>
                                <input type="file" class="form-control-file" id="eimage" size="20" name="userfile" title="gif|jpg|png|jpeg" />
                            </div>
                        </div>
                        <div class="col-3">
                            <img id="asset_photo" class="img-thumbnail" src="<?php echo base_url('assets/images/assets/default-asset-image.jpg'); ?>" />
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label for="name">Name</label>
                                <input type="text" class="form-control" id="ename" name="name" placeholder="" value="" required />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label for="stock_code">Stock Code</label>
                                <input type="text" class="form-control" id="estock_code" name="stock_code" placeholder="" value="" />
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group" id="">
                                <label for="serial_number">Serial Number</label>
                                <input type="text" class="form-control" id="eserial_number" name="serial_number" placeholder="" value="" />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label for="manufacturer">Manufacturer</label>
                                <input type="text" class="form-control" id="emanufacturer" name="manufacturer" placeholder="" value="" />

                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label for="type_id">Type - <a href="#" data-dismiss="modal" data-toggle="modal" data-target="#AssetTypesModal">Add Type</a></label>
                                <select class="form-control" id="etype_id" name="type_id">

                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label for="description">Description</label>
                                <input type="text" class="form-control" id="edescription" name="description" placeholder="" value="" />
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label for="device_id">Devices</label>
                                <select class="form-control" id="edevice_id" name="device_id">
                                    <option value="0">None</option>
                                    <?php foreach (@$devices as $key => $dtvalue) { ?>
                                        <option value="<?php echo $dtvalue["id"]; ?>"><?php echo $dtvalue["name"]; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label for="department_id">Departments</label>
                                <select class="form-control" id="edepartment_id" name="department_id" required>
                                    <?php foreach (@$departments as $key => $dtvalue) { ?>
                                        <option value="<?php echo $dtvalue["id"]; ?>"><?php echo $dtvalue["name"]; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label for="personnel_id">Personnel</label>
                                <select class="form-control" id="epersonnel_id" name="personnel_id">
                                    <option value="0">None</option>
                                    <?php foreach (@$personnel as $key => $dtvalue) { ?>
                                        <option value="<?php echo $dtvalue["id"]; ?>"><?php echo $dtvalue["name"]; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group" style="text-align: right;">
                                <button type="submit" class="btn btn-primary" id="btn_asset_add">Add Assets</button>

                            </div>
                        </div>
                    </div>
                </form>
                <div class="row" id="delet_asset" style="display:none;">
                    <div class="col-12">
                        <form action="<?php echo base_url('assets/delete'); ?>" method="post">
                            <input type="hidden" id="did" name="id" value="" />
                            <button type="submit" class="btn btn-danger">Delete Assets</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('layout/down') ?>
<script>
    $('.btn-success').each(function() {
        var $this = $(this);
        $this.on("click", function() {
            $('#form_edit').attr('action', '<?php echo base_url('assets/edit'); ?>');
            $('#modal_title').html("Edit Asset");
            $('#btn_asset_add').html("Edit Asset");
            $('#eid').val($(this).data('id'));
            $('#ename').val($(this).data('name'));
            $("#asset_photo").attr("src", "<?php echo base_url('assets/images/assets/'); ?>" + $(this).data('image'));
            $('#estock_code').val($(this).data('stock_code'));
            $('#eserial_number').val($(this).data('serial_number'));
            $('#emanufacturer').val($(this).data('manufacturer'));
            $('#edescription').val($(this).data('description'));
            $('#edepartment_id').val($(this).data('department_id'));
            $('#epersonnel_id').val($(this).data('personnel_id'));
            $('#edevice_id').val($(this).data('device_id'));

            $('#did').val($(this).data('id'));
            $('#delet_asset').show();


            $("#etype_id").load("<?php echo base_url('assets/list_asset_type?id='); ?>"+$(this).data('type_id'), function(response, status, xhr) {
                if (status == "error") {
                    var msg = "Sorry but there was an error: ";
                    alert("Error");
                }
            });
            

        });
    });

    $('#btn_add_asset').on("click", function() {
        $('#modal_title').html("Add Asset");
        $('#btn_asset_add').html("Add Asset");
        $('#form_edit').attr('action', '<?php echo base_url('assets/add'); ?>');
        $('#eid').val("");
        $('#ename').val("");
        $('#estock_code').val("");
        $('#eserial_number').val("");
        $('#emanufacturer').val("");
        $('#edescription').val("");
        $('#edepartment_id').val(0);
        $('#epersonnel_id').val(0);
        $('#edevice_id').val(0);

        $('#eid').val("");
        $('#delet_asset').hide();


        $("#etype_id").load("<?php echo base_url('assets/list_asset_type'); ?>");
        $('#etype_id').val(0);
    });

    $(document).ready(function() {
        $('#dataTable').DataTable();
    });


    $(document).ready(function() {
        $("#assete_types_list").load("<?php echo base_url('assets/assete_types_index'); ?>");
    });
    $('#btn_add_type').on("click", function() {
        if ($('#asset_type_name').val() == "") {
            alert("Write a asset type");
        } else {
            var data = $('#add_asset_type_form').serializeArray();
            $.post("<?php echo base_url('assets/add_asset_type'); ?>", data);
            $('#asset_type_name').val("");
            $("#assete_types_list").load("<?php echo base_url('assets/assete_types_index'); ?>");
            $("#assete_types_list").load("<?php echo base_url('assets/assete_types_index'); ?>");
        }

    });
</script>