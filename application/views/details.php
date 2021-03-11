<?php $this->load->view('layout/up') ?>


<nav>
    <div class="nav nav-tabs" id="nav-tab" role="tablist">
        <a class="nav-item nav-link active" id="nav-shift-tab" data-toggle="tab" href="#nav-shift" role="tab" aria-controls="nav-shift" aria-selected="true">Work Shifts</a>
        <!--<a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#nav-profile" role="tab" aria-controls="nav-profile" aria-selected="false">Profile</a>
    <a class="nav-item nav-link" id="nav-contact-tab" data-toggle="tab" href="#nav-contact" role="tab" aria-controls="nav-contact" aria-selected="false">Contact</a>-->
    </div>
</nav>
<div class="tab-content" id="nav-tabContent">
    <div class="tab-pane fade show active" id="nav-shift" role="tabpanel" aria-labelledby="nav-shift-tab">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive" id="datatable">
                            <link rel="stylesheet" href="">
                            <table id="work_shift_dataTable" class="table table-striped table-bordered table-hover" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <td>id</td>
                                        <td>Name</td>
                                        <td>Start Time</td>
                                        <td>Finish Time</td>
                                        <td>Edit</td>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>


    </div>
    <!--div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">...</div>
  <div class="tab-pane fade" id="nav-contact" role="tabpanel" aria-labelledby="nav-contact-tab">...</div>-->
</div>


<div class="modal fade bd-example-modal-lg" id="add_work_shift" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal_title">Add Work Shift</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body" id="addmodalbody">
                <form id="form_edit" action="<?php echo base_url("company/add_work_shift"); ?>" method="post">
                    <input type="hidden" id="eid" name="id" value="" />
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label for="ename">Name</label>
                                <input type="text" class="form-control" id="ename" name="name" placeholder="" value="" required />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label for="estart_time">Start Tiime</label>
                                <input type="time" class="form-control" id="estart_time" name="start_time" placeholder="" value="" required />
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label for="efinish_time">Finish Time</label>
                                <input type="time" class="form-control" id="efinish_time" name="finish_time" placeholder="" value="" required />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group" style="text-align: right;">
                                <button type="submit" class="btn btn-primary" id="btn_work_shift_add">Add Work Shift</button>

                            </div>
                        </div>
                    </div>
                    <hr>
                </form>
                <div class="row" id="delet_work_shift" style="display:none;">
                    <div class="col-12">
                        <form action="<?php echo base_url("company/delete_work_shift"); ?>" method="post">
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
<script type="text/javascript" src="https://cdn.datatables.net/w/dt/dt-1.10.18/b-1.5.6/datatables.min.js"></script>

<script>
    $(document).ready(function() {
        $('#work_shift_dataTable').each(function() {
            var $this = $(this);
            $this.DataTable({
                dom: 'lBfrtip',
                buttons: [{
                    text: 'Add Work Shift',
                    action: function(e, dt, node, config) {
                        open_add_modal();
                    }
                }, {
                    text: 'Reload',
                    action: function(e, dt, node, config) {
                        dt.ajax.reload();
                    }
                }],
                "processing": true,
                "serverSide": true,
                "order": [],
                "ajax": {
                    url: "<?php echo base_url("company/get_work_shifts"); ?>",
                    type: "POST"
                },
                "columnDefs": [{
                        "targets": [0],
                        "orderable": false,
                        "searchable": true,
                    },
                    {
                        "targets": [0],
                        "visible": false
                    },
                ],
            });
        });
    });

    function open_add_modal() {
        $('#add_work_shift').modal('toggle');
        $('#form_edit').attr('action', '<?php echo base_url('company/add_work_shift'); ?>');
        $('#modal_title').html("Add Work Shift");
        $('#btn_work_shift_add').html("Add Work Shift");
        $('#did').val(0);
        $('#ename').val("");
        $('#estart_time').val();
        $('#efinish_time').val();
    }

    function edit(id, name, start_time, finish_time) {
        $('#form_edit').attr('action', '<?php echo base_url('company/edit_work_shift'); ?>');
        $('#modal_title').html("Edit Work Shift");
        $('#btn_work_shift_add').html("Save");
        $('#delet_work_shift').show();
        $('#eid').val(id);
        $('#did').val(id);
        $('#ename').val(name);
        $('#estart_time').val(start_time);
        $('#efinish_time').val(finish_time);
    }
</script>