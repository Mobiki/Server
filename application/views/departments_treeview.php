<div class="col-12" id="treeview_json">
</div>

<script src="<?php echo base_url("assets/js/jquery.min.js"); ?>"> </script>
<link rel="stylesheet" href="<?php echo base_url("assets/css/bootstrap.min-3.4.1.css"); ?>">
<link rel="stylesheet" href="<?php echo base_url("assets/css/bootstrap-treeview.min.css"); ?>">
<script type="text/javascript" charset="utf8" src="<?php echo base_url("assets/js/bootstrap-treeview.min.js"); ?>"></script>

<script type="text/javascript">
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