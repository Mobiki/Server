<div class="col-md-12">
    <div class="card">
        <div class="card-header py-3">
            <?php
            $currentDate =  time();
            date("Y-m-d", $currentDate)
            ?>
            <h6 class="m-0 font-weight-bold text-primary"><?php echo date("d/m/Y", $currentDate); ?></h6>
        </div>
        <div class="card-body">
            <div class="table-responsive" id="datatable">
                <link rel="stylesheet" href="">
                <table id="dataTable" class="table table-striped table-bordered table-hover">
                    <thead>
                        <tr>
                            <td></td>
                            <td>Update Date</td>
                            <td>Name</td>
                            <td>Location</td>
                            <td>Battery</td>
                            <td>Rssi</td>
                            <td>Detail</td>
                        </tr>
                    </thead>
                    <tbody id="rtls">
                        <tr>
                            <td colspan="7">Loading...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script>
    setInterval(function() {
        try {
            $("#rtls").load("widgets/rtls");
        } catch (err) {
            console.log("error");
        }
    }, 10000);
</script>