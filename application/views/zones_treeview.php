<link rel="stylesheet" href="<?php echo base_url("assets/css/bootstrap.min.css"); ?>">
<link rel="stylesheet" href="<?php echo base_url("assets/css/bootstrap-treeview.min.css"); ?>">
<link rel="stylesheet" href="https://blackrockdigital.github.io/startbootstrap-sb-admin-2/vendor/fontawesome-free/css/all.min.css">



<div class="modal fade bd-modal-lg" id="editZoneModal" tabindex="-1" role="dialog" aria-labelledby="addZoneModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalLabel">Add Zone</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body" id="addmodalbody">

            </div>
        </div>
    </div>
</div>

<div class="col-12" id="treeview_json">
</div>

<script src="<?php echo base_url("assets/js/jquery.min.js"); ?>"> </script>
<script src="<?php echo base_url("assets/js/bootstrap.bundle.min.js"); ?>"> </script>
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


    function clicked(d) {
        console.log(d);
        //$('#editZoneModal').modal('show');
    }





</script>