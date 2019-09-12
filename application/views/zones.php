<?php $this->load->view('layout/up') ?>
<link rel="stylesheet" href="<?php echo base_url("assets/css/bootstrap-treeview.min.css"); ?>">

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary"><button id="btn_zone_add" type="button" data-toggle='modal' data-target='#addZoneModal' class="btn btn-primary btn-sm">Add Zone</button></h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12" id="treeview_json">
                    </div>
                    <?php
                    /*foreach (@$zones as $key => $value) {
                    if ($value["parent_id"] == 0) {
                        echo '<div class="row">';
                        echo '<div class="col-2">';
                        echo $value["name"];
                        echo '</div>';
                        echo '<div class="col-1">';
                        echo '<button type="button" id="btn_zone_delete" data-id="" class="btn btn-outline-secondary btn-sm"><i class="fa fa-window-close" aria-hidden="true"></i></button>';
                        echo '</div>';
                        echo '<div class="col-1">';
                        echo '<button type="button" id="btn_zone_delete" data-id="" class="btn btn-outline-danger btn-sm"><i class="fa fa-window-close" aria-hidden="true"></i></button>';
                        echo '</div>';
                        echo '</div>';
                        hierarchical(1,$zones, $value["id"]);
                        echo " <hr>";
                    }
                }

                function hierarchical($a,$zones, $id)
                {
                    foreach ($zones as $key => $value) {
                        if ($value["parent_id"] == $id) {
                            echo '<div class="row">';
                            for ($i = 0; $i < $a; $i++) {
                                echo '&nbsp;-';
                                echo $a;
                            }
                            echo '<div class="col-2">';
                            echo $value["name"];
                            echo '</div>';
                            echo '</div>';
                            $a = $a + 1; 
                            
                            hierarchical($a,$zones, $value["id"]);
                        }
                        
                    }
                }*/
                    ?>
                </div>
            </div>
        </div>
    </div>

</div>


<div class="modal fade bd-modal-lg" id="addZoneModal" tabindex="-1" role="dialog" aria-labelledby="addZoneModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal_title">Add Zone</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body" id="addmodalbody">
                <form id="form_edit" action="<?php echo base_url("zones/add"); ?>" method="post">
                    <input type="hidden" id="eid" name="id" value="" />
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label for="name">Zone name</label>
                                <input type="zonename" class="form-control" id="ename" name="name" placeholder="">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label for="parent_id">Parent Zone</label>
                                <select class="form-control" id="eparent_id" name="parent_id">
                                    <option value="0">Null</option>
                                    <?php foreach ($zones as $key => $zvalue) {  ?>
                                        <option value="<?php echo $zvalue["id"]; ?>"><?php echo $zvalue["name"]; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label for="description">Description</label>
                                <input type="zonename" class="form-control" id="edescription" name="description" placeholder="">
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary" id="btn_zone_edit">Add Zone</button>
                    </div>
                </form>
                <div class="row" id="delet_zone" style="display:none;">
                    <div class="col-12">
                        <form action="<?php echo base_url("zones/delete"); ?>" method="post">
                            <input type="hidden" id="did" name="id" value="" />
                            <button type="submit" class="btn btn-danger">Delete User</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<?php $this->load->view('layout/down') ?>
<script type="text/javascript" charset="utf8" src="<?php echo base_url("assets/js/bootstrap-treeview.min.js"); ?>"></script>

<script type="text/javascript">
    $(document).ready(function() {

        var treeData;
        $.ajax({
            type: "GET",
            url: "<?php echo base_url("zones/treeview"); ?>",
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



    $("#btn_zone_add").click(function() {
        $('#modal_title').html("Add Zone");
        $('#btn_zone_edit').html("Add Zone");

        $('#delet_zone').hide();
        $('#form_edit').attr('action', '<?php echo base_url("zones/add"); ?>');
        $('#eid').val("");
        $('#ename').val("");
        $('#eparent_id').val("");
        $('#edescription').val("");
    });

    function clicked(d) {
        console.log(d);
    }

    function btn_edit(id) {
        $('#modal_title').html("Edit Zone");
        $('#btn_zone_edit').html("Edit Zone");

        $('#delet_zone').show();
        $('#did').val(id);

        //get json
        $.getJSON('<?php echo base_url("zones/get_by_id?id="); ?>' + id, function(data) {
            $('#eid').val(id);
            $('#ename').val(data.name);
            $('#eparent_id').val(data.parent_id);
            $('#edescription').val(data.description);
        });

        $('#form_edit').attr('action', '<?php echo base_url("zones/edit"); ?>');
        console.log(id);
        $('#addZoneModal').modal('show');
    }

</script>