<div id="sub_asset_type_list">
    <?php
    foreach (@$asset_type as $key => $dvalue) {
        ?>
        <div class="row">
            <div class="col-8">
                <div class="form-group">
                    <input type="text" class="form-control" id="type_name<?php echo $dvalue["id"]; ?>" name="name" placeholder="" value="<?php echo $dvalue["name"]; ?>" required />
                </div>
            </div>

            <div class="col-2">
                <div class="form-group">
                    <button type="button" id="btn_asset_type_edit" data-id="<?php echo $dvalue["id"]; ?>" data-name="<?php echo $dvalue["name"]; ?>" class="btn btn-outline-success btn-sm"><i class="fa fa-check" aria-hidden="true"></i></button>
                </div>
            </div>

            <div class="col-2">
                <div class="form-group">
                    <button type="button" id="btn_asset_type_delete" data-id="<?php echo $dvalue["id"]; ?>" class="btn btn-outline-danger btn-sm"><i class="fa fa-window-close" aria-hidden="true"></i></button>
                </div>
            </div>

        </div>
    <?php
    }
    ?>
</div>
<script>
    $('.btn-outline-danger').each(function() {
        var $this = $(this);
        $this.on("click", function() {
            var id = $(this).data('id');
            $.post("<?php echo base_url('assets/delete_asset_type'); ?>", {
                'id': id
            });

            $("#sub_asset_type_list").load("<?php echo base_url('assets/assete_types_index'); ?>");
            $("#sub_asset_type_list").load("<?php echo base_url('assets/assete_types_index'); ?>");
        });
    });

    $('.btn-outline-success').each(function() {
        var $this = $(this);
        $this.on("click", function() {
            if ($('#type_name' + $(this).data('id')).val() == "") {
                alert("Write a asset type");
            } else {
                var id = $(this).data('id');
                var name = $('#type_name' + $(this).data('id')).val();
                $.post("<?php echo base_url('assets/edit_asset_type'); ?>", {
                    'id': id,
                    'name': name
                });

                $("#sub_asset_type_list").load("<?php echo base_url('assets/assete_types_index'); ?>");
                $("#sub_asset_type_list").load("<?php echo base_url('assets/assete_types_index'); ?>");
            }

        });
    });
</script>