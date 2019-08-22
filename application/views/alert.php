<?php $this->load->view('layout/up')


?>

<link href="<?php echo base_url("assets/css/bootstrap-datepicker.min.css"); ?>" rel="stylesheet">


<!-- Nav tabs -->
<ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
    <li class="nav-item">
        <a class="nav-link active" id="pills-setting-tab" data-toggle="pill" href="#pills-setting" role="tab" aria-controls="pills-setting" aria-selected="true">Alert Rules</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" id="pills-logs-tab" data-toggle="pill" href="#pills-logs" role="tab" aria-controls="pills-logs" aria-selected="false">Alert Logs</a>
    </li>
</ul>
<div class="tab-content" id="pills-tabContent">
    <div class="tab-pane fade show active" id="pills-setting" role="tabpanel" aria-labelledby="pills-setting-tab">

       
    <div class="card">
                <div class="card-header py-3">
                    
                    <h6 class="m-0 font-weight-bold text-primary"> <button type="button" data-toggle='modal' data-target='#addAlertModal' class="btn btn-primary btn-sm">Add Alert Rule </button> - <button type="button" class="btn btn-secondary btn-sm" onclick="sendtoredis()">Update</button> </h6>
                </div>
                <div class="card-body">

    <div style="font-size: small;">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-hover">
                                <thead style="text-align: center;">
                                    <tr > 
                                        <th style='padding: 7px;'>Name</th>
                                        <th style='padding: 7px;'>Device</th>
                                        <th style='padding: 7px;'>Sensor Type</th>
                                        <th style='padding: 7px;'>Sensor Value</th>
                                        <th style='padding: 7px;'>Equation</th>
                                        <th style='padding: 7px;'>Edit</th>
                                    </tr>
                                </thead>
                                <tbody id="alert_rules">

                                <?php foreach ($alert_rules as $key => $value) {
                                    //`id``name``device_id``device_type``sensor_value``equation`?>
<tr>
                                    <td><?php echo $value["name"]; ?></td>
                                    <td>
                                        <?php foreach ($devices as $key => $dvalue) {
                                            if ($dvalue["id"]==$value["device_id"]) {
                                                echo $dvalue["name"]; 
                                            }
                                        }
                                        
                                        if ($value["device_id"]==0) {
                                            echo "All"; 
                                        }?>
                                </td>
                                    <td>
                                        
                                    <?php 
                                    foreach ($devices_type as $key => $dtvalue) {
                                        if ($dtvalue["id"]==$value["device_type"]) {
                                            echo $dtvalue["name"]; 
                                        }
                                    }
                                    ?>
                                
                                </td>
                                    <td><?php echo $value["sensor_value"]; ?></td>
                                    <td>
                                        <?php
                                        if($value["equation"]==0){
                                            echo "=";
                                        }
                                        if($value["equation"]==1){
                                            echo "<";
                                        }
                                        if($value["equation"]==2){
                                            echo ">";
                                        }
                                         ?>
                                
                                </td>
                                    <td><button type="button" class="btn btn-success btn-sm">Edit</button></td>
</tr>
                                <?php } ?>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
    </div>


    </div>
    <div class="tab-pane fade" id="pills-logs" role="tabpanel" aria-labelledby="pills-logs-tab">

        <br>
        <div class="col">
            <div class="row">

                <div class="col-2">
                    <p class="font-weight-bold">Cihaz : </p>
                </div>
                <div class="col-3">
                    <select class="browser-default custom-select" id="sensors">
                        <?php foreach ($devices as $key => $value) {?>
                        <option value="<?php echo $value["mac"]; ?>"><?php echo $value["name"]; ?></option>
                        <?php }?>
                    </select>
                </div>
            </div><br>
            <div class="row">

                <div class="col-2">
                    <p class="font-weight-bold">Başlangıç Tarihi : </p>
                </div>
                <div class="col-3"><input id="start" name="start" class="date-pick form-control" value="<?php echo date("m/d/Y"); ?>" data-date-format="mm/dd/yyyy" type="text"></div>
                <div class="col-2">
                    <p class="font-weight-bold">Bitiş Tarihi : </p>
                </div>
                <div class="col-3"><input id="finish" name="finish" class="date-pick form-control" value="<?php echo date("m/d/Y"); ?>" data-date-format="mm/dd/yyyy" type="text" /></div>
                <div class="col-1"><button id="gerReport" class="btn btn-success">Raporla</button></div>
                <div class="col-1"><img height="40px" src="https://de.marketscreener.com/images/loading_100.gif" style="display: none;" id="imgProgress1" /></div>

            </div>
        </div>
        <hr>
        <div class="col">
            <div class="row" id="report">


            </div>
        </div>
    </div>

</div>






<div class="modal fade bd-example-modal-lg" id="addAlertModal" tabindex="-1" role="dialog" aria-labelledby="addAlertModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalLabel">Add Alert</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body" id="addmodalbody">
                <form action="alert/add" method="post">
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">

                                <label for="name">Alert Name</label>
                                <input type="gatewaysn" class="form-control" id="name" name="name" placeholder="">
                            </div>
                        </div>
                        <div class="col-6">
                        <div class="form-group">
                                <label for="zone_id">Zone</label>
                                <select class="form-control" id="ezone_id" name="ezone_id">
                                    <?php foreach ($devices as $key => $dvalue) {  ?>
                                        <option value="<?php echo $dvalue["id"]; ?>"><?php echo $dvalue["name"]; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    </div>



                    <hr>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Add Alert Rule</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>













<div id="alert"></div>
<?php $this->load->view('layout/down') ?>

<script src="<?php echo base_url("assets/js/moment-with-locales.js"); ?>"></script>
<script src="<?php echo base_url("assets/js/bootstrap-datepicker.min.js"); ?>"></script>

<script type="text/javascript">
    $('.datepicker').datepicker({
        format: 'mm/dd/yyyy'
    });

    $('#gerReport').on('click', function(event) {
        $('#report').empty();
        var startDate = $('#start').val().split('/');
        var startepoch = new Date(startDate[2], startDate[0] - 1, startDate[1], 00, 00, 00).getTime() / 1000;
        var finishDate = $('#finish').val().split('/');
        var finishepoch = new Date(finishDate[2], finishDate[0] - 1, finishDate[1], 23, 59, 59).getTime() / 1000;
        var sensorsmac = $('#sensors').val();

        //alert(startepoch + '  ' + finishepoch+' '+sensorsmac);
        if (sensorsmac != null) {
            $("#imgProgress1").show();
            $("#report").load("alert/log?sensor=" + sensorsmac + "&sDate=" + startepoch + "&fDate=" + finishepoch, function() {
                $("#imgProgress1").hide();
                console.log("İlk yükleme 2");
            });
        } else {
            alert('Select device');
        }
    });


    $('input.date-pick').datepicker().on('change', function(ev) {
        var firstDate = $(this).val();
    });

    // Call the dataTables jQuery plugin
    $(document).ready(function() {
        $('#dataTable').DataTable();
    });

   function sendtoredis(){
    $("#alert").load("alert/toredis");
    }
</script>