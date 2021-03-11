<?php $this->load->view('layout/up') ?>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary"><button type="button" id="btn_add_personnel" data-toggle='modal' data-target='#addPersonnelModal' class="btn btn-primary btn-sm">Add Personnel</button></h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <div style="font-size: small;">
                            <div class="table-responsive">
                                <table id="dataTable" class="table table-striped table-bordered table-hover" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <td>Status</td>
                                            <td>Name</td>
                                            <td>Image</td>
                                            <td>Email</td>
                                            <td>Type</td>
                                            <td>Department</td>
                                            <td>Zone</td>
                                            <td>Work Shift</td>
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
                                                    break;
                                                } else {
                                                    $department_name = "";
                                                }
                                            }

                                            foreach (@$zones as $key => $zone) {
                                                if ($zone["id"] == $value["zone_id"]) {
                                                    $zone_name = $zone["name"];
                                                    break;
                                                } else {
                                                    $zone_name = "";
                                                }
                                            }

                                            foreach (@$work_shifts as $key => $work_shift) {
                                                if ($work_shift["id"] == $value["work_shift_id"]) {
                                                    $work_shift_name = $work_shift["name"];
                                                    break;
                                                } else {
                                                    $work_shift_name = "";
                                                }
                                            }

                                            foreach (@$personnel_type as $key => $dvalue) {
                                                if ($dvalue["id"] == $value["type_id"]) {
                                                    $type_name = $dvalue["name"];
                                                    break;
                                                } else {
                                                    $type_name = "";
                                                }
                                            }
                                            foreach (@$devices as $key => $dvalue) {
                                                if ($dvalue["id"] == $value["device_id"]) {
                                                    $device_name = $dvalue["name"];
                                                    break;
                                                } else {
                                                    $device_name = "";
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
                                            echo "<td>" . @$zone_name . "</td>";
                                            echo "<td>" . @$work_shift_name . "</td>";
                                            echo "<td>" . @$device_name . "</td>";
                                            echo "<td>" . "<button type='button' data-toggle='modal' data-target='#addPersonnelModal' 
                                            data-id='" . @$value["id"] . "' 
                                            data-name='" . @$value["name"] . "' 
                                            data-image='" . @$value["image"] . "' 
                                            data-email='" . @$value["email"] . "' 
                                            data-description='" . @$value["description"] . "' 
                                            data-zone_id='" . @$value["zone_id"] . "' 
                                            data-department_id='" . @$value["department_id"] . "' 
                                            data-work_shift_id='" . @$value["work_shift_id"] . "' 
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
    </div>
</div>


<div class="modal hide fade" tabindex="-1" role="dialog" id="PersonnelTypesModal" aria-labelledby="myLargeModalLabel" aria-hidden="true" data-focus-on="input:first">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="personnel_types_title">Personnel Types</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="personnel_types_modal_body">
                <form id="add_personnel_type_form" method="post">
                    <div class="row">
                        <div class="col-8">
                            <div class="form-group">
                                <input type="text" class="form-control" id="personnel_type_name" name="name" placeholder="" value="" />
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <button type="button" id="btn_add_type" class="btn btn-outline-primary btn-sm"><i class="fa fa-plus" aria-hidden="true"></i></button>
                            </div>
                        </div>
                    </div>
                </form>
                <div id="personnel_types_list">

                </div>
            </div>
        </div>
    </div>
</div>






<div class="modal fade bd-modal-lg" tabindex="-1" role="dialog" id="addPersonnelModal" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-body">



                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true" id="modal_title">Add Personel</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="profile-tab" data-toggle="tab" href="#person_logs" role="tab" aria-controls="profile" aria-selected="false" onclick="get_logs()">Logs</a>
                    </li>
                </ul>
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">

                        <br><br>


                        <form id="form_edit" action="personnel/add_personnel" enctype="multipart/form-data" method="post" accept-charset="utf-8">
                            <input type="hidden" id="eid" name="id" value="" />
                            <div class="row">
                                <div class="col-3">
                                    <div class="form-group">
                                        <label for="image">Personel photo</label>
                                        <input type="file" class="form-control-file" id="eimage" size="20" name="userfile">
                                    </div>
                                </div>
                                <div class="col-3">
                                    <img id="personnel_photo" class="img-thumbnail" src="<?php echo base_url('assets/images/personnel/default-user-image.png'); ?>" />
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
                                        <input type="text" class="form-control" id="eemail" name="email" placeholder="" value="" />
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group" id="">
                                        <label for="name">Type - <a href="#" data-dismiss="modal" data-toggle="modal" data-target="#PersonnelTypesModal">Add Type</a></label>
                                        <select class="form-control" id="etype_id" name="type_id">
                                            <option value="0">None</option>
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
                                        <select class="form-control" id="edepartment_id" name="department_id">
                                            <option value="0">None</option>
                                            <?php foreach ($departments as $key => $dtvalue) { ?>
                                                <option value="<?php echo $dtvalue["id"]; ?>"><?php echo $dtvalue["name"]; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="name">Device</label>
                                        <select class="form-control" id="edevice_id" name="device_id">
                                            <option value="0">None</option>
                                            <?php foreach ($devices as $key => $dtvalue) { ?>
                                                <option value="<?php echo $dtvalue["id"]; ?>"><?php echo $dtvalue["name"]; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="ezone_id">Zone</label>
                                        <select class="form-control" id="ezone_id" name="zone_id">
                                            <option value="0">None</option>
                                            <?php foreach ($zones as $key => $zone) { ?>
                                                <option value="<?php echo $zone["id"]; ?>"><?php echo $zone["name"]; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="ework_shift_id">Work Shift</label>
                                        <select class="form-control" id="ework_shift_id" name="work_shift_id">
                                            <option value="0">None</option>
                                            <?php foreach ($work_shifts as $key => $work_shift) { ?>
                                                <option value="<?php echo $work_shift["id"]; ?>"><?php echo $work_shift["name"]; ?></option>
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
                                <form action="personnel/delete_personnel" method="post">
                                    <input type="hidden" id="did" name="id" value="" />
                                    <button type="submit" class="btn btn-danger">Delete Personnel</button>
                                </form>
                            </div>
                        </div>



                    </div>
                    <div class="tab-pane fade" id="person_logs" role="tabpanel" aria-labelledby="profile-tab">

                        <table id="person_log" class="table table-striped table-bordered table-hover" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Location</th>
                                    <th>Date</th>
                                    <th>Epoch</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                        </table>
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
            $('#form_edit').attr('action', '<?php echo base_url('personnel/edit_personnel'); ?>');
            $('#modal_title').html("Edit Personnel");
            $('#btn_personnel_add').html("Save");
            $('#eid').val($(this).data('id'));
            $('#ename').val($(this).data('name'));
            $("#personnel_photo").attr("src", "<?php echo base_url('assets/images/personnel/'); ?>" + $(this).data('image'));
            $('#eemail').val($(this).data('email'));
            $('#edepartment_id').val($(this).data('department_id'));
            $('#edevice_id').val($(this).data('device_id'));
            $('#did').val($(this).data('id'));
            $('#cid').val($(this).data('id'));

            $('#ezone_id').val($(this).data('zone_id'));
            $('#work_shift_id').val($(this).data('work_shift_id'));
            $('#delet_personnel').show();

            $("#etype_id").load("personnel/list_personnel_type?id=" + $(this).data('type_id'), function(response, status, xhr) {
                if (status == "error") {
                    var msg = "Sorry but there was an error: ";
                    alert("Error");
                }
            });

            //for reset the model
            $('#profile-tab').show();

            $('#home-tab').addClass('active');
            $('#home-tab').addClass('show');
            $('#profile-tab').removeClass('active');
            $('#profile-tab').removeClass('show');

            $('#home').addClass('active');
            $('#home').addClass('show');
            $('#person_logs').removeClass('active');
            $('#person_logs').removeClass('show');
        });
    });

    $('#btn_add_personnel').on("click", function() {
        $('#modal_title').html("Add Personnel");
        $('#btn_personnel_add').html("Add Personnel");
        $('#form_edit').attr('action', '<?php echo base_url('personnel/add_personnel'); ?>');
        $('#eid').val("");
        $('#ename').val("");
        $('#eemail').val("");
        $('#edepartment_id').val(0);
        $('#edevice_id').val(0);
        $('#eid').val("");
        $('#ezone_id').val(0);
        $('#work_shift_id').val(0);
        $('#personnel_photo').attr("src", "<?php echo base_url("assets/images/personnel/default-user-image.png"); ?>");

        $("#etype_id").load("<?php echo base_url("personnel/list_personnel_type"); ?>");
        $('#etype_id').val(0);

        //for reset the model
        $('#profile-tab').hide();
        $('#home-tab').addClass('active');
        $('#home-tab').addClass('show');
        $('#profile-tab').removeClass('active');
        $('#profile-tab').removeClass('show');

        $('#home').addClass('active');
        $('#home').addClass('show');
        $('#person_logs').removeClass('active');
        $('#person_logs').removeClass('show');
    });

    $(document).ready(function() {
        $('#dataTable').DataTable({
            dom: 'lBfrtip',
            buttons: [{
                text: 'Add Personnel',
                action: function(e, dt, node, config) {
                    open_add_modal();
                }
            }, {
                text: 'Reload',
                action: function(e, dt, node, config) {
                    dt.ajax.reload();
                }
            }],
        });
    });

    $(document).ready(function() {
        $("#personnel_types_list").load("<?php echo base_url("personnel/personnel_types_index"); ?>");
    });

    $('#btn_add_type').on("click", function() {
        if ($('#personnel_type_name').val() == "") {
            alert("Write a personnel type");
        } else {
            var data = $('#add_personnel_type_form').serializeArray();
            $.post("personnel/add_personnel_type", data);
            $('#personnel_type_name').val("");
            $("#personnel_types_list").load("<?php echo base_url("personnel/personnel_types_index"); ?>");
            $("#personnel_types_list").load("<?php echo base_url("personnel/personnel_types_index"); ?>");
        }
    });
    //person_logs

    /*async function f() {
        let promise = new Promise((resolve, reject) => {
            setTimeout(() => resolve("done!"), 1000)
        });
        let result = await promise; // wait till the promise resolves (*)
        alert(result); // "done!"
    }
    f();*/




    ///////////////

    function get_logs() {
        $('#person_log').DataTable({
            destroy: true,
            "processing": true,
            "order": [
                [2, "asc"]
            ],
            "ajax": {
                "url": "<?php echo base_url("personnel/get_logs?person_id="); ?>" + $('#eid').val(),
                dataSrc: ''
            },
            "columns": [{
                    "data": "gateway_name"
                }, {
                    "data": "date_time"
                    /*"render": function(data, type, now) {
                        var timestamp = data;
                        var date = new Date(timestamp * 1000);
                        var year = date.getFullYear();
                        var month = date.getMonth() + 1;
                        var day = date.getDate();
                        var hours = date.getHours();
                        var minutes = date.getMinutes();
                        var seconds = date.getSeconds();
                        return year + "/" + month + "/" + day + " " + hours + ":" + minutes + ":" + seconds;
                    }*/
                },
                {
                    "data": "epoch"
                },
                {
                    "data": "status"
                }
            ],
            "columnDefs": [{
                "targets": [2],
                "visible": false
            }]
        });
    }
</script>