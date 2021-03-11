<?php $this->load->view('layout/up') ?>
<link href="<?php echo base_url("assets/css/bootstrap-datepicker.min.css"); ?>" rel="stylesheet">

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header py-3">
                <div class="row">
                    <div class="col-2">
                        <h6 class="m-0 font-weight-bold">Select Device</h6>
                    </div>
                    <div class="col-3">
                        <select class="form-control" id="device_id">
                            <?php foreach ($devices as $key => $value) {
                                if ($value["type_id"]!=1) {
                                    echo "<option value=" . $value["id"] . ">" . $value["name"] . "</option>";
                                }
                                
                            } ?>
                        </select>
                    </div>

                </div>
                <br>
                <div class="row">
                    <div class="col-2">
                        <p class="font-weight-bold">Start Date :</p>
                    </div>
                    <div class="col-3"><input id="start" name="start" class="date-pick form-control" value="<?php echo date("d/m/Y"); ?>" data-date-format="dd/mm/yyyy" type="text"></div>
                    <div class="col-2">
                        <p class="font-weight-bold">Finish Date : </p>
                    </div>
                    <div class="col-3"><input id="finish" name="finish" class="date-pick form-control" value="<?php echo date("d/m/Y"); ?>" data-date-format="dd/mm/yyyy" type="text" /></div>
                    <button type="button" class="btn btn-primary" onclick="get_logs()">Get Logs</button>
                    <div class="col-1"><img height="40px" src="https://de.marketscreener.com/images/loading_100.gif" style="display: none;" id="imgProgress1" /></div>
                </div>

            </div>
            <div class="card-body">
                <div class="row">





                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <div style="font-size: small;">
                            <div class="table-responsive">
                                <table id="dataTable" class="table table-striped table-bordered table-hover" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <td>Date Time</td>
                                            <td>Gateway</td>
                                            <td>Status</td>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>









                </div>
            </div>
        </div>
    </div>
</div>



<?php $this->load->view('layout/down') ?>
<script src="<?php echo base_url("assets/js/moment-with-locales.js"); ?>"></script>
<script src="<?php echo base_url("assets/js/bootstrap-datepicker.min.js"); ?>"></script>
<script>
    $('.datepicker').datepicker({
        format: 'dd/mm/yyyy'
    });
    $('input.date-pick').datepicker().on('change', function(ev) {
        var firstDate = $(this).val();
    });

    function get_logs() {
        var startDate = $('#start').val().split('/');
        var startepoch = new Date(startDate[2],startDate[0] - 1,  startDate[1], 00, 00, 00).getTime() / 1000;
        var finishDate = $('#finish').val().split('/');
        var finishepoch = new Date(finishDate[2],finishDate[0] - 1,  finishDate[1], 00, 00, 00).getTime() / 1000;
        

        var personnel_id = $('#personnel_id').val();
        console.log(startepoch);
        console.log(finishepoch);

        $('#dataTable').DataTable().destroy();

        /*$.post( "<?php //echo base_url("logs/get_personal_logs"); ?>",{
                    'sDate': startepoch,
                    'fDate': finishepoch,
                    'personnel_id': personnel_id
                }, function( data ) {
  console.log(data);
});*/
        $('#dataTable').DataTable({
            "processing": true,
            "serverSide": false,
            "ajax": {
                "url": "<?php echo base_url("logs/get_personal_logs"); ?>",
                "type": "POST",
                "data": {
                    'sDate': startepoch,
                    'fDate': finishepoch,
                    'personnel_id': personnel_id
                }
            },
            "columns": [{
                    "data": "date_time"
                },
                {
                    "data": "gateway_name"
                },
                {
                    "data": "status"
                },
            ]
        });

    }
</script>