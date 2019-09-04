<div id="sub_personnel_type_list">
    <?php
    foreach (@$personnel_type as $key => $dvalue) {
        ?>
        <div class="row">
            <div class="col-8">
                <div class="form-group">
                    <input type="text" class="form-control" id="type_name<?php echo $dvalue["id"]; ?>" name="name" placeholder="" value="<?php echo $dvalue["name"]; ?>" required />
                </div>
            </div>

            <div class="col-2">
                <div class="form-group">
                    <button type="button" id="btn_personnel_type_edit" data-id="<?php echo $dvalue["id"]; ?>" data-name="<?php echo $dvalue["name"]; ?>" class="btn btn-outline-success btn-sm"><i class="fa fa-check" aria-hidden="true"></i></button>
                </div>
            </div>

            <div class="col-2">
                <div class="form-group">
                    <button type="button" id="btn_personnel_type_delete" data-id="<?php echo $dvalue["id"]; ?>" class="btn btn-outline-danger btn-sm"><i class="fa fa-window-close" aria-hidden="true"></i></button>
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
            $.post("personnel/delete_personnel_type", {
                'id': id
            });

            $("#sub_personnel_type_list").load("personnel/personnel_types_index");
            $("#sub_personnel_type_list").load("personnel/personnel_types_index");
        });
    });

    $('.btn-outline-success').each(function() {
        var $this = $(this);
        $this.on("click", function() {
            if ($('#type_name' + $(this).data('id')).val() == "") {
                alert("Write a personnel type");
            } else {
                var id = $(this).data('id');
                var name = $('#type_name' + $(this).data('id')).val();
                $.post("personnel/edit_personnel_type", {
                    'id': id,
                    'name': name
                });

                $("#sub_personnel_type_list").load("personnel/personnel_types_index");
                $("#sub_personnel_type_list").load("personnel/personnel_types_index");
            }

        });
    });
</script>