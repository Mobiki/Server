<?php $this->load->view('layout/up') ?>
<link rel="stylesheet" href="<?php echo base_url("assets/css/bootstrap-treeview.min.css"); ?>">

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary"><button type="button" id="btn_add_department" data-toggle='modal' data-target='#addDepartmentModal' class="btn btn-primary btn-sm">Add Department</button></h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12" id="treeview_json">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="modal fade bd-modal-lg" tabindex="-1" role="dialog" id="addDepartmentModal" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modal_title">Add Department</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="form_edit" action="departments/add" enctype="multipart/form-data" method="post" accept-charset="utf-8">
                    <input type="hidden" id="eid" name="id" value="" />
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label for="name">Name</label>
                                <input type="text" class="form-control" id="ename" name="name" placeholder="" value="" required />
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label for="parent_id">Parent department</label>
                                <select class="form-control" id="eparent_id" name="parent_id">
                                    <option value="0">None</option>
                                    <?php foreach (@$departments as $key => $dvalue) { ?>
                                        <option value="<?php echo $dvalue["id"]; ?>"><?php echo $dvalue["name"]; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label for="expiry_date">Expiry Date</label>
                                <input type="date" class="form-control" id="eexpiry_date" name="expiry_date" placeholder="" value="" />
                            </div>
                        </div>
                    </div>

                    <hr>
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group" style="text-align: right;">
                                <button type="submit" class="btn btn-primary" id="btn_department_add">Add Department</button>

                            </div>
                        </div>
                    </div>
                </form>

                <div class="row" id="delet_department" style="display:none;">
                    <div class="col-12">
                        <form action="departments/delete" method="post">
                            <input type="hidden" id="did" name="id" value="" />
                            <button type="submit" class="btn btn-danger">Delete Department</button>

                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>



<?php $this->load->view('layout/down') ?>
<script type="text/javascript" charset="utf8" src="<?php echo base_url("assets/js/bootstrap-treeview.min.js"); ?>"></script>



<script>
    $('#btn_add_department').on("click", function() {
        $('#modal_title').html("Add Department");
        $('#btn_department_add').html("Add Department");
        $('#form_edit').attr('action', '<?php echo base_url("departments/add"); ?>');
        $('#eid').val("");
        $('#ename').val("");
        $('#eparent_id').val(0);
        $('#eexpiry_date').val("");
        $('#eid').val("");
        $('#cid').val("");
        $('#delet_department').hide();
    });


    function btn_edit(id) {
        $('#modal_title').html("Edit Departments");
        $('#btn_department_add').html("Edit Departments");

        $('#delet_department').show();
        $('#did').val(id);

        //get json
        $.getJSON('<?php echo base_url("departments/get_by_id?id="); ?>' + id, function(data) {
            $('#eid').val(id);
            $('#ename').val(data.name);
            $('#eparent_id').val(data.parent_id);
            $('#eexpiry_date').val(data.expiry_date);
        });

        $('#form_edit').attr('action', '<?php echo base_url("departments/edit"); ?>');
        console.log(id);
        $('#addDepartmentModal').modal('show');
    }

    $(document).ready(function() {

        var treeData;

        $.ajax({
            type: "GET",
            url: "<?php echo base_url("departments/treeview"); ?>",
            dataType: "json",
            success: function(response) {
                initTree(response)
            }
        });

        function initTree(treeData) {
            $('#treeview_json').treeview({
                data: treeData
            });
        }

    });
</script>


<?php
/*foreach (@$departments as $key => $value) {

    if ($value["parent_id"] == 0) {
        echo $value["name"] . " || <br>";
        h($departments, $value["name"], $value["id"], $value["parent_id"]);
        echo " <br>";
    }
}

function h($departments, $name, $id, $pid)
{
    $a = 0;
    foreach ($departments as $key => $value) {
        $a = $a + 1;
        $z = "";
        if ($value["parent_id"] == $id) {


            for ($i = 0; $i < $a; $i++) {
                $z = $z . "-";
            }
            echo $z . $value["name"] . " __ <br>";
            h($departments, $value["name"], $value["id"], $value["parent_id"]);
        }
    }
}*/
?>