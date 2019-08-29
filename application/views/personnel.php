<?php $this->load->view('layout/up') ?>




<ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
    <?php foreach (@$departments as $key => $dtvalue) { ?>
    <li class="nav-item">
        <a class="nav-link <?php if (@$dtvalue["id"] == "1") { ?>active<?php } ?>" id="pills-<?php echo @$dtvalue["id"]; ?>-tab" data-toggle="pill" href="#pills-<?php echo $dtvalue["id"]; ?>" role="tab" aria-selected="<?php if ($dtvalue["id"] == "1") { ?>true<?php } else { ?>false<?php } ?>"><?php echo $dtvalue["name"]; ?></a>
    </li>
    <?php } ?>

    <li class="nav-item">
        <a class="nav-link <?php if (count(@$departments) == 0) {
                                echo "active";
                            } ?>" id="pills-add-tab" data-toggle="modal" data-target='#addDeviceTypeModal' href="#pills-add" role="tab" aria-selected="false"><strong> + </strong></a>
    </li>
</ul>

<div class="tab-content" id="pills-tabContent">
    <?php foreach (@$departments as $key => $dtvalue) { ?>
    <div class="tab-pane fade <?php if (@$dtvalue["id"] == "1") { ?>show active<?php } ?>" id="pills-<?php echo @$dtvalue["id"]; ?>" role="tabpanel" aria-labelledby="pills-<?php echo $dtvalue["id"]; ?>-tab">

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary"><button type="button" data-toggle='modal' data-target='#addPersonnelModal' class="btn btn-primary btn-sm">Add Personnel</button></h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive" id="datatablel">
                            <link rel="stylesheet" href="">

                            <table id="dataTable" class="table table-striped table-bordered table-hover" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <td>Status</td>
                                        <td>Name</td>
                                        <td>Image</td>
                                        <td>Email</td>
                                        <td>Type</td>
                                        <td>Department</td>
                                        <td>Device</td>
                                        <td>Edit</td>
                                    </tr>
                                </thead>
                                <tbody id="personnel">
                                    <?php
                                        foreach (@$personnel as $key => $value) {

                                            foreach (@$departments as $key => $dvalue) {
                                                if ($dvalue["id"] == $value["department_id"]) {
                                                    $department_name = $dvalue["name"];
                                                }
                                            }

                                            foreach (@$personnel_type as $key => $dvalue) {
                                                if ($dvalue["id"] == $value["type_id"]) {
                                                    $type_name = $dvalue["name"];
                                                }
                                            }
                                            foreach (@$devices as $key => $dvalue) {
                                                if ($dvalue["id"] == $value["device_id"]) {
                                                    $device_name = $dvalue["name"];
                                                }
                                            }

                                            echo "<tr>";
                                            if (@$value["status"] == 1) {
                                                echo "<td>" . "<b style='color:green;'>&#11044;</b>" . "</td>";
                                            } else {
                                                echo "<td>" . "<b style='color:red;'>&#11044;</b>" . "</td>";
                                            }
                                            echo "<td>" . @$value["name"] . "</td>";
                                            echo "<td>" . "<img style='height: 35px;' src='" . "assets/images/personnel/" . @$value["image"] . "'/></td>";
                                            echo "<td>" . @$value["email"] . "</td>";
                                            echo "<td>" . @$type_name . "</td>";
                                            echo "<td>" . @$department_name . "</td>";
                                            echo "<td>" . @$device_name . "</td>";


                                            echo "<td>" . "<button type='button' data-toggle='modal' data-target='#addPersonnelModal' 
                                            data-id='" . @$value["id"] . "' 
                                            data-name='" . @$value["name"] . "' 
                                            data-image='" . @$value["image"] . "' 
                                            data-email='" . @$value["email"] . "' 
                                            data-description='" . @$value["description"] . "' 
                                            data-department_id='" . @$value["department_id"] . "' 
                                            data-type_id='" . @$value["type_id"] . "' 
                                            data-device_id='" . $value["device_id"] . "' 
                                            data-status='" . $value["status"] . "' 
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
        </div>
    </div>
    <?php } ?>
</div>













<div class="modal fade bd-modal-lg" tabindex="-1" role="dialog" id="addPersonnelModal" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modal_title">Add Personnel</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
            <?php echo form_open_multipart('personnel/do_upload');?>
                <!--<form id="form_edit" action="personnel/add" method="post">-->
                    <input type="hidden" id="eid" name="id" value="" />
                    <div class="row">

                        <div class="col-6">
                            <div class="form-group">
                                <label for="image">Personel photo</label>
                                <input type="file" class="form-control-file" id="image" name="userfile">
                            </div>

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
                                <label for="name">Email</label>
                                <input type="text" class="form-control" id="eemail" name="email" placeholder="" value="" required />
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group" id="">
                                <label for="name">Type</label>
                                <select class="form-control" id="etype_id" name="etype_id">
                                    <?php foreach ($personnel_type as $key => $dtvalue) { ?>
                                    <option value="<?php echo $dtvalue["id"]; ?>"><?php echo $dtvalue["name"]; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label for="name">Department</label>
                                <select class="form-control" id="etype_id" name="etype_id">
                                    <?php foreach ($departments as $key => $dtvalue) { ?>
                                    <option value="<?php echo $dtvalue["id"]; ?>"><?php echo $dtvalue["name"]; ?></option>
                                    <?php } ?>
                                </select>
                                
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label for="name">Device</label>
                                <select class="form-control" id="etype_id" name="etype_id">
                                    <?php foreach ($devices as $key => $dtvalue) { ?>
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
                                <button type="submit" class="btn btn-primary" id="btn_personnel_add">Add Personnel</button>

                            </div>
                        </div>
                    </div>
                </form>


                <div class="row" id="delet_personnel" style="display:none;">
                    <div class="col-12">
                        <form action="personnel/delete" method="post">
                            <input type="hidden" id="did" name="id" value="" />
                            <button type="submit" class="btn btn-danger">Delete Personnel</button>

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
            $('#form_edit').attr('action', 'personnel/edit');
            $('#modal_title').html("Edit Personnel");
            $('#btn_personnel_add').html("Edit Personnel");

            $('#eid').val($(this).data('id'));
            $('#ename').val($(this).data('name'));
            $('#edescription').val($(this).data('description'));
            $('#did').val($(this).data('id'));
            $('#cid').val($(this).data('id'));

            $('#delet_personnel').show();
            $('#change_password_section').show();
            $('#password_section').hide();
        });
    });

    $('#btn_add_personnel').on("click", function() {
        $('#modal_title').html("Add Personnel");
        $('#btn_personnel_add').html("Add Personnel");
        $('#form_edit').attr('action', 'personnel/add');

        $('#eid').val("");
        $('#ename').val("");
        $('#email').val("");
        $('#description').val("");

        $('#password_section').show();
        $('#change_password_section').hide();
        $('#eid').val("");
    });

    $(document).ready(function() {
        $('#dataTable').DataTable();
    });
</script>